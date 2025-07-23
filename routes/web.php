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
    Route::post('/fetch-formtype', [salecontroller::class, 'fetchFormType']);
    Route::post('/fetch-contactso', [salecontroller::class, 'fetchContactSo']);
    Route::get('/get-bill-detail/{so_detail_id}', function ($so_detail_id) {
        $billDetails = Bill_Detail::where('so_detail_id', $so_detail_id)->get();
        return response()->json($billDetails);});
Route::get('/txt', [SaleController::class, 'popup'])->name('popup');
Route::post('/check-billid', [salecontroller::class, 'checkBillId'])->name('check.billid');


use App\Http\Controllers\PoDocumentController;
Route::get('/add-so-detail-id-to-pdf/{soDetailId}/{POdocument}', 
    [PoDocumentController::class, 'addSoDetailIdToPoDocument']);
Route::get('/add-so-detail-id-to-bill/{so_detail_id}/{filename}', 
    [PoDocumentController::class, 'addIdToDocument']);
Route::get('/add-so-detail-id-to-billissue/{so_detail_id}/{bill_issue_no}', 
    [PoDocumentController::class, 'addIdToissueDocument']);


use App\Http\Controllers\admincontroller;
Route::get('/dashboardadmin', [AdminController::class, 'dashboard'])->name('admin.dashboardadmin');
Route::get('/history', [AdminController::class, 'history'])->name('admin.history');
Route::get('/admin/get-bill-detail/{so_detail_id}', [AdminController::class, 'getBillDetail']);
Route::post('/update-status', [admincontroller::class, 'updateStatus']);
Route::get('/dashboardadminpdf', [AdminController::class, 'dashboardpdf'])->name('admin.dashboardadminpdf');
Route::post('/update-statuspdfso', [admincontroller::class, 'updateStatuspdf']);
Route::post('/update-statuspdfsoback', [admincontroller::class, 'updateStatuspdfback']);
Route::post('/update-billissue', [admincontroller::class, 'updateBillIssue']);
Route::get('/adminroute', [AdminController::class, 'adminroute'])->name('admin.adminroute');
Route::post('/update-statuspdfso2', [admincontroller::class, 'updateStatuspdf2']);
Route::post('/update-statuspdfcan', [admincontroller::class, 'updateStatuspdfcan']);
Route::post('/update-delivery-date', [admincontroller::class, 'updateDeliveryDate'])->name('update.delivery.date');
Route::get('/upload', function () {
    return view('upload');
});

Route::post('/upload-pdf', [admincontroller::class, 'upload'])->name('upload.pdf');
Route::post('/upload/billissue', [admincontroller::class, 'uploadBillIssue'])->name('upload.billissue');



// PO system
use App\Http\Controllers\PoController;
Route::get('/dashboardpo', [PoController::class, 'dashboardpo'])->name('po.dashboardpo');
Route::get('/insertpo', [PoController::class, 'insertpo'])->name('po.insertpo');
Route::post('/insertpo', [PoController::class, 'insertpobill'])->name('insertpo.post'); 
Route::get('/get-pobill-detail/{po_detail_id}', [PoController::class, 'getpoBillDetail'])->name('getpoBillDetail');
Route::post('/fetch-polalong', [pocontroller::class, 'fetchFormType']);


use App\Http\Controllers\adminpocontroller;
Route::get('/adminpo', [adminpoController::class, 'dashboard'])->name('po.adminpo');
Route::get('/historypo', [AdminpoController::class, 'historypo'])->name('po.historypo');
Route::post('/update-statuspo', [adminpocontroller::class, 'updateStatus']);
Route::post('/update-statuspoback', [adminpocontroller::class, 'updateStatuspoback']);
Route::post('/updatepo-delivery-date', [AdminPoController::class, 'updateDeliveryDate'])->name('updatepo.delivery.date');

// Doc system
use App\Http\Controllers\DocController;
Route::get('/dashboarddoc', [DocController::class, 'dashboarddoc'])->name('document.dashboarddoc');
Route::get('/insertdoc', [DocController::class, 'insertdoc'])->name('document.insertdoc');
Route::post('/insertdocu', [DocController::class, 'insertDocu'])->name('insertdocu');
Route::get('/get-docbill-detail/{doc_id}', [DocController::class, 'getdocBillDetail'])->name('getdocBillDetail');
Route::post('/fetch-doclalong', [doccontroller::class, 'fetchFormType']);


use App\Http\Controllers\admindoccontroller;
Route::get('/admindoc', [admindoccontroller::class, 'dashboarddoc'])->name('document.admindoc');
Route::get('/admindocroute', [admindoccontroller::class, 'dashboarddocroute'])->name('document.admindocroute');
Route::get('/historydoc', [admindoccontroller::class, 'historydoc'])->name('document.historydoc');
Route::post('/update-statusdoc', [admindoccontroller::class, 'updateStatus']);
Route::post('/update-statuspdfdoc', [admindoccontroller::class, 'statuspdfdoc']);
Route::post('/update-statusdocback', [admindoccontroller::class, 'updateStatusdocback']);
Route::post('/updatedoc-delivery-date', [AdmindocController::class, 'updateDeliveryDate'])->name('updatedoc.delivery.date');

use App\Http\Controllers\alertcontroller;
Route::get('/alertsale', [alertcontroller::class, 'dashboard'])->name('alert.alertsale');
Route::post('/updatesolve', [alertcontroller::class, 'updatesolve'])->name('updatesolve');
Route::get('/alertaccount', [alertcontroller::class, 'dashboardaccount'])->name('alert.alertaccount');
Route::post('/finish', [alertcontroller::class, 'finish'])->name('finish.ng');

Route::get('/alertsale/count', function () {
    // นับจาก tblbill
    $count1 = DB::table('tblbill')
    ->whereNotNull('NG')
    ->where('statuspdf', 2)
    ->whereNull('solve') 
    ->count();

    // นับจาก pobills
    $count2 = DB::table('pobills')
    ->whereNotNull('NG')
    ->whereNull('solve') 
    ->count();

    // นับจาก docbills
    $count3 = DB::table('docbills')
    ->whereNotNull('NG')
    ->where('statuspdf', 1)
    ->whereNull('solve') 
    ->count();

    $total = $count1 + $count2 + $count3;

    return response()->json([
        'count' => $total,
        'from_tblbill' => $count1,
        'from_pobills' => $count2,
        'from_docbills' => $count3,
    ]);
});

Route::get('/alertaccount/count', function () {
    $count = DB::table('tblbill')
                ->where('formtype', "บิล/PO3/บัญชี")   
                ->where('statuspdf', 1)  
                ->count();              

    return response()->json(['count' => $count]);
});
Route::get('/getall-bill-detail/{id}', [alertcontroller::class, 'getBillDetail']);


use App\Http\Controllers\text;
Route::get('/SOlist', [text::class, 'txt1'])->name('txt1');
Route::get('/insertSO', [text::class, 'txt2'])->name('txt2');
Route::get('/adminSO', [text::class, 'txt3'])->name('txt3');



use App\Http\Controllers\Sotestcontroller;
Route::get('/Sotest', [Sotestcontroller::class, 'dashboard']);

use App\Http\Controllers\SheetController;
Route::post('/send-to-sheet', [SheetController::class, 'send']);

Route::post('/merge-pdf', [PoDocumentController::class, 'mergeAndOverwrite'])->name('merge.pdf');

use App\Http\Controllers\WorkScheduleController;
Route::get('/WorkSchedule', [WorkScheduleController::class, 'index']);

use App\Http\Controllers\checkbillController;
Route::get('/dashboardcheckbillsolve', [checkbillController::class, 'dashboardsolve']);
Route::get('/dashboardcheckbill', [checkbillController::class, 'dashboard']);
Route::post('/updatestatusdeli', [checkbillController::class, 'updatestatusdeli'])->name('updatestatusdeli');




