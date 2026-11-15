<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SettingController;

// Public Certificate Verification Route
Route::get('/verify/{code}', [CertificateController::class, 'verify'])->name('certificates.verify');

// Authentication Routes
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [AuthController::class, 'updatePassword'])->name('password.update');

    // 2. Students Directory & Admissions
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{id}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/students/{id}/pdf', [StudentController::class, 'downloadPdf'])->name('students.pdf');
    Route::post('/students/{id}/documents', [StudentController::class, 'uploadDocument'])->name('students.uploadDocument');
    Route::delete('/students/{id}/documents/{docId}', [StudentController::class, 'deleteDocument'])->name('students.deleteDocument');

    // 3. Enquiries & Admissions Pipeline
    Route::get('/enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
    Route::get('/enquiries/create', [EnquiryController::class, 'create'])->name('enquiries.create');
    Route::post('/enquiries', [EnquiryController::class, 'store'])->name('enquiries.store');
    Route::get('/enquiries/{id}', [EnquiryController::class, 'show'])->name('enquiries.show');
    Route::get('/enquiries/{id}/edit', [EnquiryController::class, 'edit'])->name('enquiries.edit');
    Route::put('/enquiries/{id}', [EnquiryController::class, 'update'])->name('enquiries.update');
    Route::delete('/enquiries/{id}', [EnquiryController::class, 'destroy'])->name('enquiries.destroy');
    Route::post('/enquiries/{id}/counselling', [EnquiryController::class, 'addCounselling'])->name('enquiries.addCounselling');
    Route::post('/enquiries/{id}/follow-up', [EnquiryController::class, 'addFollowUp'])->name('enquiries.addFollowUp');

    // 4. Courses & Batches
    Route::resource('courses', CourseController::class);
    Route::resource('batches', BatchController::class);

    // 5. Fees & Payments
    Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
    Route::post('/fees/collect', [FeeController::class, 'collectPayment'])->name('fees.collect');
    Route::get('/fees/receipt/{id}/pdf', [FeeController::class, 'downloadReceipt'])->name('fees.receiptPdf');
    Route::get('/fees/receipt/{id}', [FeeController::class, 'viewReceiptHtml'])->name('fees.receiptView');
    Route::post('/fees/receipt/{id}/correct', [FeeController::class, 'correctPayment'])->name('fees.correctPayment');

    // 6. Attendance (Students & Staff)
    Route::get('/attendance/students', [AttendanceController::class, 'studentIndex'])->name('attendance.students');
    Route::post('/attendance/students', [AttendanceController::class, 'studentSave'])->name('attendance.studentSave');
    Route::get('/attendance/staff', [AttendanceController::class, 'staffIndex'])->name('attendance.staff');
    Route::post('/attendance/staff/check-in', [AttendanceController::class, 'staffCheckIn'])->name('attendance.staffCheckIn');
    Route::post('/attendance/staff/leave', [AttendanceController::class, 'staffApplyLeave'])->name('attendance.staffApplyLeave');
    Route::post('/attendance/staff/leave/{id}/approve', [AttendanceController::class, 'staffApproveLeave'])->name('attendance.staffApproveLeave');

    // 7. Student Progress & Cognitive Assessments
    Route::resource('progress', ProgressController::class);

    // 8. Certificates
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::post('/certificates/generate', [CertificateController::class, 'generate'])->name('certificates.generate');
    Route::get('/certificates/{id}/download', [CertificateController::class, 'download'])->name('certificates.download');

    // 9. Staff Management
    Route::resource('staff', StaffController::class);

    // 10. Centralized Media Library
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [MediaController::class, 'upload'])->name('media.upload');
    Route::delete('/media/{id}', [MediaController::class, 'destroy'])->name('media.destroy');

    // 11. Reports & Analytics
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.exportCsv');

    // 12. Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');
    Route::get('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // 13. Roles, Permissions & User Access Control (Super Admin / Admin only)
    Route::middleware(['role:super-admin,admin'])->group(function () {
        Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::get('/roles/{id}/permissions', [RolePermissionController::class, 'editRolePermissions'])->name('roles.editPermissions');
        Route::put('/roles/{id}/permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('roles.updatePermissions');
        Route::post('/users', [RolePermissionController::class, 'storeUser'])->name('users.store');
        Route::post('/users/{id}/role', [RolePermissionController::class, 'updateUserRole'])->name('users.updateRole');
    });

    // 14. System Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
