<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Word;
use App\Services\DictionaryApiService;
use App\Services\ImageApiService;

class WordSeeder extends Seeder
{
    /**
     * Expanded word lists organized by CEFR levels (100+ words per level for unlimited learning)
     */
    private array $wordsByLevel = [
        'A1' => [
            // Basic words (100 words)
            'hello', 'goodbye', 'thank', 'please', 'yes', 'no', 'water', 'food', 'house', 'family',
            'friend', 'book', 'pen', 'table', 'chair', 'door', 'window', 'cat', 'dog', 'happy',
            'sad', 'big', 'small', 'hot', 'cold', 'good', 'bad', 'new', 'old', 'young',
            'man', 'woman', 'child', 'boy', 'girl', 'baby', 'mother', 'father', 'sister', 'brother',
            'eat', 'drink', 'sleep', 'walk', 'run', 'sit', 'stand', 'talk', 'listen', 'look',
            'read', 'write', 'play', 'work', 'go', 'come', 'see', 'hear', 'say', 'tell',
            'day', 'night', 'morning', 'evening', 'today', 'tomorrow', 'yesterday', 'week', 'month', 'year',
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'time', 'hour', 'minute',
            'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten',
            'car', 'bus', 'train', 'bike', 'plane', 'boat', 'street', 'road', 'city', 'country',
        ],
        'A2' => [
            // Elementary words (100 words)
            'weather', 'travel', 'hotel', 'restaurant', 'shopping', 'money', 'work', 'student', 'teacher', 'classroom',
            'beautiful', 'expensive', 'cheap', 'dangerous', 'comfortable', 'hospital', 'doctor', 'medicine', 'problem', 'question',
            'answer', 'school', 'university', 'learn', 'study', 'test', 'exam', 'grade', 'subject', 'language',
            'English', 'speak', 'understand', 'practice', 'improve', 'difficult', 'easy', 'interesting', 'boring', 'important',
            'person', 'people', 'everyone', 'someone', 'anyone', 'nobody', 'something', 'anything', 'nothing', 'everything',
            'place', 'here', 'there', 'where', 'when', 'why', 'how', 'what', 'which', 'who',
            'buy', 'sell', 'pay', 'cost', 'price', 'store', 'shop', 'market', 'customer', 'service',
            'breakfast', 'lunch', 'dinner', 'meal', 'hungry', 'thirsty', 'taste', 'delicious', 'cook', 'recipe',
            'health', 'healthy', 'sick', 'pain', 'hurt', 'feel', 'better', 'worse', 'cure', 'treatment',
            'holiday', 'vacation', 'trip', 'tourist', 'visit', 'enjoy', 'relax', 'fun', 'activity', 'entertainment',
        ],
        'B1' => [
            // Intermediate words (100 words)
            'environment', 'pollution', 'recycle', 'climate', 'technology', 'internet', 'social', 'culture', 'tradition', 'custom',
            'opinion', 'argument', 'discussion', 'experience', 'knowledge', 'skill', 'ability', 'opportunity', 'challenge', 'achievement',
            'information', 'communication', 'relationship', 'friendship', 'community', 'society', 'government', 'politics', 'economy', 'business',
            'industry', 'company', 'employee', 'manager', 'career', 'profession', 'salary', 'income', 'budget', 'investment',
            'education', 'research', 'science', 'experiment', 'theory', 'discovery', 'invention', 'innovation', 'development', 'progress',
            'nature', 'natural', 'resource', 'energy', 'power', 'electricity', 'fuel', 'conservation', 'sustainable', 'renewable',
            'situation', 'condition', 'circumstance', 'factor', 'element', 'aspect', 'feature', 'characteristic', 'quality', 'quantity',
            'advantage', 'disadvantage', 'benefit', 'drawback', 'strength', 'weakness', 'positive', 'negative', 'increase', 'decrease',
            'compare', 'contrast', 'similar', 'different', 'various', 'several', 'numerous', 'particular', 'specific', 'general',
            'necessary', 'essential', 'important', 'significant', 'crucial', 'vital', 'fundamental', 'basic', 'complex', 'complicated',
        ],
        'B2' => [
            // Upper-intermediate words (100 words)
            'sophisticated', 'contemporary', 'inevitable', 'phenomenon', 'hypothesis', 'analyze', 'synthesize', 'evaluate', 'perspective', 'paradigm',
            'sustainability', 'innovation', 'entrepreneurship', 'globalization', 'demographic', 'infrastructure', 'legislation', 'implementation', 'assessment', 'criteria',
            'controversy', 'consensus', 'diversity', 'integration', 'discrimination', 'inequality', 'justice', 'equity', 'ethical', 'moral',
            'strategy', 'objective', 'methodology', 'framework', 'initiative', 'priority', 'alternative', 'option', 'solution', 'approach',
            'consequence', 'implication', 'significance', 'relevance', 'context', 'circumstance', 'complexity', 'ambiguity', 'uncertainty', 'probability',
            'insight', 'interpretation', 'perception', 'conception', 'assumption', 'inference', 'deduction', 'conclusion', 'reasoning', 'logic',
            'enhance', 'optimize', 'maximize', 'minimize', 'facilitate', 'promote', 'sustain', 'maintain', 'preserve', 'conserve',
            'impact', 'influence', 'affect', 'contribute', 'determine', 'establish', 'demonstrate', 'illustrate', 'exemplify', 'clarify',
            'transition', 'transformation', 'evolution', 'revolution', 'adaptation', 'modification', 'adjustment', 'revision', 'reform', 'restructuring',
            'accumulate', 'aggregate', 'compile', 'consolidate', 'integrate', 'coordinate', 'collaborate', 'cooperate', 'negotiate', 'mediate',
        ],
        'C1' => [
            // Advanced words (100 words)
            'ambiguous', 'arbitrary', 'coherent', 'compelling', 'comprehensive', 'consequently', 'implicit', 'inherent', 'intrinsic', 'pragmatic',
            'predominantly', 'prerequisite', 'presumably', 'rigorous', 'subjective', 'substantial', 'subtle', 'underlying', 'unprecedented', 'versatile',
            'albeit', 'notwithstanding', 'nevertheless', 'nonetheless', 'moreover', 'furthermore', 'henceforth', 'whereby', 'wherein', 'whereas',
            'fluctuate', 'oscillate', 'deviate', 'diverge', 'converge', 'correlate', 'differentiate', 'discriminate', 'distinguish', 'characterize',
            'substantiate', 'validate', 'verify', 'authenticate', 'corroborate', 'refute', 'contradict', 'dispute', 'challenge', 'contest',
            'encompass', 'incorporate', 'embody', 'manifest', 'exemplify', 'epitomize', 'symbolize', 'signify', 'denote', 'connote',
            'perpetuate', 'propagate', 'disseminate', 'proliferate', 'permeate', 'pervade', 'saturate', 'pervasive', 'ubiquitous', 'omnipresent',
            'mitigate', 'alleviate', 'ameliorate', 'exacerbate', 'aggravate', 'intensify', 'amplify', 'diminish', 'curtail', 'impede',
            'allocate', 'apportion', 'distribute', 'dispense', 'administer', 'implement', 'execute', 'enforce', 'regulate', 'mandate',
            'ascertain', 'discern', 'deduce', 'infer', 'construe', 'interpret', 'elucidate', 'expound', 'articulate', 'enunciate',
        ],
        'C2' => [
            // Proficiency words (100 words)
            'ubiquitous', 'esoteric', 'ephemeral', 'idiosyncrasy', 'juxtaposition', 'paradigmatic', 'quintessential', 'serendipity', 'vicissitude', 'zeitgeist',
            'obfuscate', 'propensity', 'recalcitrant', 'salubrious', 'temerity', 'verisimilitude', 'acquiesce', 'ameliorate', 'corroborate', 'extrapolate',
            'anachronism', 'antithesis', 'aphorism', 'axiom', 'dichotomy', 'dogma', 'enigma', 'euphemism', 'fallacy', 'hegemony',
            'iconoclast', 'imperative', 'incorrigible', 'inexorable', 'inextricable', 'insidious', 'intractable', 'inalienable', 'immutable', 'impeccable',
            'magnanimous', 'meticulous', 'nefarious', 'ostentatious', 'parsimonious', 'pernicious', 'perspicacious', 'platitude', 'plethora', 'precarious',
            'proclivity', 'prodigious', 'profligate', 'quandary', 'rancorous', 'recondite', 'redolent', 'reprehensible', 'sagacious', 'sanguine',
            'scrupulous', 'spurious', 'superfluous', 'surreptitious', 'sycophantic', 'taciturn', 'tenacious', 'trepidation', 'truculent', 'unctuous',
            'variegated', 'vehement', 'venerable', 'verbose', 'vicarious', 'vigilant', 'vindictive', 'vitriolic', 'vociferous', 'voluminous',
            'acquiescence', 'admonition', 'adulation', 'affectation', 'alacrity', 'approbation', 'asperity', 'blandishment', 'bombast', 'cajole',
            'calumny', 'capitulate', 'castigate', 'chicanery', 'churlish', 'circumlocution', 'clandestine', 'cogent', 'complicity', 'concatenate',
        ],
    ];

    /**
     * Get word list for a specific level (for on-demand fetching)
     */
    public function getWordsForLevel(string $level): array
    {
        return $this->wordsByLevel[$level] ?? [];
    }

    /**
     * Run the database seeds.
     */
    public function run(DictionaryApiService $dictionaryService, ImageApiService $imageService): void
    {
        $this->command->info('Starting word seeding with Free Dictionary API...');
        $this->command->info('Note: First run fetches from API, subsequent runs use 7-day cache.');

        foreach ($this->wordsByLevel as $level => $words) {
            $this->command->info("\nProcessing level {$level}...");
            
            foreach ($words as $index => $wordText) {
                // Check if word already exists
                if (Word::where('word', $wordText)->exists()) {
                    $this->command->info("✓ Skipping existing: {$wordText}");
                    continue;
                }

                $this->command->info("→ Fetching: {$wordText}");

                // Fetch word data from API (with 7-day caching)
                $wordData = $dictionaryService->fetchWordData($wordText);

                if (!$wordData) {
                    $this->command->warn("✗ Could not fetch: {$wordText}");
                    continue;
                }

                // Fetch image
                $imageUrl = $imageService->fetchImage($wordText);

                // Create word using difficulty from API
                Word::create([
                    'word' => $wordText,
                    'level' => $level,
                    'difficulty' => $wordData['difficulty'] ?? 3,
                    'definition' => $wordData['definition'] ?? "Definition for {$wordText}",
                    'phonetic' => $wordData['phonetic'],
                    'audio_url' => $wordData['audio_url'],
                    'image_url' => $imageUrl,
                    'example_sentence' => $wordData['example_sentence'],
                    'meanings' => $wordData['meanings'],
                    'synonyms' => $wordData['synonyms'],
                ]);

                $this->command->info("✓ Created: {$wordText} (difficulty: {$wordData['difficulty']})");

                // Rate limiting - wait a bit to avoid hammering the APIs
                usleep(200000); // 0.2 seconds (faster due to caching)
            }
        }

        $totalWords = Word::count();
        $this->command->info("\n🎉 Word seeding completed! Total words: {$totalWords}");
    }
}

