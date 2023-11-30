<?php

namespace App\Http\Controllers\frontend;

use App\Models\Exam;
use App\Models\User;
use App\Helpers\Epoint;
use App\Models\Category;
use App\Models\Payments;
use App\Models\CouponCodes;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ApisController extends Controller
{
    public function searchinfilled(Request $request)
    {
        try {
            if ($request->type == "exams") {
                if (isset($request->action) && !empty($request->action)) {
                    if ($request->action == "category") {
                        if ($request->category == "all") {
                            $data = exams(null, null);
                        } else {
                            $category = Category::where('slugs->az_slug', $request->category)
                                ->orWhere('slugs->ru_slug', $request->category)
                                ->orWhere('slugs->en_slug', $request->category)
                                ->where('status', true)
                                ->orderBy('id', 'DESC')
                                ->first();
                            if (!empty($category)) {
                                $data = Exam::where("category_id", $category->id)->get();
                            } else {
                                $data = [];
                            }
                        }
                    }
                } else {
                    $data = Exam::whereRaw('LOWER(JSON_EXTRACT(`name`, "$.az_name")) like ?', ['%' . $request->input('query') . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`name`, "$.ru_name")) like ?', ['%' . $request->input('query') . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`name`, "$.en_name")) like ?', ['%' . $request->input('query') . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`content`, "$.az_description")) like ?', ['%' . $request->input('query') . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`content`, "$.ru_description")) like ?', ['%' . $request->input('query') . '%'])
                        ->orWhereRaw('LOWER(JSON_EXTRACT(`content`, "$.en_description")) like ?', ['%' . $request->input('query') . '%'])
                        ->get();
                    return response()->json([
                        'status' => 'success',
                        "view" => view('frontend.' . $request->type . '.render_exams', compact('data'))->render()
                    ]);
                }
            } else {
                $data = User::whereRaw('LOWER(`name`) like ?', ['%' . $request->input('query') . '%'])
                    ->with('exams')
                    ->get();
                return response()->json([
                    'status' => 'success',
                    "view" => view('frontend.' . $request->type . '.render_exams', compact('data'))->render()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function filterelements(Request $request)
    {
        try {
            $ids = $request->ids;
            $orderby = null;
            $data = collect();
            if (isset($request->orderby) && !empty($request->orderby) && $request->orderby == "asc") {
                $orderby = 'name->"$.' . $request->language . '_name"' . $request->orderby;
            } else if (isset($request->orderby) && !empty($request->orderby) && $request->orderby == "desc") {
                $orderby = 'name->"$.' . $request->language . '_name"' . $request->orderby;
            } else if (isset($request->orderby) && !empty($request->orderby) && $request->orderby == "random") {
                $orderby = "inrandomorder";
            } else if (isset($request->orderby) && !empty($request->orderby) && $request->orderby == "priceasc") {
                $orderby = 'price asc';
            } else if (isset($request->orderby) && !empty($request->orderby) && $request->orderby == "pricedesc") {
                $orderby = 'price desc';
            }

            if ($request->type == "exams") {
                if ($orderby != "inrandomorder") {
                    $data = Exam::whereIn('id', $ids)->orderByRaw($orderby)->get();
                } else {
                    $data = Exam::whereIn('id', $ids)->inRandomOrder()->get();
                }
            }
            return response()->json([
                'status' => 'success',
                "view" => view('frontend.exams.render_exams', compact('data'))->render()
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function callback(Request $request)
    {
        try {
            $epoint = new Epoint([
                "data" => $request->get("data"),
                "signature" => $request->get("signature")
            ]);

            if ($epoint->isSignatureValid()) {

                $json_string = $epoint->getDataAsJson();
                $json = $epoint->getDataAsObject();

                if ($json->status == "success") {
                    if (!empty($json->card_id)) {
                        //payments
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::info(['------------------CallBack Error------------------', $e->getMessage(), $e->getLine()]);
        }
    }

    public function create_payment($req)
    {
        try {
            $user=[
                'name'=>$req['user_name'],
                'email'=>$req['user_email'],
                'phone'=>$req['user_phone'],
                'id'=>$req['user_id']
            ];
            $exam=[
                'name'=>$req['exam_name'],
                'image'=>$req['exam_image'],
                'id'=>$req['exam_id']
            ];
            $coupon=[
                'name'=>$req['coupon_name']??null,
                'discount'=>$req['coupon_discount']??null,
                'code'=>$req['coupon_code']??null,
                'type'=>$req['coupon_type']??null,
                'id'=>$req['coupon_id']??null
            ];
            $payment = new Payments();
            $payment->token = $req['token'];
            $payment->amount = $req['amount'];
            $payment->payment_status = 0;
            $payment->data = $req;
            $payment->user_id=$req['user_id'];
            $payment->exam_id=$req['exam_id'];
            $payment->coupon_id=$req['coupon_id']??null;
            $payment->exam_result_id=$req['exam_result_id'];
            $payment->exam_data=$exam;
            $payment->user_data=$user;
            $payment->coupon_data=$coupon;
            $payment->save();

            // 'transaction_id',
            // 'frompayment'
            if ($req['amount'] > 0) {
                $epoint = new Epoint();
                $epoint = $epoint->typeCard($payment->id, $payment->amount, $req['exam_name']);
                return $epoint;
            } else {
                return $payment;
            }

        } catch (\Exception $e) {
            return [$e->getMessage(), $e->getLine()];
            Log::info(['------------------Payment Create Callback------------------', $e->getMessage(), $e->getLine()]);
        }
    }
    public function check_coupon_code(Request $request)
    {
        try {
            if (isset($request->code) && !empty($request->code)) {
                $code = CouponCodes::where("code", $request->code)
                    ->where("status", true)->first();
                if (!empty($code)) {
                    $exam = Exam::where("id", $request->exam)->first();
                    $new_price = $exam->price;

                    if ($exam->endirim_price) {
                        if ($code->type == "percent") {
                            $new_price = $exam->endirim_price - ($exam->endirim_price * $code->discount);
                        } else {
                            $new_price = $exam->endirim_price - $code->discount;
                        }
                    } else {
                        if ($code->type == "percent") {
                            $new_price = $exam->price - ($exam->price * $code->discount);
                        } else {
                            $new_price = $exam->price - $code->discount;
                        }
                    }

                    $result = '<span class="text text-info">' . trans('additional.pages.payments.coupon_info', [], $request->language) . ': ' . $code->name[$request->language . '_name'] . ': ' . $code->discount . ($code->type == 'percent' ? '%' : '₼') . '<br/>' . trans('additional.pages.payments.new_price', [], $request->language) . ' <span class="font-weight-bold text text-danger">' . $new_price . '₼</span> </span>';

                    return response()->json(['status' => 'success', 'data' => $result]);
                } else {
                    return response()->json(['status' => 'error', 'message' => trans("additional.messages.nocodefound", [], $request->language ?? 'az')]);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => trans("additional.messages.nocodefound", [], $request->language ?? 'az')]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
