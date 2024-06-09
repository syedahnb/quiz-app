<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Answer;

class QuestionAnswerSeeder extends Seeder
{
    public function run()
    {
        $questions = [
            [
                'question_text' => 'What does PHP stand for?',
                'answers' => [
                    ['answer_text' => 'Personal Home Page', 'is_correct' => false],
                    ['answer_text' => 'PHP: Hypertext Preprocessor', 'is_correct' => true],
                    ['answer_text' => 'Private Home Page', 'is_correct' => false],
                    ['answer_text' => 'Public Hypertext Processor', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => 'Which function is used to include a file in PHP?',
                'answers' => [
                    ['answer_text' => 'include()', 'is_correct' => true],
                    ['answer_text' => 'require()', 'is_correct' => false],
                    ['answer_text' => 'fetch()', 'is_correct' => false],
                    ['answer_text' => 'insert()', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => 'Which of the following is the correct way to declare a PHP variable?',
                'answers' => [
                    ['answer_text' => '$variableName', 'is_correct' => true],
                    ['answer_text' => 'var variableName', 'is_correct' => false],
                    ['answer_text' => 'variable variableName', 'is_correct' => false],
                    ['answer_text' => '@variableName', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => 'How do you create a function in PHP?',
                'answers' => [
                    ['answer_text' => 'functionName()', 'is_correct' => false],
                    ['answer_text' => 'create function functionName()', 'is_correct' => false],
                    ['answer_text' => 'function functionName()', 'is_correct' => true],
                    ['answer_text' => 'new function functionName()', 'is_correct' => false],
                ],
            ],
            [
                'question_text' => 'Which superglobal variable is used to get data from a form that uses the GET method?',
                'answers' => [
                    ['answer_text' => '$_POST', 'is_correct' => false],
                    ['answer_text' => '$_GET', 'is_correct' => true],
                    ['answer_text' => '$_SESSION', 'is_correct' => false],
                    ['answer_text' => '$_COOKIE', 'is_correct' => false],
                ],
            ],
        ];

        foreach ($questions as $questionData) {
            $question = Question::create(['question_text' => $questionData['question_text']]);
            foreach ($questionData['answers'] as $answerData) {
                $question->answers()->create($answerData);
            }
        }
    }
}
