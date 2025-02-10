<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\salecontroller;
Route::get('/', [salecontroller::class, 'home'])->name('home');
Route::get('/loginsale', [salecontroller::class, 'showLoginForm'])->name('sale.loginsale');
Route::post('/loginsale', [salecontroller::class, 'login'])->name('sale.loginsale');
Route::post('/logout', [salecontroller::class, 'logout'])->name('logout');
Route::get('/dashboard', [salecontroller::class, 'dashboard'])->name('sale.dashboard');
Route::get('/insertdata', [salecontroller::class, 'insertdata'])->name('sale.insertdata');
// Show the form (GET request)
Route::get('/sodetail', [SaleController::class, 'showForm'])->name('sodetail');

// Handle form submission (POST request)
Route::post('/sodetail', [SaleController::class, 'findData'])->name('sodetail.post');

Route::get('/txt', [SaleController::class, 'popup'])->name('popup');

use App\Http\Controllers\admincontroller;
Route::get('/dashboardadmin', [admincontroller::class, 'dashboard'])->name('admin.dashboardadmin');
Route::get('/history', [admincontroller::class, 'history'])->name('admin.history');