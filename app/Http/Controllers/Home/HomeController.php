<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\ApiController;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseResource;

class HomeController extends ApiController
{
    public function show()
    {
        $course = Course::orderBy('created_at', 'desc')->where('status' , 1)->take(3)->get();

        return $this->successResponse([
            'last' => CourseResource::collection($course),
        ]);
    }
}
