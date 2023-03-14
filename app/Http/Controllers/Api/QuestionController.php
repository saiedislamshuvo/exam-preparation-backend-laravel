<?php

namespace App\Http\Controllers\Api;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mocktest_id' => 'required',
            'questions' => 'required',
        ]);

        try {
            foreach ($request->questions as $value) {
                $question = new Question();
                $question->mocktest_id = $request->mocktest_id;
                $question->question = $value['question'];
                $question->explanation = $value['explanation'] ?? '';
                $question->save();

                foreach ($value['options'] ?? [] as $op) {
                    $option = new QuestionOption();
                    $option->question_id = $question->id;
                    $option->option = $op['option'] ?? '';
                    $option->correct = $op['correct'] ?? false;
                    $option->save();
                }
            }

            return response()->json(['success' => true], 201);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage(),
            ], 400);
        }
    }

    public function show($id)
    {
        $questions = Question::with('options')->where('mocktest_id', '=', $id)->get();
        return response()->json($questions);
    }

    public function update(Request $request)
    {
        try {
            $questions_id = [];
            foreach ($request->questions as $question) {
                array_push($questions_id, $question['id']);
            }

            $questions_data = Question::find($questions_id);

            foreach ($questions_data as $question) {
                foreach ($request->questions as $qu) {
                    if ($question->id == $qu['id']) {
                        $question->question = $qu['question'];
                        $question->update();
                        break;
                    }
                }
            }

            $options_id = [];
            foreach ($request->options as $option) {
                array_push($options_id, $option['id']);
            }

            $options_data = QuestionOption::find($options_id);

            foreach ($options_data as $option) {
                foreach ($request->options as $op) {
                    if ($option->id == $op['id']) {
                        $option->option = $op['option'];
                        $option->update();
                        break;
                    }
                }
            }

            foreach ($request->selected_options as $qid => $option) {
                $temp_options = QuestionOption::where('question_id', '=', $qid)->get();
                $temp_keys = array_keys($option);
                if (sizeof($temp_keys) > 0) {
                    $optionid = $temp_keys[0];
                    foreach ($temp_options as $key => $op) {
                        $op->correct = 0;
                        if ($op->id == $optionid) {
                            $op->correct = 1;
                        }
                        $op->update();
                    }
                }
            }

            $explanation_questions_id = [];
            foreach ($request->explanations as $question) {
                array_push($explanation_questions_id, $question['id']);
            }

            $questions_explanations = Question::find($explanation_questions_id);
            foreach ($questions_explanations as $question) {
                foreach ($request->explanations as $ex) {
                    if ($question->id == $ex['id']) {
                        $question->explanation = $ex['explanation'];
                        $question->update();
                        break;
                    }
                }
            }

            return response()->json(['success' => true, 'data' => $request->all()]);
        } catch (\Exception $ex) {
            return response()->json(['success' => false]);
        }
    }

    public function destroy($id)
    {
        Question::find($id)->delete();
        return response()->json(['success' => true]);
    }
}
