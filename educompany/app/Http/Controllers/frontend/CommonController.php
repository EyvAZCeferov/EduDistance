<?php

namespace App\Http\Controllers\frontend;

use App\Models\Exam;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\Section;
use App\Models\ExamResult;
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
    public function exam($subdomain=null,$exam_id)
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
            $result = collect();
            $nextsection=false;
            // DB::transaction(function () use ($request, &$result,&$nextsection) {
                $exam = Exam::findOrFail($request->exam_id);
                $examsection=Section::where("id",$request->current_section)->first();
                $result = ExamResult::where("id", $request->exam_result_id)->first();
                $result->update([
                    'time_reply' => $request->time_exam,
                ]);

                if(!empty($request->answers) && count($request->answers)>0){
                    foreach ($request->answers as $section_id => $answers) {
                        foreach ($answers as $question_id => $answer) {
                            $section = $exam->sections->find($section_id);
                            $question = ExamQuestion::where("id", $question_id)->first();
                            $time_reply=$request->question_time_replies[$question_id]??0;
                            // if (!empty($question) && !empty($section)) {
                                if ($question->type === 1 || $question->type==5) {
                                    $resultAnswer = new ExamResultAnswer();
                                    $resultAnswer->result_id = $result->id;
                                    $resultAnswer->section_id = $section_id;
                                    $resultAnswer->question_id = $question->id;
                                    $resultAnswer->answer_id = $answer;
                                    $resultAnswer->result = $answer == $question->correctAnswer()?->id ? 1 : 0;
                                    $resultAnswer->time_reply=$time_reply==0?null:$time_reply;
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
                                    $resultAnswer->time_reply=$time_reply==0?null:$time_reply;
                                    $resultAnswer->save();
                                } else if ($question->type == 3) {
                                    $resultAnswer = new ExamResultAnswer();
                                    $resultAnswer->result_id = $result->id;
                                    $resultAnswer->section_id = $section_id;
                                    $resultAnswer->question_id = $question->id;
                                    $resultAnswer->value = $answer;
                                    $correctAnswer = $question->correctAnswer()?->answer;
                                    if ($correctAnswer && !empty($answer)) {
                                        $correctAnswersArray = explode(',', strip_tags_with_whitespace($correctAnswer));
                                        $resultAnswer->result = in_array(strip_tags_with_whitespace($answer), $correctAnswersArray) ? 1 : 0;
                                    } else {
                                        $resultAnswer->result = 0;
                                    }
                                    $resultAnswer->time_reply=$time_reply==0?null:$time_reply;
                                    $resultAnswer->save();
                                } else if ($question->type == 4) {
                                    if($answer['answered']==1){
                                        if (!empty($answer['questions']) && !empty($answer['answers'])) {
                                            $newArray = array_combine($answer['questions'], $answer['answers']);
                                            $newArrayEncoded = [];
                                            foreach ($newArray as $key => $value) {
                                                $newArrayEncoded[strip_tags_with_whitespace($key)] = strip_tags_with_whitespace($value);
                                            }
                                            $array2 = $question->answers->pluck('answer')->toArray();
                                            $newArray2 = [];
                                            foreach ($array2 as $value) {
                                                $decodedValue = json_decode($value, true);
                                                $newArray2[strip_tags_with_whitespace($decodedValue['question_content'])] = strip_tags_with_whitespace($decodedValue['answer_content']);
                                            }

                                            $difference = ($newArrayEncoded === $newArray2) ? true : false;
                                            $resultAnswer = new ExamResultAnswer();
                                            $resultAnswer->result_id = $result->id;
                                            $resultAnswer->section_id = $section_id;
                                            $resultAnswer->question_id = $question->id;
                                            $resultAnswer->value = json_encode($newArray);
                                            $resultAnswer->result = $difference;
                                            $resultAnswer->time_reply=$time_reply==0?null:$time_reply;
                                            $resultAnswer->save();
                                        }
                                    }
                                }
                            // }
                        }
                    }
                }

                if ($examsection->time_range_sections > 0) {
                    $point = calculate_exam_result($result->id);
                    session()->put('point', $point);
                    session()->put('time_reply', session()->get("time_reply")??0+$request->time_exam);
                    session()->put('selected_section',$request->selected_section+1);
                    $nextsection=true;
                } else {
                    $pointlast = session()->has('point') ? session()->get('point') : 0;
                    $point = number_format($pointlast+ calculate_exam_result($result->id), 2);
                    $time_reply=session()->get("time_reply")??0+$request->time_exam;
                    app()->setLocale(session()->get('changedLang'));
                    session()->put('language', session()->get('changedLang'));
                    session()->put('lang', session()->get('changedLang'));
                    $result->update([
                        'point' => $point,
                        'time_reply'=>$time_reply
                    ]);
                    session()->forget('point');
                    session()->forget('time_reply');
                    session()->forget('selected_section');
                }

                $nexturl='';

                if($nextsection==true){
                    $nexturl=route("user.exams.redirect_exam", ['exam_id'=>$result->exam_id,'selected_section'=>session()->get('selected_section')??0]);
                }else{
                    if($exam->show_result_user==true){
                        $nexturl=route("user.exam.resultpage", $result->id);
                        remove_repeated_result_answers($result->id);
                    }else{
                        $nexturl=route("page.welcome");
                    }
                }

            // });

            return response()->json([
                'status' => 'success',
                'message' => trans("additional.messages.exam_finished", [], $request->language ?? 'az'),
                'url' => $nexturl,
                'nextsection'=>$nextsection
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(),'line'=>$e->getLine()]);
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
    public function examResultPage_nosubdomain($result_id)
    {
        $exam_result = ExamResult::where('user_id', auth('users')->user()->id)
            ->with('answers.answer')
            ->orderByDesc('id')
            ->findOrFail($result_id);

        return view('frontend.exams.resultpage', compact('exam_result'));
    }
    public function examResultPage($subdomain=null,$result_id)
    {
        if(Auth::guard("users")->check() && Auth::guard("users")->user()->user_type==2){
            $exam=Exam::where('slug',$result_id)->where("user_id",Auth::guard("users")->id())->first();
            $exam_results = ExamResult::where('exam_id', $exam->id)
                ->with('answers.answer')
                ->orderByDesc('id')->get();
            if(!empty($exam_results) && count($exam_results)>0){
                return view('frontend.exams.results.resultoncompany', compact('exam_results','exam'));
            }else{
                return redirect()->back()->with('info',trans("additional.pages.exams.notfound"));
            }
        }else{
            $exam_result = ExamResult::where('user_id', auth('users')->user()->id)
                ->with('answers.answer')
                ->orderByDesc('id')
                ->findOrFail($result_id);
            return view('frontend.exams.resultpage', compact('exam_result'));
        }
    }
    public function examResult_nosubdomain($result_id)
    {
        $exam_result = ExamResult::where('user_id', auth('users')->user()->id)
            ->with('answers.answer')
            ->orderByDesc('id')
            ->findOrFail($result_id);

        return view('frontend.exams.result', compact('exam_result'));
    }
    public function examResult($subdomain=null,$result_id)
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

                if(session()->has('selected_section') && (session()->get('selected_section')==$request->selected_section)){
                    $nexturl=exam_finish_and_calc($request->exam_id,Auth::guard('users')->id());
                    if(!empty($nexturl))
                        return redirect($nexturl);
                }

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
                    if (empty($exam_result) && !isset($exam_result->id)) {
                        $exam_result2 = ExamResult::where("exam_id", $request->exam_id)
                        ->where('user_id', Auth::guard('users')->id())
                        ->whereNotNull("point")
                        ->where('payed',true)
                        ->first();

                        if(!empty($exam_result2)){
                           return $this->examResult_nosubdomain($exam_result2->id);
                        }else{
                            if (!empty($exam->start_pages)) {
                                $default = exam_start_page();
                                foreach ($exam->start_pages as $page) {
                                    if (!empty($page->start_page)) {
                                        $exam_start_pages->push($page->start_page);
                                    }
                                }
                                $exam_start_pages->push($default);
                                $exam_start_pages = $exam_start_pages->sortBy('order_number')->values();
                            } else {
                                $exam_start_pages = exam_start_page();
                            }
                            if (empty($exam_start_pages)) {
                                return view("frontend.exams.exam_main_process.index", compact("exam", 'exam_result'));
                            } else {
                                return view("frontend.exams.exam_main_process.start_page", compact("exam", 'exam_start_pages'));
                            }
                        }
                    } else {
                        if ($exam_result->payed == true) {
                            if($exam->questionCount()>0){
                                return view("frontend.exams.exam_main_process.index", compact('exam', 'exam_result'));
                            }else{
                                $exam_result->delete();
                                return redirect()->back()->with('info',trans('additional.messages.examnotfound'));
                            }
                        } else {
                            $payment = payments(Auth::guard("users")->id(), $exam->id, $exam_result->id, null, null, null);
                            if (!empty($payment) && isset($payment->id)) {
                                $payment_link = $this->payment_start($request, $exam, $exam_result, !empty($payment->coupon) ? $payment->coupon : null, $payment->ammount ?? ($exam->endirim_price ?? $exam->price));
                                if (!empty($payment_link))
                                    return redirect($payment_link);
                                else
                                    return $this->notfound();
                            } else {
                                if($exam->questionCount()>0){
                                    return $this->set_exam($request);
                                }else{
                                    $exam_result->delete();
                                    return redirect()->back()->with('info',trans('additional.messages.examnotfound'));
                                }
                            }
                        }
                    }
                } else {
                    return $this->notfound();
                }
            } else {
                return redirect(route('login'))->with('error', trans("additional.headers.login"));
            }
        } catch (\Exception $e) {
            dd($e->getMessage(),$e->getLine());
            return redirect()->back()->with("error", $e->getMessage(), $e->getLine());
        } finally {
            dbdeactive();
        }
    }
    public function set_exam(Request $request)
    {
        try {
            session()->forget('point');
            session()->forget('time_reply');
            session()->forget('selected_section');
            $exam_result = ExamResult::where("exam_id", $request->get("exam_id"))
                ->where('user_id', Auth::guard('users')->id())
                ->whereNull("point")
                ->first();
            $exam = Exam::where("id", $request->get("exam_id"))
                ->with(['sections', 'references'])
                ->first();
            $coupon_code = collect();
            if (!empty($request->get("coupon_code"))) {
                $coupon_code = coupon_codes($request->get('coupon_code'), 'code');
            }
            $exam_price = 0;
            if ($exam->price > 0) {
                $payment = !empty($exam_result) && isset($exam_result->id) ? payments($exam_result->user_id, $exam->id, $exam_result->id, null, null, null) : null;
                if (empty($payment)) {
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
            }
            if (empty($exam_result) && !isset($exam_result->id)) {
                $exam_result = new ExamResult();
                $exam_result->user_id = Auth::guard('users')->id();
                $exam_result->exam_id = $request->exam_id;
                $exam_result->payed = $exam_price == 0 ? true : false;
                $exam_result->save();
            }

            if ($exam_result->payed == true) {
                return $this->redirect_exam($request);
            } else {
                $payment_link = $this->payment_start($request, $exam, $exam_result, $coupon_code, $exam_price);
                if (!empty($payment_link))
                    return redirect($payment_link);
                else
                    return $this->notfound();
            }
        } catch (\Exception $e) {
            dd($e->getMessage(),$e->getLine());
            return redirect()->back()->with("error", $e->getMessage());
        } finally {
            dbdeactive();
        }
    }
    protected function payment_start(Request $request, $exam, $exam_result, $coupon_code, $exam_price)
    {
        try {
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
            if (!empty($data) && isset($data)){
                return $data;
            }else{
                return null;
            }
        } catch (\Exception $e) {
            dd($e->getMessage(),$e->getLine());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
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

            if(!(isset($request->exam_name) && !empty($request->exam_name)))
                return redirect()->back()->with('error','Məlumatları tam doldurun');

            if(!((isset($request->description) && !empty($request->description)) || (isset($request->mce_0) && !empty($request->mce_0))))
                return redirect()->back()->with('error','Məlumatları tam doldurun');

            if ($request->hasFile('image')) {
                $image = image_upload($request->file("image"), 'exams');
            }

            if(Auth::guard('users')->user()->user_type==1){
                return redirect('/logout')->with("error","Hesabınıza şirkət olaraq daxil olmalısınız");
            }

            $name = [
                'az_name' => trim($request->exam_name, 'az'),
                'ru_name' => trim(GoogleTranslate::trans($request->exam_name, 'ru')),
                'en_name' => trim(GoogleTranslate::trans($request->exam_name, 'en')),
            ];
            $description = [
                'az_description' => trim(modifyRelativeUrlsToAbsolute($request->description ?? $request->mce_0, 'az')),
                'ru_description' => trim(modifyRelativeUrlsToAbsolute(GoogleTranslate::trans($request->description ?? $request->mce_0, 'ru'))),
                'en_description' => trim(modifyRelativeUrlsToAbsolute(GoogleTranslate::trans($request->description ?? $request->mce_0, 'en'))),
            ];
            $start_time = null;
            if ($request->input('start_time') != null)
                $start_time = Carbon::parse($request->input('start_time'));

            $data->category_id = intval($request->input('category_id'));
            $data->name = $name;
            $data->content = $description;
            $data->slug = Str::slug($name['az_name']).'-'.Str::uuid();
            $data->point = $request->input('point') ?? 0;
            $data->status = $request->input('exam_status') == "on" ? 1 : 0;
            $data->order_number = 1;
            $data->price = $request->input('price') ?? 0;
            $data->endirim_price = $request->input('endirim_price') ?? 0;
            $data->user_id = intval($request->auth_id) ?? auth('users')->id();
            $data->user_type = "users";
            $data->repeat_sound = $request->input('repeat_sound') == "on" ? 1 : 0;
            $data->show_result_user = $request->input('exam_show_result_answer') == "on" ? 1 : 0;
            $data->show_calc = $request->input('show_calculator') == "on" ? 1 : 0;
            $data->start_time = $start_time ?? null;
            if (!empty($image))
                $data->image = $image;
            $data->layout_type = $request->input('layout_type') ?? 'standart';
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

            // });
            dbdeactive();
            return redirect(route('exams_front.createoredit', ['slug' => $data->slug]))->with('success', "Əlavə edildi");
        } catch (\Exception $e) {
            dd([$e->getMessage(), $e->getLine()]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function add_edit_exam_subdomain(Request $request,$subdomain=null)
    {
        try {
            $data = collect();
            // DB::transaction(function () use (&$data, $request) {

            if(!(isset($request->exam_name) && !empty($request->exam_name)))
                return redirect()->back()->with('error','Məlumatları tam doldurun');

            if(!((isset($request->description) && !empty($request->description)) || (isset($request->mce_0) && !empty($request->mce_0))))
                return redirect()->back()->with('error','Məlumatları tam doldurun');


            if (isset($request->top_id) && !empty($request->top_id)) {
                $data = Exam::where("id", $request->top_id)->first();
            } else {
                $data = new Exam();
            }

            if(Auth::guard('users')->user()->user_type==1){
                return redirect('/logout')->with("error","Hesabınıza şirkət olaraq daxil olmalısınız");
            }

            if ($request->hasFile('image')) {
                $image = image_upload($request->file("image"), 'exams');
            }

            $name = [
                'az_name' => trim($request->exam_name, 'az'),
                'ru_name' => trim(GoogleTranslate::trans($request->exam_name, 'ru')),
                'en_name' => trim(GoogleTranslate::trans($request->exam_name, 'en')),
            ];
            $description = [
                'az_description' => trim(modifyRelativeUrlsToAbsolute($request->description ?? $request->mce_0, 'az')),
                'ru_description' => trim(modifyRelativeUrlsToAbsolute(GoogleTranslate::trans($request->description ?? $request->mce_0, 'ru'))),
                'en_description' => trim(modifyRelativeUrlsToAbsolute(GoogleTranslate::trans($request->description ?? $request->mce_0, 'en'))),
            ];
            $start_time = null;
            if ($request->input('start_time') != null)
                $start_time = Carbon::parse($request->input('start_time'));

            $data->category_id = intval($request->input('category_id'));
            $data->name = $name;
            $data->content = $description;
            $data->slug = Str::slug($name['az_name']).'-'.Str::uuid();
            $data->point = $request->input('point') ?? 0;
            $data->status = $request->input('exam_status') == "on" ? 1 : 0;
            $data->order_number = 1;
            $data->price = $request->input('price') ?? 0;
            $data->endirim_price = $request->input('endirim_price') ?? 0;
            $data->user_id = intval($request->auth_id) ?? auth('users')->id();
            $data->user_type = "users";
            $data->repeat_sound = $request->input('repeat_sound') == "on" ? 1 : 0;
            $data->show_result_user = $request->input('exam_show_result_answer') == "on" ? 1 : 0;
            $data->show_calc = $request->input('show_calculator') == "on" ? 1 : 0;
            $data->start_time = $start_time ?? null;
            if (!empty($image))
                $data->image = $image;
            $data->layout_type = $request->input('layout_type') ?? 'standart';
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

            // });
            dbdeactive();
            return redirect(route('exams_front.createoredit', ['slug' => $data->slug]))->with('success', "Əlavə edildi");
        } catch (\Exception $e) {
            dd([$e->getMessage(), $e->getLine()]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function examResultPageStudentsWithSubdomain($subdomain=null,$result_id)
    {
        if(Auth::guard("users")->check() && Auth::guard("users")->user()->user_type==2){
            $exam=Exam::where('id',$result_id)->where("user_id",Auth::guard("users")->id())->first();
            $exam_results = ExamResult::where('exam_id', $exam->id)
                ->with('answers.answer')
                ->orderByDesc('id')->get();
            if(!empty($exam_results) && count($exam_results)>0){
                return view('frontend.exams.results.resultoncompanywithdesign', compact('exam_results','exam'));
            }else{
                return redirect()->back()->with('info',trans("additional.pages.exams.notfound"));
            }
        }
    }
}
