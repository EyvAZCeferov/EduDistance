<?php

namespace App\Http\Controllers\frontend;

use App\Models\Exam;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Section;
use App\Models\ExamResult;
use App\Models\CouponCodes;
use Illuminate\Support\Str;
use App\Models\ExamQuestion;
use Illuminate\Http\Request;
use App\Models\ExamResultAnswer;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ExamReferences;
use App\Models\ExamStartPageIds;

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

    public function examFinish(Request $request)
    {
        try {
            if (!$request->answers || count($request->answers) === 0) {
                return response()->json(['status' => 'eror', 'message' => trans("additional.messages.answersnotfound", [], $request->language ?? 'az')]);
            }
            $result = collect();
            DB::transaction(function () use ($request, &$result) {
                $exam = Exam::findOrFail($request->exam_id);
                $result = ExamResult::where("id", $request->exam_result_id)->first();
                $result->update([
                    'time_reply' => $request->time_exam,
                ]);
                foreach ($request->answers as $section_id => $answers) {
                    foreach ($answers as $question_id => $answer) {
                        $section = $exam->sections->find($section_id);
                        $question = ExamQuestion::where("id", $question_id)->first();
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
                            } else if ($question->type == 3) {
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
                if ($exam->time_range_sections > 0) {
                } else {
                    $point = $exam->point * $result->correctAnswers();
                    app()->setLocale(session()->get('changedLang'));
                    session()->put('language', session()->get('changedLang'));
                    session()->put('lang', session()->get('changedLang'));
                    $result->update([
                        'point' => $point
                    ]);
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => trans("additional.messages.exam_finished", [], $request->language ?? 'az'),
                'url' => route("user.exam.resultpage", $result->id)
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        } finally {
            dbdeactive();
        }
    }

    public function examResults()
    {
        $results = ExamResult::where('user_id', auth('users')->user()->id)
            ->orderByDesc('id')
            ->get();

        return view('frontend.pages.exam.results', compact('results'));
    }

    public function examResultPage($result_id)
    {
        $exam_result = ExamResult::where('user_id', auth('users')->user()->id)
            ->with('answers.answer')
            ->orderByDesc('id')
            ->findOrFail($result_id);

        return view('frontend.exams.resultpage', compact('exam_result'));
    }

    public function examResult($result_id)
    {
        $exam_result = ExamResult::where('user_id', auth('users')->user()->id)
            ->with('answers.answer')
            ->orderByDesc('id')
            ->findOrFail($result_id);

        return view('frontend.exams.result', compact('exam_result'));
    }
    public function notfound()
    {
        return view("frontend.pages.notfound");
    }
    public function redirect_exam(Request $request)
    {
        try {
            if (Auth::guard('users')->check()) {
                $exam = Exam::where("id", $request->exam_id)
                    ->with(['sections', 'references'])
                    ->first();
                $exam_start_pages = collect();
                session()->put('selected_section', $request->selected_section ?? 0);
                session()->put('changedLang', app()->getLocale() ?? 'az');
                if ($exam->layout_type == "sat") {
                    app()->setLocale('en');
                    session()->put('language', 'en');
                    session()->put('lang', 'en');
                }
                if (!empty($exam)) {
                    $exam_result = ExamResult::where("exam_id", $request->exam_id)
                        ->where('user_id', Auth::guard('users')->id())
                        ->whereNull("point")
                        ->first();
                    if (empty($exam_result)) {
                        if (!empty($exam->start_pages)) {
                            $default = exam_start_page();
                            foreach ($exam->start_pages as $page) {
                                if (!empty($page->start_page)) {
                                    $exam_start_pages->push($page->start_page);
                                }
                            }
                            // $exam_start_pages = collect([$exam_start_pages, $default]);
                            $exam_start_pages->push($default);
                            $exam_start_pages = $exam_start_pages->sortBy('order_number')->values();
                        } else {
                            $exam_start_pages = exam_start_page();
                        }

                        if (empty($exam_start_pages)) {
                            return view("frontend.exams.exam_main_process.index", compact("exam", 'exam_result')); // imtahan
                        } else {
                            return view("frontend.exams.exam_main_process.start_page", compact("exam", 'exam_start_pages'));
                        }
                    } else {
                        return view("frontend.exams.exam_main_process.index", compact('exam', 'exam_result')); // imtahan
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
                ->with(['sections', 'references'])
                ->first();
            $coupon_code = collect();
            if (isset($request->coupon_code) && !empty($request->coupon_code)) {
                $coupon_code = CouponCodes::where("code", $request->coupon_code)->first();
            }
            $exam_price = 0;
            if (empty($exam_result)) {
                $exam_price = 0;
                if ($exam->price > 0) {
                    if ($exam->endirim_price != null && $exam->endirim_price != $exam->price) {
                        $exam_price = $exam->endirim_price;
                    } else {
                        $exam_price = $exam->price;
                    }

                    if (!empty($coupon_code) && isset($coupon_code->discount) && $coupon_code->discount > 0 && $exam_price > 0) {
                        if ($coupon_code->type == "percent") {
                            $exam_price -= $exam_price * $coupon_code->discount / 100;
                        } else {
                            if ($coupon_code->discount > $exam_price) {
                                $exam_price = 0;
                            } else {
                                $exam_price -= $coupon_code->discount;
                            }
                        }
                    }
                }
                $exam_result = new ExamResult();
                $exam_result->user_id = Auth::guard('users')->id();
                $exam_result->exam_id = $request->exam_id;
                $exam_result->payed = $exam_price == 0 ? true : false;
                $exam_result->save();

                $this->payment_start($request, $exam, $exam_result, $coupon_code, $exam_price);
            }

            if ($exam_result->payed == true) {
                // return view("frontend.exams.exam_main_process.index", compact('exam','exam_result'));
                return $this->redirect_exam($request);
            } else {
                $this->payment_start($request, $exam, $exam_result, $coupon_code, $exam_price);
                // return view("frontend.exams.exam_main_process.index", compact('exam','exam_result'));
                return $this->redirect_exam($request);
            }
        } catch (\Exception $e) {
            // return redirect()->back()->with("error", $e->getMessage());
            dd($e->getMessage(), $e->getLine());
        } finally {
            dbdeactive();
        }
    }
    protected function payment_start(Request $request, $exam, $exam_result, $coupon_code, $exam_price)
    {
        $payment_dat = [
            'exam_id' => $request->exam_id,
            'exam_name' => $exam->name[app()->getLocale() . '_name'],
            'exam_image' => getImageUrl($exam->image, 'exams'),
            'exam_result_id' => $exam_result->id,
            'user_id' => Auth::guard('users')->id(),
            'user_name' => Auth::guard('users')->user()->name,
            'user_email' => Auth::guard('users')->user()->email ?? null,
            'user_phone' => Auth::guard('users')->user()->phone ?? null,
            'token' => createRandomCode("string", 20),
            'price' => $exam->price,
            'endirim_price' => $exam->endirim_price,
            'amount' => $exam_price,
            'coupon_id' => !empty($coupon_code) && isset($coupon_code->id) ? $coupon_code->id ?? null : null,
            'coupon_name' => !empty($coupon_code) && !empty($coupon_code->name) && isset($coupon_code->name[app()->getLocale() . '_name']) ? $coupon_code->name[app()->getLocale() . '_name'] ?? null : null,
            'coupon_code' => !empty($coupon_code) && isset($coupon_code->code) ? $coupon_code->code ?? null : null,
            'coupon_discount' => !empty($coupon_code) && isset($coupon_code->discount) ? $coupon_code->discount ?? null : null,
            'coupon_type' => !empty($coupon_code) && isset($coupon_code->type) ? $coupon_code->type ?? null : null,
        ];

        $apiscontroller = new ApisController();
        $data = $apiscontroller->create_payment($payment_dat);
        // return $data;
    }

    public function add_edit_exam(Request $request)
    {
        try {
            $data = collect();
            // DB::transaction(function () use (&$data, $request) {
                if (isset($request->top_id) && !empty($request->top_id)) {
                    $data = Exam::where("id", $request->top_id)->first();
                } else {
                    $data = new Exam();
                }

                if ($request->hasFile('image')) {
                    $image = image_upload($request->file("image"), 'exams');
                }

                $name = [
                    'az_name' => trim(GoogleTranslate::trans($request->exam_name, 'az')),
                    'ru_name' => trim(GoogleTranslate::trans($request->exam_name, 'ru')),
                    'en_name' => trim(GoogleTranslate::trans($request->exam_name, 'en')),
                ];
                $description = [
                    'az_description' => trim(GoogleTranslate::trans($request->description, 'az')),
                    'ru_description' => trim(GoogleTranslate::trans($request->description, 'ru')),
                    'en_description' => trim(GoogleTranslate::trans($request->description, 'en')),
                ];
                $start_time = null;
                if ($request->input('start_time') != null)
                    $start_time = Carbon::parse($request->input('start_time'));

                $data->category_id = intval($request->input('category_id'));
                $data->name = $name;
                $data->content = $description;
                $data->slug = Str::slug($name['az_name']);
                $data->duration = $request->input('duration') ?? 0;
                $data->point = $request->input('point') ?? 0;
                $data->status = $request->input('exam_status')=="on" ? 1 : 0;
                $data->order_number = 1;
                $data->price = $request->input('price') ?? 0;
                $data->endirim_price = $request->input('endirim_price') ?? 0;
                $data->user_id = intval($request->auth_id) ?? auth('users')->id();
                $data->user_type = "users";
                $data->repeat_sound = false;
                $data->show_result_user = $request->input('exam_show_result_answer')=="on" ? 1 : 0;
                $data->show_calc = $request->input('show_calculator')=="on" ? 1 : 0;
                $data->start_time = $start_time ?? null;
                if(!empty($image))
                    $data->image = $image;
                $data->layout_type = $request->layout_type ?? 'standart';
                $data->save();

                $exam_start_pages = ExamStartPageIds::where("exam_id", $data->id)->get();
                foreach ($exam_start_pages as $val) {
                    $val->delete();
                }

                if (!empty($request->exam_start_page_id)) {
                    foreach ($request->exam_start_page_id as $id) {
                        $page = new ExamStartPageIds();
                        $page->exam_id = $data->id;
                        $page->start_page_id = $id;
                        $page->save();
                    }
                }

                $references = ExamReferences::where("exam_id", $data->id)->get();
                foreach ($references as $val) {
                    $val->delete();
                }

                if (!empty($request->exam_references)) {
                    foreach ($request->exam_references as $id) {
                        $page = new ExamReferences();
                        $page->exam_id = $data->id;
                        $page->reference_id = $id;
                        $page->save();
                    }
                }
                dbdeactive();
            // });
            return redirect(route('exams_front.createoredit',['slug'=>$data->slug]))->with('success', "Əlavə edildi");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
