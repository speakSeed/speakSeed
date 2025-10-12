<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserWord;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuizController extends Controller
{
    /**
     * Generate quiz questions
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'mode' => 'required|in:definition,word,sentence',
            'count' => 'integer|min:1|max:20',
        ]);
        
        $sessionId = $request->input('session_id');
        $mode = $request->input('mode');
        $count = $request->input('count', 10);
        
        // Get user's learning words
        $userWords = UserWord::with('word')
            ->where('session_id', $sessionId)
            ->inRandomOrder()
            ->limit($count)
            ->get();
        
        if ($userWords->isEmpty()) {
            return response()->json([
                'error' => 'No words in learning list'
            ], 404);
        }
        
        $questions = [];
        
        foreach ($userWords as $userWord) {
            $word = $userWord->word;
            
            // Get random wrong answers
            $wrongAnswers = Word::where('id', '!=', $word->id)
                ->where('level', $word->level)
                ->inRandomOrder()
                ->limit(3)
                ->get();
            
            $question = $this->generateQuestion($word, $wrongAnswers, $mode);
            $question['user_word_id'] = $userWord->id;
            
            $questions[] = $question;
        }
        
        return response()->json(['questions' => $questions]);
    }
    
    /**
     * Generate a single question
     */
    private function generateQuestion(Word $correctWord, $wrongAnswers, string $mode): array
    {
        $options = collect([$correctWord])->merge($wrongAnswers)->shuffle();
        
        $question = [
            'id' => $correctWord->id,
            'correct_answer' => $correctWord->word,
        ];
        
        switch ($mode) {
            case 'definition':
                $question['question'] = $correctWord->definition;
                $question['type'] = 'Definition to Word';
                $question['options'] = $options->pluck('word')->values()->all();
                break;
                
            case 'word':
                $question['question'] = $correctWord->word;
                $question['type'] = 'Word to Definition';
                $question['options'] = $options->map(fn($w) => [
                    'word' => $w->word,
                    'definition' => $w->definition,
                ])->values()->all();
                $question['correct_answer'] = $correctWord->definition;
                break;
                
            case 'sentence':
                $sentence = $correctWord->example_sentence ?? $correctWord->definition;
                $question['question'] = str_replace(
                    $correctWord->word,
                    '______',
                    $sentence
                );
                $question['type'] = 'Complete the Sentence';
                $question['options'] = $options->pluck('word')->values()->all();
                break;
        }
        
        return $question;
    }
    
    /**
     * Submit quiz answers
     */
    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string',
            'answers' => 'required|array',
            'answers.*.user_word_id' => 'required|exists:user_words,id',
            'answers.*.correct' => 'required|boolean',
        ]);
        
        $sessionId = $request->input('session_id');
        $answers = $request->input('answers');
        
        $results = [
            'total' => count($answers),
            'correct' => 0,
            'incorrect' => 0,
        ];
        
        foreach ($answers as $answer) {
            if ($answer['correct']) {
                $results['correct']++;
            } else {
                $results['incorrect']++;
            }
        }
        
        $results['percentage'] = round(($results['correct'] / $results['total']) * 100, 2);
        
        return response()->json($results);
    }
}
