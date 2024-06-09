<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class QuizController extends Controller
{

    public function show(Request $request)
    {
        $answeredQuestions = Session::get('answered_questions', []);
        $skippedQuestions = Session::get('skipped_questions', []);


        $mergedQuestions = array_unique(array_merge($answeredQuestions, $skippedQuestions));

        $remainingQuestions = Question::with('answers')
            ->whereNotIn('id', $mergedQuestions)
            ->get();

        if ($remainingQuestions->isEmpty()) {
            return response()->json(['redirect' => route('quiz.result')]);
        }

        $question = $remainingQuestions->random();

        return response()->json([
            'question' => $question,
            'answers' => $question->answers,
        ]);
    }


    public function storeName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Session::put('user_name', $request->name); // Store user name in session
        Session::put('answered_questions', []); // Initialize answered questions
        Session::put('answers', []); // Initialize answers

        return response()->json(['success' => true]);
    }

    public function submit(Request $request)
    {


        $skippedId = $request->input('skipped');
        $answerId = $request->input('answer');
        $questionId = $request->input('question_id');

        if ($answerId) {
            $answeredQuestions = Session::get('answered_questions', []);
            $answeredQuestions[] = $questionId;
            Session::put('answered_questions', $answeredQuestions);

            $answers = Session::get('answers', []);
            $answers[$questionId] = $answerId;
            Session::put('answers', $answers);

        } elseif ($skippedId) {
            // Check if the question is already skipped to prevent duplicates
            $skippedQuestions = Session::get('skipped_questions', []);

            if (!in_array($questionId, $skippedQuestions)) {
                $skippedQuestions[] = $questionId;
                Session::put('skipped_questions', $skippedQuestions);

                Log::info($questionId);
            }
        }

        return response()->json(['success' => true]);
    }

    public function result()
    {
        $quiz = Question::with('answers')->get();
        $answers = Session::get('answers', []);
        $answeredQuestions = Session::get('answered_questions', []);
        $skippedQuestions = Session::get('skipped_questions', []);
        Log::info("answer:", ['answers' => $answers]);
        Log::info("answeredQuestions:", ['answeredQuestions' => $answeredQuestions]);
        Log::info("answeredQuestions:", ['skippedQuestion' => $skippedQuestions]);


        $correctCount = 0;
        $wrongCount = 0;

        foreach ($quiz as $question) {
            if (isset($answers[$question->id])) {
                $answerId = $answers[$question->id];
                $answer = $question->answers->where('id', $answerId)->first();

                if ($answer) {
                    if ($answer->is_correct) {
                        $correctCount++;
                    } else {
                        $wrongCount++;
                    }
                }
            }
        }

        // Calculate skipped count
        $skippedCount = count($skippedQuestions);

        // Retrieve user name from session
        $name = Session::get('user_name');

        // Clear session data
        Session::forget('user_name');
        Session::forget('answered_questions');
        Session::forget('answers');
        Session::forget('skipped_questions');

        // Pass data to the view
        return view('quiz.result', compact('correctCount', 'wrongCount', 'skippedCount', 'name'));
    }

    public function getUserName()
    {
        // Check if the user name is set in the session
        $valid = Session::has('user_name');

        // If the user name is set, return it
        if ($valid) {
            $userName = Session::get('user_name');
            return response()->json(['valid' => true, 'user_name' => $userName]);
        }

        // If the user name is not set, return that it's not valid
        return response()->json(['valid' => false]);
    }
}
