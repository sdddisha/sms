<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('login');
});



Route::prefix('admin')->group(function () {
    Route::get('users',[AdminController::class,'user']);
    Route::post('users',[AdminController::class,'store_userdata']);
    Route::get('login',[AdminController::class,'login']);
    Route::post('login',[AdminController::class,'check_login']);
    Route::get('dashboard',[AdminController::class,'dashboard']);
    Route::get('logout',[AdminController::class,'logout'])->name('logout');
    Route::get('course',[AdminController::class,'course']);
    Route::post('course',[AdminController::class,'add_course']);
    Route::get('teacher',[AdminController::class,'teacher']);
    Route::get('edit/{id}',[AdminController::class, 'teacher_edit']);
    Route::post('update/{id}',[AdminController::class, 'teacher_update']);
    Route::get('delete/{id}',[AdminController::class, 'destroy']);
    Route::get('manage_course',[AdminController::class,'manage_course']);
    Route::get('course_edit/{id}',[AdminController::class, 'course_edit']);
    Route::post('course_update/{id}',[AdminController::class, 'course_update']);
    Route::get('course_delete/{id}',[AdminController::class, 'course_destroy']);
    Route::get('add_announ',[AdminController::class,'add_announ']);
    Route::post('announcement',[AdminController::class,'post_announcement']);
    Route::get('manage_announ',[AdminController::class,'manage_announ']);
    Route::get('announ_edit/{id}',[AdminController::class, 'announ_edit']);
    Route::post('announ_update/{id}',[AdminController::class, 'announ_update']);
    Route::get('announ_delete/{id}',[AdminController::class, 'announ_destroy']);
    Route::get('see_assign',[AdminController::class,'see_assign']);
});

Route::prefix('teacher')->group(function () {
    Route::get('login',[TeacherController::class,'index'])->name('teacher/login'); 
    Route::post('login',[TeacherController::class,'check_login']);
    Route::get('dashboard',[TeacherController::class,'dashboard']);
    Route::get('logout',[TeacherController::class,'logout'])->name('logout');
    Route::get('announcement',[TeacherController::class,'view_announcement']);
    Route::post('announcement',[TeacherController::class,'post_announcement']);
    Route::get('see_announ',[TeacherController::class,'see_announ']);
    Route::get('upload_assign',[TeacherController::class,'upload_assign']);
    Route::post('upload_assign',[TeacherController::class,'store_assign']);
    Route::get('see_assign',[TeacherController::class,'see_assign']);
    Route::get('check_assign/{id}',[TeacherController::class,'check_assign']);
    Route::get('uncheck_assign/{id}',[TeacherController::class,'uncheck_assign']);
    Route::get('forget',[TeacherController::class,'forget']);
    Route::post('forget', [TeacherController::class, 'submitForgetPasswordForm']); 
    Route::get('reset-password/{token}', [TeacherController::class, 'showResetPasswordForm']);
    Route::post('reset-password', [TeacherController::class, 'submitResetPasswordForm']);

   
});

Route::prefix('student')->group(function () {
    Route::get('login',[StudentController::class,'index'])->name('student/login');  
    Route::post('login',[StudentController::class,'check_login']);
    Route::get('dashboard',[StudentController::class,'dashboard']);
    Route::get('logout',[StudentController::class,'logout'])->name('logout');
    Route::get('show_assign',[StudentController::class,'show_assign']);
    Route::get('show_announ',[StudentController::class,'show_announ']);
    Route::get('assign_status',[StudentController::class,'assign_status']);
    Route::get('upload/{id}',[StudentController::class,'upload_assign']);
    Route::post('upload',[StudentController::class,'store_assign']);
    Route::get('forget',[StudentController::class,'forget']);
    Route::post('forget', [StudentController::class, 'submitForgetPasswordForm']); 
    Route::get('reset-password/{token}', [StudentController::class, 'showResetPasswordForm']);
    Route::post('reset-password', [StudentController::class, 'submitResetPasswordForm']);
    
});
