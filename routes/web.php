<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ClientController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// TODO: Add ->middleware('auth') when authentication is implemented
// Currently public for MVP development
Route::prefix('admin')->group(function () {
    // Dashboard & Notifications
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/switch-project', [DashboardController::class, 'switchProject']);
    Route::post('/notifications/read', [DashboardController::class, 'markNotificationsRead']);

    // CRM Leads management (AJAX & modals)
    Route::get('/crm', [LeadController::class, 'index'])->name('admin.crm');
    Route::post('/crm/status', [LeadController::class, 'updateStatus'])->name('admin.crm.status');
    Route::get('/crm/details/{id}', [LeadController::class, 'getDetails']);
    Route::post('/crm/generate', [LeadController::class, 'generateAiReply'])->name('admin.crm.generate');
    Route::post('/crm/save-reply', [LeadController::class, 'saveReply'])->name('admin.crm.save-reply');
    Route::post('/crm/send-message', [LeadController::class, 'sendMessage'])->name('admin.crm.send-message');
    Route::post('/crm/meeting', [LeadController::class, 'scheduleMeeting'])->name('admin.crm.meeting');

    // Campaigns & Projects CRUD
    Route::get('/projects', [CampaignController::class, 'projects'])->name('admin.projects');
    Route::post('/projects/store', [CampaignController::class, 'storeProject'])->name('admin.projects.store');
    Route::get('/projects/toggle/{id}', [CampaignController::class, 'toggleProject'])->name('admin.projects.toggle');

    // Keywords management
    Route::get('/keywords', [CampaignController::class, 'keywords'])->name('admin.keywords');
    Route::post('/keywords/store', [CampaignController::class, 'storeKeyword'])->name('admin.keywords.store');
    Route::post('/keywords/toggle/{id}', [CampaignController::class, 'toggleKeyword'])->name('admin.keywords.toggle');
    Route::delete('/keywords/delete/{id}', [CampaignController::class, 'deleteKeyword'])->name('admin.keywords.delete');

    // Settings & Billing
    Route::get('/settings', [CampaignController::class, 'settings'])->name('admin.settings');
    Route::post('/settings/account', [CampaignController::class, 'saveAccount'])->name('admin.settings.account');
    Route::get('/billing', [CampaignController::class, 'billing'])->name('admin.billing');

    // AI Marketer / Creative Generator
    Route::get('/marketing', [CampaignController::class, 'marketing'])->name('admin.marketing');
    Route::post('/marketing/generate', [CampaignController::class, 'generateMarketingPost'])->name('admin.marketing.generate');
    Route::post('/marketing/launch', [CampaignController::class, 'launchMarketingCampaign'])->name('admin.marketing.launch');

    // Economics & Stats
    Route::get('/statistics', [DashboardController::class, 'statistics'])->name('admin.statistics');

    // Client Directory & Impersonation (Admin)
    Route::get('/clients', [ClientController::class, 'adminIndex'])->name('admin.clients');
    Route::get('/clients/impersonate/{id}', [ClientController::class, 'impersonateClient'])->name('admin.clients.impersonate');
    Route::get('/clients/exit', [ClientController::class, 'exitImpersonate'])->name('admin.clients.exit');
});

// Client Dashboard & Simulation
Route::prefix('client')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
    Route::get('/marketing', [ClientController::class, 'marketing'])->name('client.marketing');
    Route::post('/marketing/generate', [ClientController::class, 'generateCampaign'])->name('client.marketing.generate');
    Route::post('/marketing/launch', [ClientController::class, 'launchCampaign'])->name('client.marketing.launch');
});

