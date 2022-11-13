<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Helpers\Image;
use App\Models\Activity;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->user_id) {
            $activities = Activity::where('user_id', $request->user_id)->with(['user'])->get();
        } else {
            $activities = Activity::with(['user'])->get();
        }

        if ($activities->isEmpty()) {
            return ResponseFormatter::success(data: $activities, message: 'Activity is empty');
        }

        return ResponseFormatter::success(data: $activities);
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

            $imageHelper = Image::store(request: $request, folder: 'activities');

            $params['user_id'] = $request->user()->id;
            $params['image'] = "uploads/activities/{$imageHelper['file_name']}";

            Activity::create($params);

            return ResponseFormatter::success(message: 'Activity created successfully');
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
        $activity = Activity::with(['user', 'comments'])->find($id);

        if (!$activity) {
            return ResponseFormatter::error(data: $activity, message: 'Activity not found', code: 404);
        }

        return ResponseFormatter::success(data: $activity);
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
            $activity = Activity::find($id);

            $validator = Validator::make($params, [
                'title' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string']
            ]);

            if (!$activity) {
                return ResponseFormatter::error(message: 'Activity not found', code: 404);
            }

            if ($validator->fails()) {
                return ResponseFormatter::error(message: $validator->errors()->first());
            }

            if (!$request->hasFile('image')) {
                $params['image'] = $activity->image;
            } else {
                $path = public_path($activity->image);
                if (File::exists($path)) {
                    unlink($path);
                }
                $imageHelper = Image::store(request: $request, folder: 'activities');
                $params['image'] = "uploads/activities/{$imageHelper['file_name']}";
            }

            $activity->update($params);

            return ResponseFormatter::success(message: 'Activity updated successfully');
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
        $activity = Activity::find($id);

        if (!$activity) {
            return ResponseFormatter::error(data: $activity, message: 'Activity not found', code: 404);
        }

        $activity->delete();
        return ResponseFormatter::success(message: 'Activity deleted successfully');
    }

    public function addComment(Request $request)
    {
        try {
            $params = $request->all();
            $activity = Activity::find($params['activity_id']);

            $params['user_id'] = $request->user()->id;

            $validator = Validator::make($params, [
                'text' => ['required', 'string']
            ]);

            if (!$activity) {
                return ResponseFormatter::error(message: 'Activity not found', code: 404);
            }

            if ($validator->fails()) {
                return ResponseFormatter::error(message: $validator->errors()->first());
            }

            Comment::create($params);

            return ResponseFormatter::success(message: 'Comment added successfully');
        } catch (Throwable $th) {
            return ResponseFormatter::error(message: "{$th}");
        }
    }
}
