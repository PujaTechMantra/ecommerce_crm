<?php

use Illuminate\Support\Facades\Route;
// Livewire Components
use App\Livewire\AdminLogin;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\DB;

use Symfony\Component\DomCrawler\Crawler;

use App\Livewire\Product\CollectionList;
use App\Livewire\Product\ProductList;

use App\Http\Controllers\Admin\CronController;
use GuzzleHttp\Client;
use App\Livewire\Organization\{OrgDashboard};
use App\Livewire\Admin\{
    AdminForgotPassword, CustomerAdd, Dashboard, CustomerIndex, CustomerDetails,
    OrderIndex, OfferIndex, PolicyDetails, OrderDetail, CityIndex, PincodeIndex,
    RiderEngagement, PaymentSummary, PaymentUserSummary, UserPaymentHistory,
    PaymentVehicleSummary, RefundSummary, ChangePassword,AdminOrganizationIndex,AdminOrganizationDashboard,AdminOrganizationInvoices,AdminOrganizationPayments,PushNotificationList
};
use App\Livewire\Product\{
    MasterCategory, MasterSubCategory, ColorList, SizeList, MasterProduct, AddProduct, EditProduct, UpdateProduct,
    GalleryIndex, StockProduct, MasterProductType, ProductWiseVehicle, VehicleList,
    MasterSubscription, VehicleCreate, VehicleUpdate, VehicleDetail, VehiclePaymentSummary,
    BomPartList, SellingQuery
};

use App\Livewire\Master\{
    BannerIndex, FaqIndex, WhyEwentIndex, EmployeeManagementList, EmployeeManagementCreate,
    EmployeeManagementUpdate, DesignationIndex, DesignationPermissionList
};

// Public Route for Login

// With admin prefix
Route::get('admin/login', AdminLogin::class)->name('login');
Route::get('admin/forgot-password', AdminForgotPassword::class)->name('admin.forgot-password');
Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Without admin prefix (duplicate)
Route::get('login', AdminLogin::class)->name('login');
Route::get('forgot-password', AdminForgotPassword::class)->name('admin.forgot-password');
Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');

// Default Root Route
Route::get('/', function () {
    if (auth('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    if (auth('organization')->check()) {
        return redirect()->route('organization.dashboard');
    }
    return redirect()->route('login');
});

// Admin Routes - Authenticated and Authorized
Route::middleware(['auth:admin', 'admin.maintenance'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('reset-password', ChangePassword::class)->name('admin.reset-password');

    // Dashboard and Customer Routes
    Route::get('dashboard', Dashboard::class)->name('admin.dashboard');
    Route::group(['prefix' => 'rider'], function () {
        Route::get('add', CustomerAdd::class)->name('admin.customer.add');
        Route::get('verification/list', CustomerIndex::class)->name('admin.customer.verification.list');
        Route::get('engagement/list', RiderEngagement::class)->name('admin.customer.engagement.list');
        Route::get('details/{id}', CustomerDetails::class)->name('admin.customer.details');
    });

    // Product Routes
    Route::group(['prefix' => 'models'], function () {
        Route::get('/collections', CollectionList::class)->name('admin.product.collections');

        Route::get('/list', ProductList::class)->name('admin.product.index');
        Route::get('/categories', MasterCategory::class)->name('admin.product.categories');
        Route::get('/sub-categories', MasterSubCategory::class)->name('admin.product.sub_categories');
        Route::get('/colors', ColorList::class)->name('admin.product.colors');
        Route::get('/sizes', SizeList::class)->name('admin.product.sizes');
        
        Route::get('/keywords', MasterProductType::class)->name('admin.product.type');
        Route::get('/new', AddProduct::class)->name('admin.product.add');
        Route::get('/edit/{productId}', EditProduct::class)->name('admin.product.edit');
        Route::get('/gallery/{product_id}', GalleryIndex::class)->name('admin.product.gallery');
        Route::get('/subscriptions', MasterSubscription::class)->name('admin.model.subscriptions');
    });

    Route::group(['prefix' => 'stock'], function () {
        Route::get('/list', StockProduct::class)->name('admin.product.stocks');
        Route::get('/vehicle/{product_id}', ProductWiseVehicle::class)->name('admin.product.stocks.vehicle');
    });

    Route::group(['prefix' => 'bom-parts'], function () {
        Route::get('/', BomPartList::class)->name('admin.bom_part.list');
    });

    Route::group(['prefix' => 'selling-query'], function () {
        Route::get('/', SellingQuery::class)->name('admin.selling_query.list');
    });

    Route::group(['prefix' => 'vehicle'], function () {
        Route::get('/list', VehicleList::class)->name('admin.vehicle.list');
        Route::get('/create', VehicleCreate::class)->name('admin.vehicle.create');
        Route::get('/update/{id}', VehicleUpdate::class)->name('admin.vehicle.update');
        Route::get('/details/{vehicle_id}', VehicleDetail::class)->name('admin.vehicle.detail');
        Route::get('/payment/summary/{vehicle_id}', VehiclePaymentSummary::class)->name('admin.vehicle.payment-summary');
    });

    // Order Management
    Route::group(['prefix' => 'order'], function () {
        Route::get('/list', OrderIndex::class)->name('admin.order.list');
        Route::get('/details/{id}', OrderDetail::class)->name('admin.order.detail');
    });

    // Payment Management
    Route::group(['prefix' => 'payment'], function () {
        Route::get('/summary/{model_id?}/{vehicle_id?}', PaymentSummary::class)->name('admin.payment.summary');
        Route::get('/vehicle/summary/{model_id?}/{vehicle_id?}', PaymentVehicleSummary::class)->name('admin.payment.vehicle.summary');
        Route::get('/user-history/{user_id}', PaymentUserSummary::class)->name('admin.payment.user_history');
        Route::get('/user/payment-history', UserPaymentHistory::class)->name('admin.payment.user_payment_history');
        Route::get('/refund-summary', RefundSummary::class)->name('admin.payment.refund.summary');
    });

    // Offer Management
    Route::group(['prefix' => 'offer'], function () {
        Route::get('/list', OfferIndex::class)->name('admin.offer.list');
    });

    // Master Routes
    Route::group(['prefix' => 'master'], function () {
        Route::get('/banner', BannerIndex::class)->name('admin.banner.index');
        Route::get('/faq', FaqIndex::class)->name('admin.faq.index');
        Route::get('/why-ewent', WhyEwentIndex::class)->name('admin.why-ewent');
        Route::get('/policy-details', PolicyDetails::class)->name('admin.policy-details');
    });

    // Employee Management
    Route::group(['prefix' => 'employee'], function () {
        Route::get('list', EmployeeManagementList::class)->name('admin.employee.list');
        Route::get('create', EmployeeManagementCreate::class)->name('admin.employee.create');
        Route::get('update/{id}', EmployeeManagementUpdate::class)->name('admin.employee.update');
        Route::get('/designations', DesignationIndex::class)->name('admin.designation.index');
        Route::get('/designation/permission/{id}', DesignationPermissionList::class)->name('admin.designation.permission');
    });

    // Location Management
    Route::group(['prefix' => 'location'], function () {
        Route::get('/city', CityIndex::class)->name('admin.city.index');
        Route::get('/pincodes', PincodeIndex::class)->name('admin.pincode.index');
    });
    // Organization Management
    Route::group(['prefix'=>'organization'], function (){
        Route::get('/', AdminOrganizationIndex::class)->name('admin.organization.index');
        Route::get('/invoices', AdminOrganizationInvoices::class)->name('admin.organization.invoice.list');
        Route::get('/payments', AdminOrganizationPayments::class)->name('admin.organization.payment.list');
        Route::get('{id}/dashboard/', AdminOrganizationDashboard::class)->name('admin.organization.dashboard');
    });
    // Notification Management
    Route::group(['prefix'=>'notifications'], function (){
        Route::get('/push-notification', PushNotificationList::class)->name('admin.notification.push-notification');
    });
}); 


// Organization Routes - Authenticated
Route::prefix('organization')->group(function () {
    Route::get('/', function () {
        return redirect()->route('organization.dashboard');
    });
    // // Example: Organization Dashboard
    Route::get('dashboard', OrgDashboard::class)
        ->name('organization.dashboard');

    Route::prefix('rider')->group(function () {
        Route::get('details/{id}', CustomerDetails::class)->name('organization.rider.details');
    });
    Route::get('vehicle/details/{vehicle_id}', VehicleDetail::class)->name('organization.vehicle.detail');
});

// Cron
Route::group(['prefix' => 'cron'], function () {
    Route::get('/test', [CronController::class, 'TestLog']);
    Route::get('/vehicles/daily-timeline', [CronController::class, 'DailyVehicleLog']);
    Route::get('/vehicles/check/payment-overdue', [CronController::class, 'VehiclePaymentOverDue']);
    Route::get('/vehicles/overdue/immobilizer-requests', [CronController::class, 'OverDueImmobilizerRequests']);

    // New cron
    // Daily
    Route::get('/generate-organization-invoice', [CronController::class, 'generateOrganizationInvoice']);
    // Weekly(Friday)
    Route::get('/logs/cleanup', [CronController::class, 'removeOldCronLogs']);
    // Weekly(Saturday)
    Route::get('/vehicle-timelines/cleanup', [CronController::class, 'removeVehicleTimeline']);
    // weekly(Sunday)
    Route::get('/payment-logs/cleanup', [CronController::class, 'removePaymentLog']);
   // weekly(Monday)
    Route::get('/user-location-logs/cleanup', [CronController::class, 'removeUserLocationLog']);
    // Every day 11.50pm
    Route::get('/update-overdue-organization-invoices', [CronController::class, 'updateOverdueOrgInvoices']);
});
