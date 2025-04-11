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

// SO system
use App\Models\Bill_Detail;
    use App\Http\Controllers\salecontroller;
    Route::get('/', [salecontroller::class, 'home'])->name('home');
    Route::get('/loginsale', [salecontroller::class, 'showLoginForm'])->name('sale.loginsale');
    Route::post('/loginsale', [salecontroller::class, 'login'])->name('sale.loginsale');
    Route::post('/logout', [salecontroller::class, 'logout'])->name('logout');
    Route::get('/dashboard', [SaleController::class, 'dashboard'])->name('sale.dashboard');
    Route::get('/insertdata', [salecontroller::class, 'insertdata'])->name('sale.insertdata'); // GET
    Route::post('/insertdata', [salecontroller::class, 'insertData'])->name('sale.insertdata.post'); // POST
    Route::post('/sodetail', [SaleController::class, 'findData'])->name('sodetail.post');
    Route::get('/sodetail', [SaleController::class, 'showForm'])->name('sodetail');
    Route::post('/insert', [SaleController::class, 'insert'])->name('insert.post');
    Route::get('/get-details/{id}', [saleController::class, 'getDetails']);
    Route::get('/sale/modifydata/{id}', [salecontroller::class, 'modifyData'])->name('sale.modifydata');
    Route::any('/update-bill', [salecontroller::class, 'updateBill']);
    Route::delete('/delete-bill/{so_detail_id}', [salecontroller::class, 'deleteBill']);





    Route::get('/get-bill-detail/{so_detail_id}', function ($so_detail_id) {
        $billDetails = Bill_Detail::where('so_detail_id', $so_detail_id)->get();
        return response()->json($billDetails);
    });
Route::get('/txt', [SaleController::class, 'popup'])->name('popup');


use App\Http\Controllers\PoDocumentController;
Route::get('/add-so-detail-id-to-pdf/{soDetailId}/{POdocument}', 
    [PoDocumentController::class, 'addSoDetailIdToPoDocument']);
Route::get('/add-so-detail-id-to-bill/{so_detail_id}/{filename}', 
    [PoDocumentController::class, 'addIdToDocument']);

    

use App\Http\Controllers\admincontroller;
Route::get('/dashboardadmin', [AdminController::class, 'dashboard'])->name('admin.dashboardadmin');
Route::get('/history', [AdminController::class, 'history'])->name('admin.history');
Route::get('/admin/get-bill-detail/{so_detail_id}', [AdminController::class, 'getBillDetail']);
Route::post('/update-status', [admincontroller::class, 'updateStatus']);
Route::get('/dashboardadminpdf', [AdminController::class, 'dashboardpdf'])->name('admin.dashboardadminpdf');
Route::post('/update-statuspdfso', [admincontroller::class, 'updateStatuspdf']);
Route::post('/update-billid', [AdminController::class, 'updateBillId'])->name('update.billid');




// PO system
use App\Http\Controllers\PoController;
Route::get('/dashboardpo', [PoController::class, 'dashboardpo'])->name('po.dashboardpo');
Route::get('/insertpo', [PoController::class, 'insertpo'])->name('po.insertpo');
Route::post('/insertpo', [PoController::class, 'insertpobill'])->name('insertpo.post'); 
Route::get('/get-pobill-detail/{po_detail_id}', [PoController::class, 'getpoBillDetail'])
     ->name('getpoBillDetail');


use App\Http\Controllers\adminpocontroller;
Route::get('/adminpo', [adminpoController::class, 'dashboard'])->name('po.adminpo');
Route::get('/historypo', [AdminpoController::class, 'historypo'])->name('po.historypo');
Route::post('/update-statuspo', [adminpocontroller::class, 'updateStatus']);


// Doc system
use App\Http\Controllers\DocController;
Route::get('/dashboarddoc', [DocController::class, 'dashboarddoc'])->name('document.dashboarddoc');
Route::get('/insertdoc', [DocController::class, 'insertdoc'])->name('document.insertdoc');
Route::post('/insertdocu', [DocController::class, 'insertDocu'])->name('insertdocu');
Route::get('/get-docbill-detail/{doc_id}', [DocController::class, 'getdocBillDetail'])->name('getdocBillDetail');





use App\Http\Controllers\admindoccontroller;
Route::get('/admindoc', [admindoccontroller::class, 'dashboarddoc'])->name('document.admindoc');
Route::get('/historydoc', [admindoccontroller::class, 'historydoc'])->name('ducument.historydoc');
Route::post('/update-statusdoc', [admindoccontroller::class, 'updateStatus']);



//test 
use App\Http\Controllers\text;
Route::get('/SOlist', [text::class, 'txt1'])->name('txt1');
Route::get('/insertSO', [text::class, 'txt2'])->name('txt2');
Route::get('/adminSO', [text::class, 'txt3'])->name('txt3');