<?php

namespace App\Http\Controllers\Api;

use App\Models\Mocktest;
use App\Models\Result;
use App\Models\ResultItem;
use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function store(Request $request)
    {
        $mocktest = Mocktest::find($request->mocktest_id);
        if ($mocktest ?? false) {
            $result = new Result();
            $result->user_id = $request->user()->id;
            $result->mocktest_id = $request->mocktest_id;
            $result->total_questions = sizeof($mocktest->questions);
            $result->total_answered = sizeof($request->answer_paper);
            $result->save();

            $correct_answered = 0;
            foreach ($request->answer_paper as $answer) {
                ResultItem::create([
                    'result_id' => $result->id,
                    'question_id' => $answer->question_id,
                    'question_option_id' => $answer->option_id,
                ]);
                $question = Question::find($answer->question_id);
                foreach ($question->options as $question_option) {
                    if ($question_option->id == $answer->option_id) {
                        if ($question_option->correct ?? false) {
                            $correct_answered++;
                        }
                    }
                }
            }
            $result->correct_answered = $correct_answered;
            $result->update();
        }
        return response()->json(['success' => false]);
    }
}
