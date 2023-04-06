<?php

namespace App\Http\Controllers\Skill;

use App\Http\Controllers\ApiController;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Skill\SkillResource;
use Illuminate\Support\Facades\Validator;

class SkillController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $skill = Skill::orderBy('created_at', 'desc')->paginate(5);

        return $this->successResponse([
            'skill' => SkillResource::collection($skill),
            'links' => SkillResource::collection($skill)->response()->getData()->links,
            'meta' => SkillResource::collection($skill)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'slug' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();



        $skill = Skill::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'status' => $request->status,
            'description' => $request->description,
            'icon' => $request->icon,
        ]);

        DB::commit();

        return $this->successResponse(new SkillResource($skill), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Skill $skill)
    {
        return $this->successResponse(new SkillResource($skill));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skill $skill)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'slug' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();



        $skill->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'status' => $request->status,
            'description' => $request->description,
            'icon' => $request->icon,
        ]);

        DB::commit();

        return $this->successResponse(new SkillResource($skill), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        DB::beginTransaction();
        $skill->delete();
        DB::commit();
        return $this->successResponse(new SkillResource($skill), 200);
    }

    public function courses(Skill $skill)
    {
        return $this->successResponse(new SkillResource($skill->load('courses')), 200);
    }
}

