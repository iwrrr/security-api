<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Models\Satker;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SatkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $satker = Satker::all();

        if ($satker->isEmpty()) {
            return ResponseFormatter::success(data: $satker, message: 'Satker is empty');
        }

        return ResponseFormatter::success(data: $satker);
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
                'name' => ['required', 'string', 'max:100']
            ]);

            if ($validator->fails()) {
                return ResponseFormatter::error(message: $validator->errors()->first());
            }

            Satker::create($params);

            return ResponseFormatter::success(message: 'Satker created successfully');
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
        $satker = Satker::find($id);

        if (!$satker) {
            return ResponseFormatter::error(data: $satker, message: 'Satker not found', code: 404);
        }

        return ResponseFormatter::success(data: $satker);
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
            $satker = Satker::find($id);

            $validator = Validator::make($params, [
                'name' => ['required', 'string', 'max:100']
            ]);

            if (!$satker) {
                return ResponseFormatter::error(message: 'Satker not found', code: 404);
            }

            if ($validator->fails()) {
                return ResponseFormatter::error(message: $validator->errors()->first());
            }

            $satker->update($params);

            return ResponseFormatter::success(message: 'Satker updated successfully');
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
        $satker = Satker::find($id);

        if (!$satker) {
            return ResponseFormatter::error(data: $satker, message: 'Satker not found', code: 404);
        }

        $satker->delete();
        return ResponseFormatter::success(message: 'Satker deleted successfully');
    }
}
