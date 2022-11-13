<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Models\News;
use App\Helpers\Image;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $news = News::orderBy('updated_at', 'desc')->get();

        if ($news->isEmpty()) {
            return ResponseFormatter::success(data: $news, message: 'News is empty');
        }

        if ($news->count() >= 5) {
            $news = $news->random(5);
            return ResponseFormatter::success(data: $news);
        }

        return ResponseFormatter::success(data: $news);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $params = $request->all();
            $validator = Validator::make($params, [
                'title' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string'],
                'image' => ['required', 'mimes:png,jpg,jpeg', 'max:2048'],
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(message: $validator->errors()->first());
            }

            $imageHelper = Image::store(request: $request, folder: 'news');

            $params['image'] = "uploads/news/{$imageHelper['file_name']}";

            News::create($params);

            return ResponseFormatter::success(message: 'News created successfully');
        } catch (Throwable $th) {
            return ResponseFormatter::error(message: "{$th}");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $news = News::find($id);

        if (!$news) {
            return ResponseFormatter::error(data: $news, message: 'News not found', code: 404);
        }

        return ResponseFormatter::success(data: $news);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $params = $request->all();
            $news = News::find($id);

            $validator = Validator::make($params, [
                'title' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string'],
            ]);

            if (!$news) {
                return ResponseFormatter::error(message: 'News not found', code: 404);
            }

            if ($validator->fails()) {
                return ResponseFormatter::error(message: $validator->errors()->first());
            }

            if (!$request->hasFile('image')) {
                $params['image'] = $news->image;
            } else {
                $path = public_path($news->image);
                if (File::exists($path)) {
                    unlink($path);
                }
                $imageHelper = Image::store(request: $request, folder: 'news');
                $params['image'] = "uploads/news/{$imageHelper['file_name']}";
            }

            $news->update($params);

            return ResponseFormatter::success(message: 'News updated successfully');
        } catch (Throwable $th) {
            return ResponseFormatter::error(message: "{$th}");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $news = News::find($id);

        if (!$news) {
            return ResponseFormatter::error(data: $news, message: 'News not found', code: 404);
        }

        $news->delete();
        return ResponseFormatter::success(message: 'News deleted successfully');
    }
}
