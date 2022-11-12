<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Helpers\Image;
use App\Models\Article;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getArticles()
    {
        $articles = Article::all();

        if ($articles->isEmpty()) {
            return ResponseFormatter::error(data: $articles, message: 'Article not found', code: 404);
        }

        return ResponseFormatter::success(data: $articles);
    }

    /**
     * Display a random resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getHeadlineArticles()
    {
        $articles = Article::all();

        if ($articles->isEmpty()) {
            return ResponseFormatter::error(data: $articles, message: 'Article not found', code: 404);
        }

        if ($articles->count() >= 5) {
            $articles = $articles->random(5);
            return ResponseFormatter::success(data: $articles);
        }

        return ResponseFormatter::success(data: $articles);
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

            $imageHelper = Image::store(request: $request, folder: 'articles');

            $params['user_id'] = $request->user()->id;
            $params['image'] = "uploads/articles/{$imageHelper['file_name']}";

            Article::create($params);

            return ResponseFormatter::success(message: 'Article created successfully');
        } catch (Throwable $th) {
            return ResponseFormatter::error(message: $th);
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
        $article = Article::with(['user', 'comments'])->find($id);

        if (!$article) {
            return ResponseFormatter::error(data: $article, message: 'Article not found', code: 404);
        }

        return ResponseFormatter::success(data: $article);
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
            $article = Article::findOrFail($id);
            $params = $request->all();

            $validator = Validator::make($params, [
                'title' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string']
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(message: $validator->errors()->first());
            }

            if (!$request->hasFile('image')) {
                $params['image'] = $article->image;
            } else {
                $path = public_path($article->image);
                if (File::exists($path)) {
                    unlink($path);
                }
                $imageHelper = Image::store(request: $request, folder: 'articles');
                $params['image'] = "uploads/articles/{$imageHelper['file_name']}";
            }

            $article->update($params);

            return ResponseFormatter::success(message: 'Article updated successfully');
        } catch (Throwable $th) {
            return ResponseFormatter::error(message: $th);
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
        $article = Article::find($id);

        if (!$article) {
            return ResponseFormatter::error(data: $article, message: 'Article not found', code: 404);
        }

        $article->delete();
        return ResponseFormatter::success(message: 'Article deleted successfully');
    }
}
