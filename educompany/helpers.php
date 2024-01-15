<?php

use App\Models\Exam;
use App\Models\User;
use App\Models\Blogs;
use App\Models\Teams;
use App\Models\Section;
use App\Models\Sliders;
use App\Models\Category;
use App\Models\Counters;
use App\Models\Settings;
use App\Models\ExamResult;
use App\Models\References;
use App\Models\CouponCodes;
use App\Models\ExamQuestion;
use App\Models\Payments;
use App\Models\ExamStartPage;
use App\Models\MarkQuestions;
use App\Models\StandartPages;
use App\Models\ExamReferences;
use App\Models\StudentRatings;
use App\Models\ExamResultAnswer;
use App\Models\ExamStartPageIds;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use GuzzleHttp\Client;


if (!function_exists('answerChoice')) {
    function answerChoice($key): string
    {
        $choices = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
        return $choices[$key] ?? $key;
    }
}

if (!function_exists('getImageUrl')) {
    function getImageUrl($image, $clasore)
    {
        $url = public_path('uploads/' . $clasore . '/' . $image);
        try {
            if (in_array(pathinfo($image, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $tempurl = 'temp/' . $image;
                if (!File::exists(public_path($tempurl))) {
                    Image::cache(function ($image) use ($url, $tempurl) {
                        return $image->make($url)->save(public_path($tempurl));
                    });
                }
            } else {
                $tempurl = '/uploads/' . $clasore . '/' . $image;
            }

            return url($tempurl);
        } catch (\Exception $e) {
            \Log::info([
                '----------------GET IMAGE ERROR-----------------',
                $e->getMessage(),
                $e->getLine()
            ]);
            return url($tempurl);
        }
    }
}

if (!function_exists('strip_tags_with_whitespace')) {
    function strip_tags_with_whitespace($string, $allowable_tags = null)
    {
        $string = str_replace('<', ' <', $string);
        $string = preg_replace('/\p{Z}/u', ' ', $string);
        $string = str_replace(['&nbsp;', '\u{A0}'], ' ', $string);
        $string = strip_tags($string, $allowable_tags);
        $string = preg_replace('/\s+/', ' ', $string);
        $string = trim($string);

        return $string;
    }
}

if (!function_exists('createRandomCode')) {
    function createRandomCode($type = "int", $length = 4)
    {
        if ($type == "int") {
            if ($length == 4) {
                return random_int(1000, 9999);
            }
        } elseif ($type == "string") {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
    }
}

if (!function_exists('dbdeactive')) {
    function dbdeactive()
    {
        DB::connection()->disconnect();
        Cache::flush();
    }
}

if (!function_exists('image_upload')) {
    function image_upload($image, $clasor, $imagename = null)
    {
        try {
            $filename = $imagename ?? time() . '.' . $image->extension();
            $image->storeAs($clasor, $filename, 'uploads');
            return $filename;
        } catch (\Exception $e) {
            \Log::info([
                '------------------IMAGE UPLOAD ERROR-----------------',
                $e->getMessage(),
                $e->getLine(),
            ]);
        }
    }
}

if (!function_exists('file_upload')) {
    function file_upload($file, $clasor, $name = null)
    {
        $filename = $name ?? time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs($clasor, $filename, 'uploads');
        return $filename;
    }
}

if (!function_exists('delete_image')) {

    function delete_image($image, $clasor)
    {
        if (Storage::disk('uploads')->exists($clasor . '/' . $image)) {
            Storage::disk('uploads')->delete($clasor . '/' . $image);
            return true;
        }
        return false;
    }
}

if (!function_exists('queuework')) {
    function queuework()
    {
        while (true) {
            try {
                Artisan::call('queue:work');
            } catch (\Exception $e) {
                return $e->getMessage();
            }
            sleep(1);
        }
    }
}

if (!function_exists('count_endirim_faiz')) {
    function count_endirim_faiz($price, $endirim_price)
    {
        $model = 0;
        if ($price > 0 && $endirim_price > 0 && $endirim_price <= $price) {
            $discount_percentage = (($price - $endirim_price) / $price) * 100;
            $formatted_discount = number_format($discount_percentage, 2); // İki ondalık basamak
            $model = $formatted_discount;
        }
        return Cache::rememberForever("count_endirim_faiz" . $price . $endirim_price, fn () => $model);
    }
}

if (!function_exists('settings')) {
    function settings($key = null)
    {
        $mdsettings = Settings::latest()->first();
        if (isset($key) && !empty($key)) {
            $subdomain = session()->has("subdomain") ? session()->get("subdomain") : null;
            if (!empty($subdomain)) {
                $mds = users($subdomain, 'subdomain');
                if (!empty($mds) && isset($mds->id)) {
                    if ($key == "name") {
                        $model = isset($mds->name) && !empty($mds->name) ? $mds->name : $mdsettings->name[app()->getLocale() . '_name'];
                    } else if ($key == "description") {
                        $model = isset($mds->name) && !empty($mds->name) ? $mds->name . '-' . $mds->subdomain : $mdsettings->description[app()->getLocale() . '_description'];
                    } else if ($key == "logo") {
                        $model = isset($mds->picture) && !empty($mds->picture) ? getImageUrl($mds->picture, 'users') : getImageUrl($key, 'settings');
                    } else if ($key == "logo_white") {
                        $model = getImageUrl($mdsettings->logo_white, 'settings');
                    }
                } else {
                    if ($key == "name") {
                        $model = $mdsettings->name[app()->getLocale() . '_name'];
                    } else if ($key == "description") {
                        $model = $mdsettings->description[app()->getLocale() . '_description'];
                    } else if ($key == "logo") {
                        $model = getImageUrl($mdsettings->logo, 'settings');
                    } else if ($key == "logo_white") {
                        $model = getImageUrl($mdsettings->logo_white, 'settings');
                    }
                }
            } else {
                if ($key == "name") {
                    $model = $mdsettings->name[app()->getLocale() . '_name'];
                } else if ($key == "description") {
                    $model = $mdsettings->description[app()->getLocale() . '_description'];
                } else if ($key == "logo") {
                    $model = getImageUrl($mdsettings->logo, 'settings');
                } else if ($key == "logo_white") {
                    $model = getImageUrl($mdsettings->logo_white, 'settings');
                }
            }
        } else {
            $model = $mdsettings;
        }
        return Cache::rememberForever("settings" . $key . session()->getId(), fn () => $model);
    }
}

if (!function_exists('standartpages')) {
    function standartpages($key = null, $type = "slug")
    {
        if (isset($key) && !empty($key) && $type == "type") {
            $model = StandartPages::where('type', $key)->first();
        } else if (isset($key) && !empty($key) && $type == "slug") {
            $model = StandartPages::where('slugs->az_slug', $key)->orWhere('slugs->ru_slug', $key)->orWhere('slugs->en_slug', $key)->first();
        } else {
            $model = StandartPages::orderBy('id', 'ASC')->get();
        }
        return Cache::rememberForever("standartpages" . $key . $type, fn () => $model);
    }
}

if (!function_exists('categories')) {
    function categories($key = null, $type = "slug")
    {
        if ($type == "slug") {
            $model = Category::where('slugs->az_slug', $key)->orWhere('slugs->ru_slug', $key)->orWhere('slugs->en_slug', $key)->first();
        } else if ($type == "onlyparent") {
            $model = Category::whereNull('parent_id')->orderBy('order_number', 'DESC')->get();
        } else if ($type == "exammedcats") {
            $model = Category::whereHas('exams')->orderBy('order_number', 'DESC')->get();
        } else if ($type == "id") {
            $model = Category::where('id', $key)->first();
        } else {
            $model = Category::orderBy('order_number', 'DESC')->get();
        }
        return Cache::rememberForever("categories" . $key . $type, fn () => $model);
    }
}

if (!function_exists('sections')) {
    function sections($key = null, $type = "exammed")
    {
        if ($type == "exammed") {
            $model = Section::whereHas('questions')->select('name', DB::raw('MAX(id) as id'))
                ->groupBy('name')->get();
        }else if ($type == "id") {
            $model = Section::where('id',$key)->first();
        } else {
            $model = Section::select('name', DB::raw('MAX(id) as id'))
                ->groupBy('name')
                ->orderBy('id', 'DESC')->get();
        }
        return Cache::rememberForever("sections" . $key . $type, fn () => $model);
    }
}

if (!function_exists('users')) {
    function users($key = null, $type = "exammed")
    {
        if ($type == "exammed") {
            $model = User::where('user_type', 2)->orderBy("id", "DESC")->whereHas('exams')->get();
        } else if ($type == "company") {
            $model = User::where('user_type', 2)->orderBy("id", "DESC")->get();
        } else if ($type == "subdomain") {
            $model = User::where('subdomain', $key)->where('user_type', 2)->orderBy("id", "DESC")->first();
        } else if ($type == "id") {
            $model = User::where('id', $key)->first();
        } else {
            $model = User::orderBy("id", "DESC")->whereHas('exams')->get();
        }
        return Cache::rememberForever("users" . $key . $type, fn () => $model);
    }
}

if (!function_exists('counters')) {
    function counters()
    {
        $model = Counters::orderBy('order_number', 'ASC')->where('status', true)->get();
        return Cache::rememberForever("counters", fn () => $model);
    }
}

if (!function_exists('exams')) {
    function exams($key = null, $type = "id")
    {
        if (isset($key) && $type == "id") {
            $model = Exam::where('id', $key)->first();
        } else if (isset($key) && $type == "slug") {
            $model = Exam::where("slug", $key)->first();
        } else if (isset($key) && $type == "subdomain") {
            $model = Exam::where("user_id", $key)->orderBy("id", 'DESC');
            if (session()->has("subdomain")) {
                $user = users(session()->get("subdomain"), 'subdomain');
                $model = $model->where("user_id", $user->id);
            }
            $model = $model->get();
        } else if (isset($key) && $type == "search") {
            $model = Exam::whereRaw('LOWER(JSON_EXTRACT(`name`, "$.az_name")) like ?', ['%' . $key . '%'])
                ->orWhereRaw('LOWER(JSON_EXTRACT(`name`, "$.ru_name")) like ?', ['%' . $key . '%'])
                ->orWhereRaw('LOWER(JSON_EXTRACT(`name`, "$.en_name")) like ?', ['%' . $key . '%'])
                ->orWhereRaw('LOWER(JSON_EXTRACT(`description`, "$.az_description")) like ?', ['%' . $key . '%'])
                ->orWhereRaw('LOWER(JSON_EXTRACT(`description`, "$.ru_description")) like ?', ['%' . $key . '%'])
                ->orWhereRaw('LOWER(JSON_EXTRACT(`description`, "$.en_description")) like ?', ['%' . $key . '%'])
                ->orderBy("order_number", 'ASC');
            if (session()->has("subdomain")) {
                $user = users(session()->get("subdomain"), 'subdomain');
                $model = $model->where("user_id", $user->id);
            }
            $model = $model->orderBy("id", 'DESC')->get();
        } else if (empty($key) && $type == "most_used_tests") {
            $model = Exam::with([
                'results' => function ($query) {
                    $query->orderBy('point', 'DESC');
                }
            ])->orderByDesc('id');
            if (session()->has("subdomain")) {
                $user = users(session()->get("subdomain"), 'subdomain');
                $model = $model->where("user_id", $user->id);
            }
            $model = $model->orderBy("id", 'DESC')->get();
        } else {
            $model = Exam::where('status', true)->orderBy("id", 'DESC')->orderBy("order_number", 'ASC');
        }

        return Cache::rememberForever("exams" . $key . $type, fn () => $model);
    }
}

if (!function_exists('sliders')) {
    function sliders()
    {
        $model = Sliders::where('status', true)->orderBy('id', 'DESC')->get();
        return Cache::rememberForever("sliders", fn () => $model);
    }
}

if (!function_exists('student_ratings')) {
    function student_ratings()
    {
        $model = StudentRatings::where('status', true)->orderBy('order_number', 'ASC')->get();
        return Cache::rememberForever("student_ratings", fn () => $model);
    }
}

if (!function_exists('blogs')) {
    function blogs($key = null)
    {
        if (isset($key) && !empty($key)) {
            $model = Blogs::where('status', true)
                ->where('slugs->az_slug', $key)->orWhere('slugs->ru_slug', $key)->orWhere('slugs->en_slug', $key)
                ->first();
        } else {
            $model = Blogs::where('status', true)->orderBy('id', 'DESC')->get();
        }
        return Cache::rememberForever("blogs" . $key, fn () => $model);
    }
}

if (!function_exists('teams')) {
    function teams($key = null)
    {
        if (isset($key) && !empty($key)) {
            $model = Teams::where('slugs->az_slug', $key)->orWhere('slugs->ru_slug', $key)->orWhere('slugs->en_slug', $key)
                ->first();
        } else {
            $model = Teams::orderBy('order_number', 'ASC')->get();
        }
        return Cache::rememberForever("teams" . $key, fn () => $model);
    }
}

if (!function_exists('exam_answered')) {
    function exam_answered($auth_id, $exam_id)
    {
        $model = ExamResult::where('user_id', $auth_id)->where('exam_id', $exam_id)->first();
        return Cache::rememberForever("exam_answered" . $auth_id . $exam_id, fn () => $model);
    }
}

if (!function_exists('exam_start_page')) {
    function exam_start_page($key = null, $type = "default")
    {
        if ($type == "default") {
            $model = ExamStartPage::orderBy("order_number", 'ASC')->where('default', true)->first();
        } else if ($type == "expectdefault") {
            $model = ExamStartPage::orderBy("order_number", 'ASC')->where('default', false)->get();
        } else {
            $model = ExamStartPage::orderBy("order_number", 'ASC')->get();
        }
        return Cache::rememberForever("exam_start_page" . $key . $type, fn () => $model);
    }
}

if (!function_exists('coupon_codes')) {
    function coupon_codes($key = null, $type = "default")
    {
        if ($type == "default") {
            $model = CouponCodes::where("status", 'ASC')->orderBy('id', 'DESC')->first();
        } else if ($type == "code") {
            $model = CouponCodes::where("code", $key)->first();
        } else if ($type == "id") {
            $model = CouponCodes::where("id", $key)->first();
        } else {
            $model = CouponCodes::orderBy('id', 'DESC')->get();
        }
        return Cache::rememberForever("coupon_codes" . $key . $type, fn () => $model);
    }
}

if (!function_exists('payments')) {
    function payments($auth_id = null, $exam_id = null, $exam_result_id = null, $transaction_id = null, $coupon_id = null, $id = null)
    {
        $model = Payments::orderBy('id', 'DESC');
        if (isset($auth_id) && !empty($auth_id)) {
            $model = $model->where("user_id", $auth_id);
        }

        if (isset($exam_id) && !empty($exam_id)) {
            $model = $model->where("exam_id", $exam_id);
        }

        if (isset($exam_result_id) && !empty($exam_result_id)) {
            $model = $model->where("exam_result_id", $exam_result_id);
        }

        if (isset($transaction_id) && !empty($transaction_id)) {
            $model = $model->where("transaction_id", $transaction_id);
        }

        if (isset($coupon_id) && !empty($coupon_id)) {
            $model = $model->where("coupon_id", $coupon_id);
        }

        if (isset($id) && !empty($id)) {
            $model = $model->where("id", $id);
        }

        $model = $model->where('payment_status', 0);
        $model = $model->get();
        if (count($model) == 1) {
            $model = $model[0];
        }
        return Cache::rememberForever("payments" . $auth_id . $exam_id . $exam_result_id . $transaction_id . $coupon_id . $id, fn () => $model);
    }
}

if (!function_exists('references')) {
    function references($key = null, $type = "asc")
    {
        if ($type == "asc") {
            $model = References::orderBy('order_number', 'ASC')->get();
        } else {
            $model = References::orderBy('id', 'DESC')->get();
        }
        return Cache::rememberForever("references" . $key . $type, fn () => $model);
    }
}

if (!function_exists('exist_on_model')) {
    function exist_on_model($key = null, $data_id = null, $type = "references")
    {
        if ($type == "references") {
            $model = ExamReferences::where("exam_id", $data_id)->where("reference_id", $key)->first();
        } elseif ($type == "start_page") {
            $model = ExamStartPageIds::where("exam_id", $data_id)->where("start_page_id", $key)->first();;
        }
        return Cache::rememberForever("exist_on_model" . $key . $data_id . $type, fn () => $model);
    }
}

if (!function_exists('question_is_marked')) {
    function question_is_marked($question_id, $exam_id, $exam_result_id, $user_id)
    {
        $model = MarkQuestions::where("exam_id", $exam_id)
            ->where("exam_result_id", $exam_result_id)
            ->where("question_id", $question_id)
            ->where("user_id", $user_id)->first();
        return Cache::rememberForever("question_is_marked" . $question_id . $exam_id . $exam_result_id . $user_id, fn () => $model);
    }
}

if (!function_exists('int_to_abcd_value')) {
    function int_to_abcd_value($key)
    {
        $model = '';
        if ($key == 0) {
            $model = "A";
        } else if ($key == 1) {
            $model = "B";
        } else if ($key == 2) {
            $model = "C";
        } else if ($key == 3) {
            $model = "D";
        } else if ($key == 4) {
            $model = "E";
        } else if ($key == 5) {
            $model = "F";
        } else if ($key == 6) {
            $model = "G";
        } else if ($key == 7) {
            $model = "H";
        }
        return Cache::rememberForever("int_to_abcd_value" . $key, fn () => $model);
    }
}

if (!function_exists('answer_result_true_or_false')) {
    function answer_result_true_or_false($question_id, $value)
    {
        $model = null;
        if ($value != null) {
            $question = ExamQuestion::where("id", $question_id)->first();

            if ($question->type == 1) {
                if ($question->correctAnswer()->id == $value) {
                    $model = true;
                } else {
                    $model = false;
                }
            } else if ($question->type == 2) {
                if (!empty($question->correctAnswer()->where('id',$value)->first())) {
                    $model = true;
                } else {
                    $model = false;
                }
            } else if($question->type==3){
                if (strip_tags_with_whitespace($question->correctAnswer()->answer) == strip_tags_with_whitespace($value)) {
                    $model = true;
                } else {
                    $model = false;
                }
            } else if ($question->type == 4) {
            }
        }
        return Cache::rememberForever("answer_result_true_or_false" . $question_id . $value, fn () => $model);
    }
}

if (!function_exists('your_answer_result_true_or_false')) {
    function your_answer_result_true_or_false($question_id, $value, $result_id)
    {
        $model = null;
        $question_result = ExamResultAnswer::where("question_id", $question_id)
            ->where('result_id', $result_id)
            ->first();
        if (!empty($question_result)) {
            if($question_result->question->type==1){
                if ($question_result->answer_id == $value) {
                    $model = true;
                } else {
                    $model = false;
                }
            }else if($question_result->question->type==2){
                if (in_array($value,$question_result->answers)) {
                    $model = true;
                } else {
                    $model = false;
                }
            }else if($question_result->question->type==3){
                if (isset($question_result->value) && !empty($question_result->value)) {
                    $model = $question_result->value;
                } else {
                    $model = null;
                }
            }
        }
        return Cache::rememberForever("your_answer_result_true_or_false" . $question_id . $result_id . $value, fn () => $model);
    }
}

if (!function_exists('exam_result_answer_true_or_false')) {
    function exam_result_answer_true_or_false($question_id, $result_id)
    {
        $result = 'null';
        $model = ExamResultAnswer::where('question_id', $question_id)->where('result_id', $result_id)->first();
        if (!empty($model) && isset($model->id)) {
            if ($model->result == true) {
                $result = 'true';
            } else {
                $result = 'false';
            }
        }

        return Cache::rememberForever("exam_result_answer_true_or_false"  . $question_id . $result_id, fn () => $result);
    }
}

if (!function_exists('exam_for_profile')) {
    function exam_for_profile($type, $auth_id)
    {
        $model = Exam::orderBy('id', 'DESC');
        $user = users($auth_id, 'id');
        if ($user->user_type == 2)
            $model = $model->where("user_id", $auth_id);

        if ($type == "active") {
            $model = $model->whereHas('results', function ($query) {
                $query->orderBy("id", 'DESC');
                $query->whereBetween('created_at', [Carbon::now()->subDays(10), Carbon::now()]);
            });
        } else {
            $model = $model->whereHas('results', function ($query) {
                $query->orderBy("id", 'DESC');
                $query->whereBetween('created_at', [Carbon::now()->subDays(50), Carbon::now()->subDays(10)]);
            });
        }

        $model = $model->get();
        return Cache::rememberForever("exam_for_profile"  . $type . $auth_id, fn () => $model);
    }
}

if (!function_exists('create_dns_record')) {
    function create_dns_record($domain)
    {
        try {
            $recordType = 'A';
            $recordContent = '46.175.148.19';
            $url = "https://api.cloudflare.com/client/v4/zones/" . env('CL_ZN_ID') . "/dns_records";
            $client = new Client();
            $response = $client->request('POST', $url, [
                'headers' => [
                    'X-Auth-Email' => env('CL_AC_MAIL'),
                    'X-Auth-Key' => env('CL_API_TOKEN'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'type' => $recordType,
                    'name' => $domain,
                    'content' => $recordContent,
                    'proxied' => true,
                    'ttl' => 300
                ],
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                $responseData = json_decode($response->getBody(), true);
                purge_cache();
                return response()->json(['message' => 'DNS kaydı oluşturuldu.', 'data' => $responseData]);
            } else {
                \Log::info([
                    'CREATE DNS RECORD ERROR',
                    $statusCode
                ]);
                return response()->json(['error' => 'API isteği başarısız oldu. Durum Kodu:' . $statusCode]);
            }
        } catch (\Exception $e) {
            \Log::info([
                'CREATE DNS RECORD ERROR',
                $e->getMessage(),
                $e->getLine()
            ]);
        }
    }
}

if (!function_exists('purge_cache')) {
    function purge_cache()
    {
        try {
            $url = "https://api.cloudflare.com/client/v4/zones/" . env('CL_ZN_ID') . "/purge_cache";
            $client = new Client();
            $response = $client->request('DELETE', $url, [
                'headers' => [
                    'X-Auth-Email' => env('CL_AC_MAIL'),
                    'X-Auth-Key' => env('CL_API_TOKEN'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'purge_everything' => true,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                return response()->json(['message' => 'Önbellek temizlendi.']);
            } else {
                \Log::info([
                    'PURGE CACHE ERROR',
                    $statusCode
                ]);
                return response()->json(['error' => 'API isteği başarısız oldu. Durum Kodu:' . $statusCode]);
            }
        } catch (\Exception $e) {
            \Log::info([
                'PURGE CACHE ERROR',
                $e->getMessage(),
                $e->getLine()
            ]);
        }
    }
}

if (!function_exists('modifyRelativeUrlsToAbsolute')) {
    function modifyRelativeUrlsToAbsolute($content)
    {
        $domain = 'https://digitalexam.az';
        $pattern = '/<img.*?src=[\"\'](.*?)\.\.\/(.*?)["\']/';
        $replacement = '<img src="' . $domain . '/$2"';
        $modifiedContent = preg_replace($pattern, $replacement, $content);
        return $modifiedContent;
    }
}

if (!function_exists('calculate_exam_result')) {
    function calculate_exam_result($exam_result_id)
    {
        $examResult = ExamResult::where("id", $exam_result_id)->first();
        $exam = Exam::where('id', $examResult->exam_id)->first();
        $examsections = Section::where("exam_id", $exam->id);
        if (session()->has('selected_section')) {
            $examsections = $examsections->where('id', session()->get('selected_section'));
        }
        $examsections = $examsections->pluck('id');
        $examquestions = ExamQuestion::orderBy("id", 'DESC')->whereIn("section_id", $examsections)->get();
        $exampoint = $exam->point;
        $correctAnswers = $examResult->correctAnswers();
        $examquestionscount = count($examquestions);
        $model = 0;
        $model = ($correctAnswers / $examquestionscount) * $exampoint;
        return $model;
    }
}

if (!function_exists('exam_result')) {
    function exam_result($exam_id, $auth_id)
    {
        $model = ExamResult::orderBy('id', 'DESC');

        if (isset($auth_id) && !empty($auth_id)) {
            $user = users($auth_id, 'id');
        }

        if ($user->user_type == 2) {
            $model = Exam::orderBy('id', 'DESC');
        } else {
            $model = $model->where('user_id', $auth_id);
            $model = $model->where("point", '>', 0);
            $model = $model->where("time_reply", '>', 0);
            $model = $model->where("payed", 1);
        }

        if (isset($exam_id) && !empty($exam_id)) {
            if ($user->user_type == 2) {
                $model = $model->where("id", $exam_id);
            } else {
                $model = $model->where('exam_id', $exam_id);
            }
        }

        $model = $model->first();

        if ($user->user_type == 2 && $model->user_id==$user->id) {
            $model = !empty($model) && isset($model->slug) ? $model->slug : null;
        } else {
            $model = !empty($model) && isset($model->id) ? $model->id : null;
        }

        return Cache::rememberForever("exam_result"  . $exam_id . $auth_id, fn () => $model);
    }
}

if (!function_exists('get_answer_choised')) {
    function get_answer_choised($exam_results_ids,$question_id,$question_type,$value_id=null)
    {
        $model = ExamResultAnswer::orderBy('id', 'DESC')
        ->whereIn('result_id',$exam_results_ids)
        ->where('question_id',$question_id);

        if($question_type==1){
            $model=$model->where('answer_id',$value_id)
            ->whereNotNull("answer_id")
            ->whereNull('value');
        }else if($question_type==2){
            $model = $model->where(function ($query) use ($value_id) {
                $query->whereIn('answers', [$value_id])
                    ->whereNotNull("answers")
                    ->whereNull("answer_id")
                    ->whereNull('value');
            });
        }

        $model=$model->with('result_model')->get();

        return Cache::rememberForever("get_answer_choised"  . $exam_results_ids .$question_id.$question_type.$value_id, fn () => $model);
    }
}
