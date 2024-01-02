<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoutesController extends Controller
{
    public function standartpage($slug)
    {
        if ($slug != "contactus") {
            $data = standartpages($slug, 'slug');
            return view("frontend.pages.standartpage", compact("data"));
        } else {
            return view("frontend.pages.contactus");
        }
    }
    public function welcome()
    {
        try {
            return view('frontend.welcome');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function exams(Request $request)
    {
        try {
            $data = collect();
            $categories = collect();
            $category = collect();
            $subdomain = Session::get("subdomain")??null;

            if (isset($request->slug) && !empty($request->slug)) {
                $category = Category::where('slugs->az_slug', $request->slug)
                    ->orWhere('slugs->ru_slug', $request->slug)
                    ->orWhere('slugs->en_slug', $request->slug)
                    ->orWhere('slugs->tr_slug', $request->slug)
                    ->orderBy('id', 'DESC')
                    ->first();

                if (!empty($category->exams)) {
                    $data = $category->exams;
                }

                if (!empty($subdomain)) {
                    $data = $data->where('user_id', settings($subdomain)->id)->get();
                }

                if (!empty($category->sub)) {
                    foreach ($category->sub as $sub) {
                        $categories->push($sub);
                    }
                }
            } else {
                if (!empty($subdomain))
                    $data = exams(settings($subdomain)->id,'subdomain');
                else
                    $data = exams();
            }

            return view('frontend.exams.index', compact('data', 'categories', 'category'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function examinfo(Request $request, $slug)
    {
        try {
            $data = collect();
            if (is_numeric($slug) && preg_match('/^\d+$/', $slug)) {
                $data = exams($slug, 'id');
            } else {
                $data = exams($slug, 'slug');
            }
            return view('frontend.exams.show', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function blogs_front(Request $request, $slug)
    {
        try {
            $data = blogs($slug);
            return view('frontend.pages.blogs.show', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function teams_front(Request $request, $slug)
    {
        try {
            $data = teams($slug);
            return view('frontend.pages.teams.show', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function search(Request $request)
    {
        try {
            $key = $request->input("search");
            $data = Exam::whereRaw('name like ?', ['%' . $key . '%'])->orWhereRaw('content like ?', ['%' . $key . '%'])->orderBy("order_number", 'ASC')->get();

            return view("frontend.exams.search", compact("data"));
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect('/notfound')->with("error", $e->getMessage());
        }
    }
}
