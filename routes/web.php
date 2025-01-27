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

Route::get('/', [salecontroller::class, 'showLoginForm'])->name('sale.loginsale');
Route::post('/', [salecontroller::class, 'login'])->name('sale.loginsale');
Route::post('/logout', [salecontroller::class, 'logout'])->name('logout');
Route::get('/dashboard', [salecontroller::class, 'dashboard'])->name('sale.dashboard');
Route::get('/insertdata', [salecontroller::class, 'insertdata'])->name('sale.insertdata');

use App\Http\Controllers\admincontroller;
Route::get('/admin', [admincontroller::class, 'showLoginForm'])->name('admin.loginadmin');
Route::post('/admin', [admincontroller::class, 'login'])->name('admin.loginadmin');
Route::post('/logoutadmin', [admincontroller::class, 'logoutadmin'])->name('logoutadmin');
Route::get('/dashboardadmin', [admincontroller::class, 'dashboard'])->name('admin.dashboardadmin');