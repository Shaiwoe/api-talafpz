<?php

namespace App\Http\Controllers\Episode;

use Carbon\Carbon;
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Episode\EpisodeResource;

class EpisodeController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $episode = Episode::orderBy('created_at', 'desc')->paginate(5);

        return $this->successResponse([
            'articles' => EpisodeResource::collection($episode),
            'links' => EpisodeResource::collection($episode)->response()->getData()->links,
            'meta' => EpisodeResource::collection($episode)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Episode $episode)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'course_id' => 'required|integer',
            'slug' => 'required',
            'type' => 'required',
            'description' => 'required',
            'body' => 'required',
            'video' => 'required',
            'tags' => 'required',
            'status' => 'required',
            'time' => 'required',
            'number' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        if ($request->has('video') && $request->video !== null) {
            $videoName = Carbon::now()->microsecond . '.' . $request->video->extension();
            $request->video->storeAs('episode/video', $videoName, 'public');
        }

        $episode = Episode::create([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'slug' => $request->slug,
            'type' => $request->type,
            'description' => $request->description,
            'body' => $request->body,
            'video' => $request->video !== null ? $videoName : $episode->video,
            'tags' => $request->tags,
            'status' => $request->status,
            'time' => $request->time,
            'number' => $request->number,

        ]);

        DB::commit();

        return $this->successResponse(new EpisodeResource($episode), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Episode $episode)
    {
        return $this->successResponse(new EpisodeResource($episode));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Episode $episode)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'course_id' => 'required|integer',
            'slug' => 'required',
            'type' => 'required',
            'description' => 'required',
            'body' => 'required',
            'video' => 'nullable',
            'tags' => 'required',
            'status' => 'required',
            'time' => 'required',
            'number' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        if ($request->has('video') && $request->video !== null) {
            $videoName = Carbon::now()->microsecond . '.' . $request->video->extension();
            $request->video->storeAs('episode/video', $videoName, 'public');
        }

        $episode->update([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'slug' => $request->slug,
            'type' => $request->type,
            'description' => $request->description,
            'body' => $request->body,
            'video' => $request->video !== null ? $videoName : $episode->video,
            'tags' => $request->tags,
            'status' => $request->status,
            'time' => $request->time,
            'number' => $request->number,

        ]);

        DB::commit();

        return $this->successResponse(new EpisodeResource($episode), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Episode $episode)
    {
        DB::beginTransaction();
        $episode->delete();
        DB::commit();
        return $this->successResponse(new EpisodeResource($episode), 200);
    }
}
