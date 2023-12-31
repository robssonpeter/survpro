<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SurveysController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return redirect('/login');
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    $surveys = \App\Models\Survey::orderBy('survey_id', 'DESC')->limit(5)->get();
    $survey_count = \App\Models\Survey::count();
    $responses_count = \App\Models\Response::/*groupBy('session_id')->*/count();
    return Inertia::render('Dashboard', compact('surveys', 'survey_count', 'responses_count'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * Questions related routes
     */
    Route::get('/admin/questions', [QuestionController::class, 'index'])->name('questions.index');
    Route::get('/admin/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/admin/question/store', [QuestionController::class, 'store'])->name('questions.store');
    Route::delete('/survey/questions/{id}', [QuestionController::class, 'deleteQuestion'])->name('questions.delete');


    /**
     * Surveys related routes
     */
    Route::get('/admin/surveys', [SurveysController::class, 'index'])->name('surveys.index');
    Route::get('/admin/survey/{slug}', [SurveysController::class, 'modify'])->name('survey.modify');
    Route::get('/admin/surveys/create', [SurveysController::class, 'create'])->name('surveys.create');
    Route::get('/admin/responses/{survey_id}', [SurveysController::class, 'responses'])->name('surveys.responses');
    Route::post('/admin/surveys', [SurveysController::class, 'store'])->name('surveys.store');
    Route::post('/admin/surveys/send', [SurveysController::class, 'sendSurvey'])->name('survey.send');
    Route::delete('/admin/surveys/{survey_id}', [SurveysController::class, 'delete'])->name('survey.delete');
    Route::post('/save-setting', [SettingsController::class, 'saveSetting'])->name('survey.setting.save');

    /**
     * Participants Related routes
     */
    Route::post('/saved/recipient-lists', [SurveysController::class, 'savedRecipientsList'])->name('saved.recipients-list');
// Add more routes for editing and deleting questions
});
Route::get('/user/survey/{slug}', [SurveysController::class, 'participate'])->name('survey.participate');
Route::post('/responses', [ResponseController::class, 'store'])->name('response.store');
Route::post('/submit-survey', [SurveysController::class, 'submitSurvey'])->name('survey.submit-responses');



require __DIR__.'/auth.php';
