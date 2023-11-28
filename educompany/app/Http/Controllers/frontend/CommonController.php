<?php

namespace App\Http\Controllers\frontend;

use App\Models\Exam;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Section;
use App\Models\Exercise;
use App\Models\ExamResult;
use App\Models\UsedCoupon;
use App\Models\RequestForm;
use Illuminate\Support\Str;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use App\Models\ExamStartPage;
use App\Models\ExerciseResult;
use App\Models\ExamResultAnswer;
use App\Models\ExerciseQuestion;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CommonController extends Controller
{

    public function exam($exam_id)
    {
        $exam = Exam::findOrFail($exam_id);
        $sections = Section::where('exam_id', $exam->id)->orderBy('created_at')->get();
        if ($exam->questionCount() === 0) {
            return redirect()->route('user.index');
        }

        return view('frontend.pages.exam.index', compact('exam', 'exam_id', 'sections'));
    }

    public function examFinish(Request $request, $exam_id)
    {

        if (!$request->answers || count($request->answers) === 0) {
            return redirect()->route('user.index');
        }
        DB::transaction(function () use ($request, $exam_id) {
            $exam = Exam::findOrFail($exam_id);

            $result = new ExamResult();
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
                            $resultAnswer = new ExamResultAnswer();
                            $resultAnswer->result_id = $result->id;
                            $resultAnswer->section_id = $section_id;
                            $resultAnswer->question_id = $question->id;
                            $resultAnswer->answer_id = $answer;
                            $resultAnswer->result = $answer == $question->correctAnswer()?->id ? 1 : 0;
                            $resultAnswer->save();
                        } else if ($question->type === 2) {
                            $answer = array_map('intval', $answer);
                            $user_answer = serialize($answer);
                            $correct_answer = serialize($question->correctAnswer()?->pluck('id')->toArray());

                            $resultAnswer = new ExamResultAnswer();
                            $resultAnswer->result_id = $result->id;
                            $resultAnswer->section_id = $section_id;
                            $resultAnswer->question_id = $question->id;
                            $resultAnswer->answers = $answer;
                            $resultAnswer->result = $user_answer == $correct_answer ? 1 : 0;
                            $resultAnswer->save();
                        } else {
                            $resultAnswer = new ExamResultAnswer();
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

    public function examResults()
    {
        $results = ExamResult::where('user_id', auth('users')->user()->id)
            ->orderByDesc('id')
            ->get();

        return view('frontend.pages.exam.results', compact('results'));
    }

    public function examResult($result_id)
    {
        $result = ExamResult::where('user_id', auth('users')->user()->id)
            ->with('answers.answer')
            ->orderByDesc('id')
            ->findOrFail($result_id);

        return view('frontend.pages.exam.result', compact('result'));
    }

    public function notfound()
    {
        return view("frontend.pages.notfound");
    }
    public function redirect_exam(Request $request)
    {
        try {
            if (Auth::guard('users')->check()) {
                $exam = Exam::where('id', $request->exam_id)
                    ->with(['sections'])
                    ->first();
                $exam_start_pages = collect();
                if (!empty($exam)) {
                    $exam_result = ExamResult::where("exam_id", $request->exam_id)
                        ->where('user_id', Auth::guard('users')->id())
                        ->whereNull("point")
                        ->first();
                    if (empty($exam_result)) {
                        if (!empty($exam->start_page_id)) {
                            $exam_start_pages = ExamStartPage::where('id', $exam->start_page_id)->first();
                            if (empty($exam_start_pages)) {
                                $exam_start_pages = exam_start_page();
                            } else {
                                if ($exam_start_pages->default == false) {
                                    $default = exam_start_page();
                                    $exam_start_pages = collect([$exam_start_pages, $default]);
                                    $exam_start_pages = $exam_start_pages->sortBy('order_number')->values();
                                }
                            }
                        } else {
                            $exam_start_pages = exam_start_page();
                        }

                        if (empty($exam_start_pages)) {
                            return view("frontend.exams.exam_main_process.index", compact("exam")); // imtahan
                        } else {
                            return view("frontend.exams.exam_main_process.start_page", compact("exam", 'exam_start_pages'));
                        }
                    } else {
                        return view("frontend.exams.exam_main_process.index", compact('exam')); // imtahan
                    }
                } else {
                    return $this->notfound();
                }
            } else {
                return redirect(route('login'))->with('error', trans("additional.headers.login"));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        } finally {
            dbdeactive();
        }
    }
    public function set_exam(Request $request)
    {
        try {
            $exam_result = ExamResult::where("exam_id", $request->exam_id)
                ->where('user_id', Auth::guard('users')->id())
                ->whereNull("point")
                ->first();
            $exam = Exam::where("id", $request->exam_id)
                ->with(['sections'])
                ->first();
            if (empty($exam_result)) {
                $exam_price = 0;
                if ($exam->price > 0) {
                    if ($exam->endirim_price != null && $exam->endirim_price != $exam->price) {
                        $exam_price = $exam->endirim_price;
                    } else {
                        $exam_price = $exam->price;
                    }
                }
                $exam_result = new ExamResult();
                $exam_result->user_id = Auth::guard('users')->id();
                $exam_result->exam_id = $request->exam_id;
                $exam_result->payed = $exam_price == 0 ? true : false;
                $exam_result->save();
            }

            if ($exam_result->payed == true) {
                return view("frontend.exams.exam_main_process.index", compact('exam'));
            } else {
                $payment_dat = [
                    'exam_id' => $request->exam_id,
                    'exam_name' => $exam->name[app()->getLocale() . '_name'],
                    'exam_image' => getImageUrl($exam->image, 'exams'),
                    'exam_result_id' => $exam_result->id,
                    'user_id' => Auth::guard('users')->id(),
                    'token' => Str::uuid(),
                    'price' => $exam->price,
                    'endirim_price' => $exam->endirim_price,
                    'amount' => $exam_price,
                    'coupon_id' => 'coupon_id',
                    'coupon_code' => 'coupon_code',
                    'coupon_value' => 'coupon_code',
                    'coupon_type' => 'coupon_code',
                ];
                $apiscontroller = new ApisController();
                $response = $apiscontroller->create_payment($payment_dat);
                dd($response);
            }


        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        } finally {
            dbdeactive();
        }
    }
}
