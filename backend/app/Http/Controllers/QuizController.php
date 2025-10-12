<?php

namespace App\Http\Controllers;

use App\Models\UserWord;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuizController extends Controller
{
    /**
     * Generate quiz questions based on mode
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'mode' => 'required|string|in:quiz,images,listening,writing',
            'count' => 'integer|min:1|max:50',
        ]);

        $sessionId = $request->input('session_id');
        $mode = $request->input('mode');
        $count = $request->input('count', 10);

        // Get user's words prioritizing those due for review
        $userWords = UserWord::bySession($sessionId)
            ->with('word')
            ->get();

        if ($userWords->isEmpty()) {
            return response()->json([
                'error' => 'No words saved for learning. Please add some words first.'
            ], 404);
        }

        // Prioritize words due for review
        $dueWords = $userWords->filter(function ($uw) {
            return $uw->next_review_date && $uw->next_review_date <= now();
        });

        $wordsToQuiz = $dueWords->isEmpty() 
            ? $userWords->random(min($count, $userWords->count()))
            : $dueWords->random(min($count, $dueWords->count()));

        $questions = match($mode) {
            'quiz' => $this->generateMultipleChoiceQuestions($wordsToQuiz),
            'images' => $this->generateImageMatchingQuestions($wordsToQuiz),
            'listening' => $this->generateListeningQuestions($wordsToQuiz),
            'writing' => $this->generateWritingQuestions($wordsToQuiz),
            default => [],
        };

        return response()->json([
            'mode' => $mode,
            'questions' => $questions,
        ]);
    }

    private function generateMultipleChoiceQuestions($userWords): array
    {
        $questions = [];

        foreach ($userWords as $userWord) {
            $correctWord = $userWord->word;
            
            // Get random wrong answers from the same level
            $wrongAnswers = Word::where('level', $correctWord->level)
                ->where('id', '!=', $correctWord->id)
                ->inRandomOrder()
                ->limit(3)
                ->get();

            $options = $wrongAnswers->pluck('word')->toArray();
            $options[] = $correctWord->word;
            shuffle($options);

            $questions[] = [
                'id' => $userWord->id,
                'type' => 'definition_to_word',
                'question' => $correctWord->definition,
                'options' => $options,
                'correct_answer' => $correctWord->word,
                'phonetic' => $correctWord->phonetic,
            ];
        }

        return $questions;
    }

    private function generateImageMatchingQuestions($userWords): array
    {
        $questions = [];

        foreach ($userWords as $userWord) {
            $correctWord = $userWord->word;
            
            // Get random words with images
            $otherWords = Word::where('level', $correctWord->level)
                ->where('id', '!=', $correctWord->id)
                ->whereNotNull('image_url')
                ->inRandomOrder()
                ->limit(5)
                ->get();

            $images = $otherWords->map(function ($w) {
                return ['url' => $w->image_url, 'word' => $w->word];
            })->toArray();

            $images[] = ['url' => $correctWord->image_url, 'word' => $correctWord->word];
            shuffle($images);

            $questions[] = [
                'id' => $userWord->id,
                'type' => 'word_to_image',
                'word' => $correctWord->word,
                'images' => $images,
                'correct_answer' => $correctWord->image_url,
            ];
        }

        return $questions;
    }

    private function generateListeningQuestions($userWords): array
    {
        $questions = [];

        foreach ($userWords as $userWord) {
            $word = $userWord->word;
            
            $questions[] = [
                'id' => $userWord->id,
                'type' => 'listening',
                'word' => $word->word,
                'audio_url' => $word->audio_url,
                'phonetic' => $word->phonetic,
                'correct_answer' => $word->word,
            ];
        }

        return $questions;
    }

    private function generateWritingQuestions($userWords): array
    {
        $questions = [];

        foreach ($userWords as $userWord) {
            $word = $userWord->word;
            
            $questions[] = [
                'id' => $userWord->id,
                'type' => 'writing',
                'definition' => $word->definition,
                'image_url' => $word->image_url,
                'example_sentence' => $word->example_sentence,
                'correct_answer' => $word->word,
                'hint' => substr($word->word, 0, 1) . str_repeat('_', strlen($word->word) - 1),
            ];
        }

        return $questions;
    }

    /**
     * Submit quiz answers and update spaced repetition
     */
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.user_word_id' => 'required|integer',
            'answers.*.correct' => 'required|boolean',
            'answers.*.time_spent' => 'integer|min:0',
        ]);

        $results = [];
        $totalCorrect = 0;

        foreach ($request->input('answers') as $answer) {
            $userWord = UserWord::find($answer['user_word_id']);
            
            if (!$userWord) {
                continue;
            }

            // Update using spaced repetition service
            $spacedRepetitionService = app(\App\Services\SpacedRepetitionService::class);
            $quality = $spacedRepetitionService->performanceToQuality(
                $answer['correct'],
                $answer['time_spent'] ?? 0,
                1
            );
            
            $userWord = $spacedRepetitionService->updateReviewSchedule($userWord, $quality);
            $userWord->save();

            if ($answer['correct']) {
                $totalCorrect++;
            }

            $results[] = [
                'user_word_id' => $userWord->id,
                'next_review' => $userWord->next_review_date,
                'status' => $userWord->status,
            ];
        }

        return response()->json([
            'total' => count($request->input('answers')),
            'correct' => $totalCorrect,
            'accuracy' => count($request->input('answers')) > 0 
                ? round(($totalCorrect / count($request->input('answers'))) * 100, 2) 
                : 0,
            'results' => $results,
        ]);
    }
}

