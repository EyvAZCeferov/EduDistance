<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\ExamResult;
use App\Models\ExamResultAnswer;
use App\Models\Exercise;
use App\Models\ExerciseQuestion;
use App\Models\ExerciseResult;
use App\Models\RequestForm;
use App\Models\Section;
use App\Models\UsedCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CommonController extends Controller
{

    public function exam ($exam_id) {
        $exam = Exam::findOrFail($exam_id);
        $sections = Section::where('exam_id', $exam->id)->orderBy('created_at')->get();
        if ($exam->questionCount() === 0) {
            return redirect()->route('user.index');
        }

        return view('frontend.pages.exam.index', compact('exam', 'exam_id', 'sections'));
    }

    public function examFinish (Request $request, $exam_id) {

        if (!$request->answers || count($request->answers) === 0) {
            return redirect()->route('user.index');
        }
        DB::transaction(function() use($request,$exam_id){
            $exam = Exam::findOrFail($exam_id);

            $result = new ExamResult ();
            $result->user_id = auth('users')->user()->id;
            $result->exam_id = $exam->id;
            $result->point = $exam->point;
            $result->save();

            foreach ($request->answers as $section_id => $answers) {
                foreach ($answers as $question_id => $answer) {
                    $section = $exam->sections->find($section_id);
                    $question = $section->questions->find($question_id);

                    if ($question && $section) {
                        if ($question->type === 1) {
                            $resultAnswer = new ExamResultAnswer ();
                            $resultAnswer->result_id = $result->id;
                            $resultAnswer->section_id = $section_id;
                            $resultAnswer->question_id = $question->id;
                            $resultAnswer->answer_id = $answer;
                            $resultAnswer->result = $answer == $question->correctAnswer()?->id ? 1 : 0;
                            $resultAnswer->save();
                        } else if($question->type === 2) {
                            $answer = array_map('intval', $answer);
                            $user_answer =serialize($answer);
                            $correct_answer =serialize($question->correctAnswer()?->pluck('id')->toArray());

                            $resultAnswer = new ExamResultAnswer ();
                            $resultAnswer->result_id = $result->id;
                            $resultAnswer->section_id = $section_id;
                            $resultAnswer->question_id = $question->id;
                            $resultAnswer->answers = $answer;
                            $resultAnswer->result = $user_answer == $correct_answer ? 1 : 0;
                            $resultAnswer->save();
                        } else {
                            $resultAnswer = new ExamResultAnswer ();
                            $resultAnswer->result_id = $result->id;
                            $resultAnswer->section_id = $section_id;
                            $resultAnswer->question_id = $question->id;
                            $resultAnswer->value = $answer;
                            $resultAnswer->result = strip_tags_with_whitespace($answer) == strip_tags_with_whitespace($question->correctAnswer()?->answer) ? 1 : 0;
                            $resultAnswer->save();
                        }

                    }
                }
            }
        });

        return redirect()->route('user.exam.results');
    }

    public function examResults () {
        $results = ExamResult::where('user_id', auth('users')->user()->id)
            ->orderByDesc('id')
            ->get();

        return view('frontend.pages.exam.results', compact('results'));
    }

    public function examResult ($result_id) {
        $result = ExamResult::where('user_id', auth('users')->user()->id)
            ->with('answers.answer')
            ->orderByDesc('id')
            ->findOrFail($result_id);

        return view('frontend.pages.exam.result', compact('result'));
    }

    public function notfound(){
        return view("frontend.pages.notfound");
    }
}
