<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Word;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        $words = [
            // A1 Level (Elementary)
            ['word' => 'hello', 'level' => 'A1', 'definition' => 'A greeting', 'example_sentence' => 'Hello, how are you?', 'image_url' => 'https://via.placeholder.com/400x300?text=hello'],
            ['word' => 'apple', 'level' => 'A1', 'definition' => 'A round fruit with red or green skin', 'example_sentence' => 'I eat an apple every day.', 'image_url' => 'https://via.placeholder.com/400x300?text=apple'],
            ['word' => 'book', 'level' => 'A1', 'definition' => 'A written work published in printed or electronic form', 'example_sentence' => 'I am reading a good book.', 'image_url' => 'https://via.placeholder.com/400x300?text=book'],
            ['word' => 'cat', 'level' => 'A1', 'definition' => 'A small domesticated carnivorous mammal', 'example_sentence' => 'My cat is very playful.', 'image_url' => 'https://via.placeholder.com/400x300?text=cat'],
            ['word' => 'dog', 'level' => 'A1', 'definition' => 'A domesticated carnivorous mammal', 'example_sentence' => 'The dog is barking loudly.', 'image_url' => 'https://via.placeholder.com/400x300?text=dog'],
            
            // A2 Level (Pre-Intermediate)
            ['word' => 'adventure', 'level' => 'A2', 'definition' => 'An exciting or unusual experience', 'example_sentence' => 'We had an amazing adventure in the mountains.', 'image_url' => 'https://via.placeholder.com/400x300?text=adventure'],
            ['word' => 'beautiful', 'level' => 'A2', 'definition' => 'Pleasing to the senses or mind', 'example_sentence' => 'The sunset is beautiful tonight.', 'image_url' => 'https://via.placeholder.com/400x300?text=beautiful'],
            ['word' => 'computer', 'level' => 'A2', 'definition' => 'An electronic device for processing data', 'example_sentence' => 'I use my computer every day for work.', 'image_url' => 'https://via.placeholder.com/400x300?text=computer'],
            ['word' => 'delicious', 'level' => 'A2', 'definition' => 'Highly pleasant to taste', 'example_sentence' => 'This cake is absolutely delicious.', 'image_url' => 'https://via.placeholder.com/400x300?text=delicious'],
            ['word' => 'environment', 'level' => 'A2', 'definition' => 'The surroundings or conditions in which someone lives', 'example_sentence' => 'We should protect our environment.', 'image_url' => 'https://via.placeholder.com/400x300?text=environment'],
            
            // B1 Level (Intermediate)
            ['word' => 'accomplish', 'level' => 'B1', 'definition' => 'To successfully complete or achieve something', 'example_sentence' => 'She worked hard to accomplish her goals.', 'image_url' => 'https://via.placeholder.com/400x300?text=accomplish'],
            ['word' => 'beneficial', 'level' => 'B1', 'definition' => 'Producing good or helpful results', 'example_sentence' => 'Exercise is beneficial for your health.', 'image_url' => 'https://via.placeholder.com/400x300?text=beneficial'],
            ['word' => 'characteristic', 'level' => 'B1', 'definition' => 'A typical quality or feature', 'example_sentence' => 'Patience is a characteristic of good teachers.', 'image_url' => 'https://via.placeholder.com/400x300?text=characteristic'],
            ['word' => 'demonstrate', 'level' => 'B1', 'definition' => 'To show or make evident', 'example_sentence' => 'Let me demonstrate how to use this device.', 'image_url' => 'https://via.placeholder.com/400x300?text=demonstrate'],
            ['word' => 'enthusiastic', 'level' => 'B1', 'definition' => 'Having or showing intense enjoyment or interest', 'example_sentence' => 'She was enthusiastic about the new project.', 'image_url' => 'https://via.placeholder.com/400x300?text=enthusiastic'],
            
            // B2 Level (Upper-Intermediate)
            ['word' => 'ambiguous', 'level' => 'B2', 'definition' => 'Open to more than one interpretation', 'example_sentence' => 'The statement was deliberately ambiguous.', 'image_url' => 'https://via.placeholder.com/400x300?text=ambiguous'],
            ['word' => 'benevolent', 'level' => 'B2', 'definition' => 'Well-meaning and kindly', 'example_sentence' => 'He was known for his benevolent nature.', 'image_url' => 'https://via.placeholder.com/400x300?text=benevolent'],
            ['word' => 'controversial', 'level' => 'B2', 'definition' => 'Giving rise to public disagreement', 'example_sentence' => 'The policy was highly controversial.', 'image_url' => 'https://via.placeholder.com/400x300?text=controversial'],
            ['word' => 'diligent', 'level' => 'B2', 'definition' => 'Having or showing care in one\'s work', 'example_sentence' => 'She is a diligent student who always completes her assignments.', 'image_url' => 'https://via.placeholder.com/400x300?text=diligent'],
            ['word' => 'elaborate', 'level' => 'B2', 'definition' => 'Involving many careful details', 'example_sentence' => 'They created an elaborate plan for the event.', 'image_url' => 'https://via.placeholder.com/400x300?text=elaborate'],
            
            // C1 Level (Advanced)
            ['word' => 'articulate', 'level' => 'C1', 'definition' => 'Express fluently and coherently', 'example_sentence' => 'She was able to articulate her thoughts clearly.', 'image_url' => 'https://via.placeholder.com/400x300?text=articulate'],
            ['word' => 'bewildering', 'level' => 'C1', 'definition' => 'Confusing or perplexing', 'example_sentence' => 'The situation was utterly bewildering.', 'image_url' => 'https://via.placeholder.com/400x300?text=bewildering'],
            ['word' => 'contemplate', 'level' => 'C1', 'definition' => 'Think deeply about something', 'example_sentence' => 'He sat quietly to contemplate his next move.', 'image_url' => 'https://via.placeholder.com/400x300?text=contemplate'],
            ['word' => 'discernible', 'level' => 'C1', 'definition' => 'Able to be perceived or recognized', 'example_sentence' => 'There was a discernible change in his attitude.', 'image_url' => 'https://via.placeholder.com/400x300?text=discernible'],
            ['word' => 'exacerbate', 'level' => 'C1', 'definition' => 'Make a problem worse', 'example_sentence' => 'The delay only served to exacerbate the situation.', 'image_url' => 'https://via.placeholder.com/400x300?text=exacerbate'],
            
            // C2 Level (Proficient)
            ['word' => 'anachronistic', 'level' => 'C2', 'definition' => 'Belonging to a period other than that being portrayed', 'example_sentence' => 'The use of modern slang in the historical drama felt anachronistic.', 'image_url' => 'https://via.placeholder.com/400x300?text=anachronistic'],
            ['word' => 'byzantine', 'level' => 'C2', 'definition' => 'Excessively complicated', 'example_sentence' => 'The tax code was byzantine in its complexity.', 'image_url' => 'https://via.placeholder.com/400x300?text=byzantine'],
            ['word' => 'circumlocution', 'level' => 'C2', 'definition' => 'Using many words where fewer would do', 'example_sentence' => 'His circumlocution made his point unclear.', 'image_url' => 'https://via.placeholder.com/400x300?text=circumlocution'],
            ['word' => 'deleterious', 'level' => 'C2', 'definition' => 'Causing harm or damage', 'example_sentence' => 'The policy had deleterious effects on the economy.', 'image_url' => 'https://via.placeholder.com/400x300?text=deleterious'],
            ['word' => 'egregious', 'level' => 'C2', 'definition' => 'Outstandingly bad', 'example_sentence' => 'It was an egregious error that cost the company millions.', 'image_url' => 'https://via.placeholder.com/400x300?text=egregious'],
        ];
        
        foreach ($words as $word) {
            Word::create($word);
        }
    }
}
