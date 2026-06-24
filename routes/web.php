<?php
 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AgentController;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Portal (Protected)
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
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
    Route::get('/projects', [ProjectController::class, 'projects'])->name('admin.projects');
    Route::post('/projects/store', [ProjectController::class, 'storeProject'])->name('admin.projects.store');
    Route::get('/projects/toggle/{id}', [ProjectController::class, 'toggleProject'])->name('admin.projects.toggle');

    // Keywords management
    Route::get('/keywords', [KeywordController::class, 'keywords'])->name('admin.keywords');
    Route::post('/keywords/store', [KeywordController::class, 'storeKeyword'])->name('admin.keywords.store');
    Route::post('/keywords/toggle/{id}', [KeywordController::class, 'toggleKeyword'])->name('admin.keywords.toggle');
    Route::delete('/keywords/delete/{id}', [KeywordController::class, 'deleteKeyword'])->name('admin.keywords.delete');

    // Settings & Billing
    Route::get('/settings', [SettingsController::class, 'settings'])->name('admin.settings');
    Route::post('/settings/account', [SettingsController::class, 'saveAccount'])->name('admin.settings.account');
    Route::get('/billing', [BillingController::class, 'billing'])->name('admin.billing');

    // AI Marketer / Creative Generator
    Route::get('/marketing', [MarketingController::class, 'marketing'])->name('admin.marketing');
    Route::post('/marketing/generate-social', [MarketingController::class, 'generateSocialSuite'])->name('admin.marketing.generate-social');
    Route::post('/marketing/generate-growth', [MarketingController::class, 'generateGrowthSuite'])->name('admin.marketing.generate-growth');
    Route::post('/marketing/generate-campaign', [MarketingController::class, 'generateAdCampaign'])->name('admin.marketing.generate-campaign');
    Route::post('/marketing/launch', [MarketingController::class, 'launchMarketingCampaign'])->name('admin.marketing.launch');

    // Economics & Stats
    Route::get('/statistics', [DashboardController::class, 'statistics'])->name('admin.statistics');
    Route::post('/vm/trigger', [DashboardController::class, 'triggerVmCrawl'])->name('admin.vm.trigger');

    // AI Agents Control Center
    Route::get('/agents', [AgentController::class, 'index'])->name('admin.agents');
    Route::post('/agents/toggle', [AgentController::class, 'toggleAgent'])->name('admin.agents.toggle');
    Route::post('/agents/enqueue', [AgentController::class, 'enqueueTask'])->name('admin.agents.enqueue');
    Route::get('/agents/visitor-stream', [AgentController::class, 'getVisitorStream']);
    Route::get('/agents/whatsapp-logs', [AgentController::class, 'getWhatsAppLogs']);
    Route::get('/agents/linkedin-logs', [AgentController::class, 'getLinkedInLogs']);
    Route::get('/agents/queue-logs', [AgentController::class, 'getQueueLogs']);

    // Client Directory & Impersonation (Admin)
    Route::get('/clients', [ClientController::class, 'adminIndex'])->name('admin.clients');
    Route::get('/clients/impersonate/{id}', [ClientController::class, 'impersonateClient'])->name('admin.clients.impersonate');
    Route::get('/clients/exit', [ClientController::class, 'exitImpersonate'])->name('admin.clients.exit');
});

// Client Portal (Protected)
Route::prefix('client')->middleware('auth')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
    
    // Client Creative Builder (using dynamic MarketingController)
    Route::get('/marketing', [MarketingController::class, 'marketing'])->name('client.marketing');
    Route::post('/marketing/generate-social', [MarketingController::class, 'generateSocialSuite'])->name('client.marketing.generate-social');
    Route::post('/marketing/generate-growth', [MarketingController::class, 'generateGrowthSuite'])->name('client.marketing.generate-growth');
    Route::post('/marketing/generate-campaign', [MarketingController::class, 'generateAdCampaign'])->name('client.marketing.generate-campaign');
    Route::post('/marketing/launch', [MarketingController::class, 'launchMarketingCampaign'])->name('client.marketing.launch');
});

