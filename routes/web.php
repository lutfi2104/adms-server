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
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\AbsensiSholatController;
use App\Http\Controllers\iclockController;


Route::get('devices', [DeviceController::class, 'index'])->name('devices.index');
Route::get('devices/{id}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
Route::put('devices/{id}', [DeviceController::class, 'update'])->name('devices.update');

Route::get('devices-log', [DeviceController::class, 'DeviceLog'])->name('devices.DeviceLog');
Route::get('finger-log', [DeviceController::class, 'FingerLog'])->name('devices.FingerLog');
Route::get('attendance', [DeviceController::class, 'Attendance'])->name('devices.Attendance');

// Employees CRUD
Route::get('employees', [DeviceController::class, 'Employees'])->name('employees.index');
Route::post('employees', [DeviceController::class, 'StoreEmployee'])->name('employees.store');
Route::get('employees/{id}/edit', [DeviceController::class, 'EditEmployee'])->name('employees.edit');
Route::post('employees/{id}', [DeviceController::class, 'UpdateEmployee'])->name('employees.update');
Route::delete('employees/{id}', [DeviceController::class, 'DeleteEmployee'])->name('employees.destroy');

// Access Sessions (Keluar-Masuk)
Route::get('access-sessions', [DeviceController::class, 'AccessSessions'])->name('access.sessions');

// handshake
Route::get('/iclock/cdata', [iclockController::class, 'handshake']);
// request dari device
Route::post('/iclock/cdata', [iclockController::class, 'receiveRecords']);

Route::get('/iclock/test', [iclockController::class, 'test']);
Route::get('/iclock/getrequest', [iclockController::class, 'getrequest']);



Route::get('/', function () {
    return redirect('devices') ;
});
