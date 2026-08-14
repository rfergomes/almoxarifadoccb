<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\QuickRegistrationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

use App\Http\Controllers\AttachmentController;

// Autenticação
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Recuperação de Senha ("Esqueci Minha Senha")
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->middleware('guest')->name('password.update');

// Rotas Protegidas por Autenticação e RBAC
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('can:view-dashboard')
        ->name('dashboard');

    // Movimentações & Saídas
    Route::get('/movements', [MovementController::class, 'index'])->middleware('can:view-movements')->name('movements.index');
    Route::get('/movements/create', [MovementController::class, 'create'])->middleware('can:create-movements')->name('movements.create');
    Route::post('/movements', [MovementController::class, 'store'])->middleware('can:create-movements')->name('movements.store');
    Route::get('/movements/{movement}', [MovementController::class, 'show'])->middleware('can:view-movements')->name('movements.show');
    Route::get('/movements/{movement}/pdf', [MovementController::class, 'exportPdf'])->middleware('can:view-movements')->name('movements.pdf');
    Route::post('/movements/items/{item}/return', [MovementController::class, 'returnItem'])->middleware('can:create-movements')->name('movements.items.return');

    // Entradas de Estoque (NF / Doações)
    Route::get('/entries', [EntryController::class, 'index'])->middleware('can:view-movements')->name('entries.index');
    Route::get('/entries/create', [EntryController::class, 'create'])->middleware('can:create-movements')->name('entries.create');
    Route::post('/entries', [EntryController::class, 'store'])->middleware('can:create-movements')->name('entries.store');

    // Inventário Geral Periódico
    Route::get('/inventories', [InventoryController::class, 'index'])->middleware('can:manage-materials')->name('inventories.index');
    Route::get('/inventories/create', [InventoryController::class, 'create'])->middleware('can:manage-materials')->name('inventories.create');
    Route::post('/inventories', [InventoryController::class, 'store'])->middleware('can:manage-materials')->name('inventories.store');
    Route::get('/inventories/{inventory}', [InventoryController::class, 'show'])->middleware('can:manage-materials')->name('inventories.show');
    Route::post('/inventories/{inventory}/save-counts', [InventoryController::class, 'saveCounts'])->middleware('can:manage-materials')->name('inventories.save-counts');
    Route::post('/inventories/{inventory}/complete', [InventoryController::class, 'complete'])->middleware('can:manage-materials')->name('inventories.complete');
    Route::get('/inventories/{inventory}/pdf', [InventoryController::class, 'pdf'])->middleware('can:manage-materials')->name('inventories.pdf');

    // Gestão de Usuários (Restrito a Administradores)
    Route::get('/users', [UserController::class, 'index'])->middleware('can:manage-users')->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->middleware('can:manage-users')->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('can:manage-users')->name('users.update');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->middleware('can:manage-users')->name('users.reset-password');

    // Configurações do Sistema
    Route::get('/settings', [SettingController::class, 'index'])->middleware('can:manage-users')->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->middleware('can:manage-users')->name('settings.update');

    // Cadastros Rápido via Modal Inline (AJAX)
    Route::post('/api/quick-beneficiary', [QuickRegistrationController::class, 'beneficiary'])->middleware('can:manage-beneficiaries')->name('api.quick-beneficiary');
    Route::post('/api/quick-destination', [QuickRegistrationController::class, 'destination'])->middleware('can:manage-destinations')->name('api.quick-destination');
    Route::post('/api/quick-material', [QuickRegistrationController::class, 'material'])->middleware('can:manage-materials')->name('api.quick-material');

    // Central de Relatórios Gerenciais & Manual do Usuário
    Route::get('/reports', [ReportController::class, 'index'])->middleware('can:view-dashboard')->name('reports.index');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->middleware('can:view-dashboard')->name('reports.export.pdf');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->middleware('can:view-dashboard')->name('reports.export.excel');
    Route::get('/user-manual/pdf', [ReportController::class, 'userManualPdf'])->middleware('can:view-dashboard')->name('user-manual.pdf');

    // Anexos & Download
    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])->middleware('can:view-movements')->name('attachments.download');
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->middleware('can:manage-materials')->name('attachments.destroy');

    // Materiais & Estoque
    Route::get('/materials', [MaterialController::class, 'index'])->middleware('can:view-materials')->name('materials.index');
    Route::post('/materials', [MaterialController::class, 'store'])->middleware('can:manage-materials')->name('materials.store');
    Route::put('/materials/{material}', [MaterialController::class, 'update'])->middleware('can:manage-materials')->name('materials.update');
    Route::post('/materials/{material}/adjust-stock', [MaterialController::class, 'adjustStock'])->middleware('can:manage-materials')->name('materials.adjust-stock');

    // Beneficiários
    Route::get('/beneficiaries', [BeneficiaryController::class, 'index'])->middleware('can:view-beneficiaries')->name('beneficiaries.index');
    Route::post('/beneficiaries', [BeneficiaryController::class, 'store'])->middleware('can:manage-beneficiaries')->name('beneficiaries.store');
    Route::put('/beneficiaries/{beneficiary}', [BeneficiaryController::class, 'update'])->middleware('can:manage-beneficiaries')->name('beneficiaries.update');

    // Destinos
    Route::get('/destinations', [DestinationController::class, 'index'])->middleware('can:view-destinations')->name('destinations.index');
    Route::post('/destinations', [DestinationController::class, 'store'])->middleware('can:manage-destinations')->name('destinations.store');
    Route::put('/destinations/{destination}', [DestinationController::class, 'update'])->middleware('can:manage-destinations')->name('destinations.update');
});
