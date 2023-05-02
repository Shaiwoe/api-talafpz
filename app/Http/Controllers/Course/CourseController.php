<?php

namespace App\Http\Controllers\Course;

use Carbon\Carbon;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Course\CourseResource;
use App\Models\Skill;

class CourseController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $course = Course::orderBy('created_at', 'desc')->paginate(5);

        return $this->successResponse([
            'courses' => CourseResource::collection($course),
            'links' => CourseResource::collection($course)->response()->getData()->links,
            'meta' => CourseResource::collection($course)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'skill_id' => 'required|integer',
            'slug' => 'required',
            'type' => 'required',
            'description' => 'required',
            'body' => 'required',
            'image' => 'required|image',
            'tags' => 'required',
            'timeCourse' => 'required',
            'condition' => 'required',
            'status' => 'required',
            'price' => 'required',
            'quantity' => 'required',
            'sale_price' => 'nullable|integer',
            'date_on_sale_from' => 'nullable|date_format:Y-m-d H:i:s',
            'date_on_sale_to' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        if ($request->has('image') && $request->image !== null) {
            $imageName = Carbon::now()->microsecond . '.' . $request->image->extension();
            $request->image->storeAs('course/image', $imageName, 'public');
        }

        $course = Course::create([
            'title' => $request->title,
            'skill_id' => $request->skill_id,
            'slug' => $request->slug,
            'type' => $request->type,
            'description' => $request->description,
            'body' => $request->body,
            'image' => $request->image !== null ? $imageName : $course->image,
            'tags' => $request->tags,
            'timeCourse' => $request->timeCourse,
            'condition' => $request->condition,
            'status' => $request->status,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'sale_price' => $request->sale_price !== null ? $request->sale_price : 0,
            'date_on_sale_from' => $request->date_on_sale_from,
            'date_on_sale_to' => $request->date_on_sale_to,
        ]);

        DB::commit();

        return $this->successResponse(new CourseResource($course), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return $this->successResponse(new CourseResource($course));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'skill_id' => 'required|integer',
            'slug' => 'required',
            'type' => 'required',
            'description' => 'required',
            'body' => 'required',
            'image' => 'nullable|image',
            'tags' => 'required',
            'status' => 'required',
            'timeCourse' => 'required',
            'condition' => 'required',
            'price' => 'required',
            'quantity' => 'required',
            'sale_price' => 'nullable|integer',
            'date_on_sale_from' => 'nullable|date_format:Y-m-d H:i:s',
            'date_on_sale_to' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        if ($request->has('image') && $request->image !== null) {
            $imageName = Carbon::now()->microsecond . '.' . $request->image->extension();
            $request->image->storeAs('course/image', $imageName, 'public');
        }

        $course->update([
            'title' => $request->title,
            'skill_id' => $request->skill_id,
            'slug' => $request->slug,
            'type' => $request->type,
            'description' => $request->description,
            'body' => $request->body,
            'image' => $request->image !== null ? $imageName : $course->image,
            'tags' => $request->tags,
            'timeCourse' => $request->timeCourse,
            'condition' => $request->condition,
            'status' => $request->status,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'sale_price' => $request->sale_price !== null ? $request->sale_price : 0,
            'date_on_sale_from' => $request->date_on_sale_from !== null ? $request->date_on_sale_from : $course->date_on_sale_from,
            'date_on_sale_to' => $request->date_on_sale_to !== null ? $request->date_on_sale_to : $course->date_on_sale_to
        ]);

        DB::commit();

        return $this->successResponse(new CourseResource($course), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        DB::beginTransaction();
        $course->delete();
        DB::commit();
        return $this->successResponse(new CourseResource($course), 200);
    }

    public function episodes(Course $course)
    {
        return $this->successResponse(new CourseResource($course->load('episodes')), 200);
    }


}
