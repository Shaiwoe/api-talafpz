<?php

namespace App\Http\Controllers\Article;

use Carbon\Carbon;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Article\ArticleResource;

class ArticleController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $article = Article::orderBy('created_at', 'desc')->paginate(5);

        return $this->successResponse([
            'articles' => ArticleResource::collection($article),
            'links' => ArticleResource::collection($article)->response()->getData()->links,
            'meta' => ArticleResource::collection($article)->response()->getData()->meta,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Article $article)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'slug' => 'required',
            'description' => 'required',
            'body' => 'required',
            'image' => 'required|image',
            'video' => 'nullable|video',
            'voice' => 'nullable|voice',
            'tags' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        if ($request->has('image') && $request->image !== null) {
            $imageName = Carbon::now()->microsecond . '.' . $request->image->extension();
            $request->image->storeAs('article/image', $imageName, 'public');
        }

        if ($request->has('video') && $request->video !== null) {
            $videoName = Carbon::now()->microsecond . '.' . $request->video->extension();
            $request->video->storeAs('article/video', $videoName, 'public');
        }

        if ($request->has('voice') && $request->voice !== null) {
            $voiceName = Carbon::now()->microsecond . '.' . $request->voice->extension();
            $request->voice->storeAs('article/voice', $voiceName, 'public');
        }

        $article = Article::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'description' => $request->description,
            'body' => $request->body,
            'image' => $request->image !== null ? $imageName : $article->image,
            'video' => $request->video !== null ? $videoName : $article->video,
            'voice' => $request->voice !== null ? $voiceName : $article->voice,
            'tags' => $request->tags,
            'status' => $request->status,
        ]);

        DB::commit();

        return $this->successResponse(new ArticleResource($article), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return $this->successResponse(new ArticleResource($article));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'slug' => 'required',
            'description' => 'required',
            'body' => 'required',
            'image' => 'nullable|image',
            'video' => 'nullable|video',
            'voice' => 'nullable|voice',
            'tags' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        if ($request->has('image') && $request->image !== null) {
            $imageName = Carbon::now()->microsecond . '.' . $request->image->extension();
            $request->image->storeAs('article/image', $imageName, 'public');
        }

        if ($request->has('video') && $request->video !== null) {
            $videoName = Carbon::now()->microsecond . '.' . $request->video->extension();
            $request->video->storeAs('article/video', $videoName, 'public');
        }

        if ($request->has('voice') && $request->voice !== null) {
            $voiceName = Carbon::now()->microsecond . '.' . $request->voice->extension();
            $request->voice->storeAs('article/voice', $voiceName, 'public');
        }

        $article->update([
            'title' => $request->title,
            'slug' => $request->slug,
            'description' => $request->description,
            'body' => $request->body,
            'image' => $request->image !== null ? $imageName : $article->image,
            'video' => $request->video !== null ? $videoName : $article->video,
            'voice' => $request->voice !== null ? $voiceName : $article->voice,
            'tags' => $request->tags,
            'status' => $request->status,
        ]);

        DB::commit();

        return $this->successResponse(new ArticleResource($article), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        DB::beginTransaction();
        $article->delete();
        DB::commit();
        return $this->successResponse(new ArticleResource($article), 200);
    }
}
