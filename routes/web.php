<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectWBSController;
use App\Http\Controllers\DeliverableController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\WorkAmountController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\CandidatesController;
use App\Http\Controllers\SectionTitleController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ProjectResourceController;
use App\Http\Controllers\ResourceTypeController;
use App\Http\Controllers\SupplierController;

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
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function(){
    
    Route::resource('projects', ProjectController::class);
    
    Route::resource('projects.wbs', ProjectWBSController::class)->parameters([
        'wbs' => 'wbs'
    ])->scoped();
    
    Route::resource('projects.deliverables', DeliverableController::class);
    
    Route::resource('/resources_types/', ResourceTypeController::class);
    
    Route::get('/projects/{project}/resources/equipment', 
            [ProjectResourceController::class, 'index'])
            ->name('projectEquipment');
    
    Route::get('/projects/{project}/resources/equipment/assign',
        [ProjectResourceController::class, 'chooseEquipment']);
    
    Route::post('/projects/{project}/resources/equipment', 
            [ProjectResourceController::class, 'assignEquipmentToProject']);
            
    Route::post('/projects/{project}/team', [TeamController::class, 'store']);
    
    Route::get('/projects/{project}/team', [TeamController::class, 'index']);
    
    Route::get('/projects/{project}/team/edit', [TeamController::class, 'edit']);
    
    Route::resource('statuses', StatusController::class);
    
    Route::resource('work_units', WorkAmountController::class);
    
});

Route::prefix('admin')->middleware(['middleware'=>'auth'])->group(function () {

    Route::resource('sections', SectionTitleController::class)->parameters([
        'section' => 'section'
    ]);
});


Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


Route::group(['middleware'=>'auth'], function(){
    
    Route::resource('suppliers', SupplierController::class)->names([
        'show' => 'supplier'
    ]);
    
    Route::resource('candidates', CandidatesController::class)->parameters([
        'candidate' => 'candidate'
    ])->scoped();
    
    Route::resource('equipment', EquipmentController::class);

});

