<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\DetailResepController;
use Illuminate\Support\Facades\Route;


Route::post('/login',[AuthController::class,'login']);


Route::get('/publik/dokumen/{kode_rm}', [PasienController::class, 'getDokumenPublik']);

Route::middleware(['auth:api'])->group(function(){
    // PROFILE
    Route::get('/profile', function () {
        return response()->json(auth('api')->user());
    });

    Route::post('/profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware(['role:admin,dokter'])->group(function(){
        Route::apiResource('/pasiens', PasienController::class)->only(['show','index']);
        Route::apiResource('/rekam-medis', RekamMedisController::class)->only(['show','index']);
        Route::apiResource('/pemeriksaan', PemeriksaanController::class)->only(['show','index','store','update','destroy']);
        Route::apiResource('/obat', ObatController::class)->only(['show','index']);
        Route::apiResource('/resep', ResepController::class)->only(['show','index','store','update']);
        Route::apiResource('/transaksi', TransaksiController::class)->only(['show','index','store']);
        Route::apiResource('/detail-resep', DetailResepController::class)->only(['show','index','store','update']);
    });

    Route::middleware(['role:admin'])->group(function(){
        Route::apiResource('/pasiens', PasienController::class)->only(['destroy','update','store']);
        Route::apiResource('/rekam-medis', RekamMedisController::class)->only(['destroy','store','update']);
        Route::apiResource('/obat', ObatController::class)->only(['destroy','store','update']);
        Route::apiResource('/resep', ResepController::class)->only(['destroy','update']);
        Route::apiResource('/transaksi', TransaksiController::class)->only(['destroy','update']);
        Route::apiResource('/detail-resep', DetailResepController::class)->only(['destroy']);
        Route::apiResource('/petugas', AuthController::class);
    });

});