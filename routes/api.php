<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\Skill\SkillController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Article\ArticleController;
use App\Http\Controllers\Contact\ContactController;
use App\Http\Controllers\Coupone\CouponeController;
use App\Http\Controllers\Episode\EpisodeController;
use App\Http\Controllers\Payment\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
Route::post('/auth/check-otp', [AuthController::class, 'checkOtp']);
Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);

Route::group(['middleware' => ['auth:sanctum']], function() {

    Route::post('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

});

Route::apiResource('articles' , ArticleController::class);
Route::apiResource('skills' , SkillController::class);
Route::get('/skills/{skill}/courses' , [SkillController::class , 'courses']);
Route::apiResource('courses' , CourseController::class);
Route::get('/courses/{course}/episodes' , [CourseController::class , 'episodes']);
Route::apiResource('episodes' , EpisodeController::class);
Route::apiResource('coupones' , CouponeController::class);
Route::post('/payment/send' , [PaymentController::class , 'send']);
Route::post('/payment/verify', [PaymentController::class, 'verify']);

Route::get('/index', [HomeController::class, 'show']);
Route::get('/lastCourse', [CourseController::class, 'lastCourse']);

Route::apiResource('contact' , ContactController::class);

////////////////////// Admin Paanel ////////////////////////
Route::group(['middleware' => ['auth:sanctum']], function() {
    // Route::get('/categories/{category}/children', [CategoryController::class, 'children']);
    // Route::get('/categories/{category}/parent', [CategoryController::class, 'parent']);
    // Route::get('/categories/{category}/products', [CategoryController::class, 'products']);
    // Route::get('/categories-list', [CategoryController::class, 'listAdmin']);

    // Route::apiResource('products', ProductController::class);

    // Route::get('/transactions/chart', [TransactionController::class, 'chart']);
    // Route::get('/transactions', [TransactionController::class, 'index']);


    // Route::apiResource('coupons', CouponController::class);

    // Route::apiResource('orders', OrderController::class);

    Route::apiResource('admin-panel/users', UserController::class);
    Route::apiResource('admin-panel/categories', CategoryArticleController::class);

    Route::apiResource('admin-panel/articles' , ArticleController::class);

    Route::post('admin-panel/auth/register', [AuthController::class, 'register']);
    Route::post('admin-panel/auth/me', [AuthController::class, 'me']);
    Route::post('admin-panel/auth/logout', [AuthController::class, 'logoutAdmin']);
});

Route::post('/admin-panel/auth/login', [AuthController::class, 'loginAdmin']);
