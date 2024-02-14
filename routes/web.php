<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MiscellaneousController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\DNSController;
use App\Http\Controllers\UserListController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\CreditListController;
use App\Http\Controllers\PubblyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => ['guest']], function () {

    Route::get('login', [AuthenticationController::class, 'showLogin'])->name('login');
    Route::post('dologin', [AuthenticationController::class, 'doLogin'])->name('login.submit');
    Route::get('register', [AuthenticationController::class, 'showSignup'])->name('register');
    Route::post('dosignup', [AuthenticationController::class, 'doSignup'])->name('register.submit');
    Route::get('user/verify/{token}', [AuthenticationController::class, 'verifyEmail'])->name('user/verify');
    Route::get('forgot-password', [AuthenticationController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('send-reset-link', [AuthenticationController::class, 'sendResetLink'])->name('send-reset-link');
    Route::get('reset-password/{token}', [AuthenticationController::class, 'showResetPassword'])->name('reset-password');
    Route::post('resetPassword', [AuthenticationController::class, 'resetPassword'])->name('resetPassword');

    Route::get('activation', [PubblyController::class, 'showActivationDevice'])->name('activation');
    Route::get('add-device', [PubblyController::class, 'addDevice'])->name('add-device');
    Route::post('getCheckoutLinkForActivation', [PubblyController::class, 'getCheckoutLinkForActivation'])->name('getCheckoutLinkForActivation');

});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return redirect('/dashboard');
    });
    Route::post('logout', [AuthenticationController::class, 'doLogout'])->name('logout');
    Route::get('admin/user/verify/{token}', [AuthenticationController::class, 'verifyEmail'])->name('admin/user/verify');

    Route::impersonate();

    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('my-profile', [AuthenticationController::class, 'showProfile'])->name('my-profile');
    Route::post('updateProfile', [AuthenticationController::class, 'updateProfile'])->name('updateProfile');
    Route::post('changePassword', [AuthenticationController::class, 'changePassword'])->name('changePassword');
    Route::get('change-password', [AuthenticationController::class, 'account_change_password'])->name('change-password');
    Route::get('account-settings-billing', [AuthenticationController::class, 'account_settings_billing'])->name('account-settings-billing');

    Route::resource('device', DeviceController::class);
    Route::get('device/list/{id?}', [DeviceController::class, 'show'])->name('device/list');
    Route::post('device/renew/{id}', [DeviceController::class, 'renew'])->name('device/renew');
    Route::post('deviceUpdate',  [DeviceController::class, 'update'])->name('deviceUpdate');
    Route::get('device/codes/history/{id}', [DeviceController::class, 'codeHistory'])->name('device/codes/history');
    Route::post('device/change_device_code_status/{id}', [DeviceController::class, 'changeDeviceStatus'])->name('change_device_code_status');
    Route::post('device/active_deactive_device/{id}', [DeviceController::class, 'activeDeactiveDevice'])->name('active_deactive_device');
    Route::post('device/change_cloud_status/{id}', [DeviceController::class, 'changeCloudStatus'])->name('change_cloud_status');
    Route::post('device/change_code_renew_status/{id}', [DeviceController::class, 'changeCodeAutoRenew'])->name('change_code_renew_status');
    Route::post('device/get_device_code/{id}', [DeviceController::class, 'getDeviceCode'])->name('get_device_code');
    Route::post('device/remove_session', [DeviceController::class, 'removeSession'])->name('remove_session');
    Route::get('showCodeHistory', [DeviceController::class, 'showCodeHistory'])->name('showCodeHistory');
    Route::post('device/deleteAll', [DeviceController::class, 'deleteAll']);

    Route::resource('playlist', PlaylistController::class);
    Route::get('playlist/list/{id?}', [PlaylistController::class, 'show'])->name('playlist/list');
    Route::post('playlistUpdate',  [PlaylistController::class, 'update'])->name('playlistUpdate');
    Route::get('edit/{id}', [PlaylistController::class, 'editMultiDNS'])->name('edit');
    Route::delete('delete/{id}', [PlaylistController::class, 'deleteMultiDNS'])->name('delete');
    Route::post('checkExistPlaylist',  [PlaylistController::class, 'checkExistPlaylist'])->name('checkExistPlaylist');
    Route::post('checkExistUserName',  [PlaylistController::class, 'checkExistUserName'])->name('checkExistUserName');
    Route::post('changePlaylistStatus', [PlaylistController::class, 'changePlaylistStatus'])->name('changePlaylistStatus');
    Route::post('assignPlaylist', [PlaylistController::class, 'assignPlaylist'])->name('assignPlaylist');
    Route::post('removeDevicePlaylist', [PlaylistController::class, 'removeDevicePlaylist'])->name('removeDevicePlaylist');
    Route::post('playlist/deleteAll', [PlaylistController::class, 'deleteAll']);

    Route::resource('dns', DNSController::class);
    Route::get('dns/list/{id?}', [DNSController::class, 'show'])->name('dns/list');
    Route::post('dnsUpdate',  [DNSController::class, 'update'])->name('dnsUpdate');
    Route::post('checkExistDNS',  [DNSController::class, 'checkExistDNS'])->name('checkExistDNS');
    Route::post('dns/deleteAll', [DNSController::class, 'deleteAll']);

    Route::resource('invoice', InvoiceController::class);
    Route::get('list', [InvoiceController::class, 'show'])->name('invoice/list');
    Route::post('upgradePlan', [InvoiceController::class, 'upgradePlan'])->name('upgradePlan');
    Route::post('canceledSubscription', [InvoiceController::class, 'canceledSubscription'])->name('canceledSubscription');
    Route::post('deleteSubscription', [InvoiceController::class, 'deleteSubscription'])->name('deleteSubscription');
    Route::get('my-order', [InvoiceController::class, 'showMyOrder'])->name('my-order');
    Route::get('subscription', [InvoiceController::class, 'subscription'])->name('subscription');
    Route::get('invoice/preview/{id}', [InvoiceController::class, 'invoicePreview'])->name('invoice/preview');
    Route::get('sendInvoice/{id}', [InvoiceController::class, 'sendInvoice'])->name('sendInvoice');
    Route::get('invoicePDF/{user_id}', [InvoiceController::class, 'invoicePDF'])->name('invoicePDF');
    // Route::get('downloadInvoice', [InvoiceController::class, 'downloadInvoice'])->name('downloadInvoice');
    Route::post('getCheckoutLink', [InvoiceController::class, 'getCheckoutLink'])->name('getCheckoutLink');
    Route::get('thank-you', [InvoiceController::class, 'getThankYou'])->name('thank-you');
    Route::post('CheckCouponCode', [InvoiceController::class, 'CheckCouponCode'])->name('CheckCouponCode');
    Route::post('invoice/deleteAll', [InvoiceController::class, 'deleteAll']);

    Route::post('coupon/change_coupon_status/{id}', [CouponController::class, 'change_coupon_status'])->name('change_coupon_status');
    Route::get('coupon/generateCouponCode', [CouponController::class, 'generateCouponCode'])->name('generateCouponCode');
    Route::get('couponshow/{id}', [CouponController::class, 'view'])->name('couponshow');
    Route::resource('coupon', CouponController::class);
    Route::get('coupon', [CouponController::class, 'show'])->name('coupon/list');
    Route::post('coupon/deleteAll', [CouponController::class, 'deleteAll']);

    Route::resource('users', UserListController::class);
    Route::get('users/list', [UserListController::class, 'show'])->name('users/list');
    Route::post('userUpdate',  [UserListController::class, 'update'])->name('userUpdate');
    Route::post('checkExistEmail',  [UserListController::class, 'checkExistEmail'])->name('checkExistEmail');
    Route::post('users/change_user_status/{id}', [UserListController::class, 'change_user_status'])->name('change_user_status');
    Route::post('users/verified_user/{id}', [UserListController::class, 'verified_user'])->name('verified_user');
    Route::get('users/view/account/{id}', [UserListController::class, 'user_view_account'])->name('user-view-account');
    Route::post('user/deleteAll', [UserListController::class, 'deleteAll']);

    Route::resource('plan', PlansController::class);
    Route::get('plan', [PlansController::class, 'show'])->name('plan/list');
    Route::post('planUpdate',  [PlansController::class, 'update'])->name('planUpdate');
    Route::post('change_plan_status', [PlansController::class, 'change_plan_status'])->name('change_plan_status');
    Route::get('subscription/plan/{device_id?}', [PlansController::class, 'subscriptionPlan'])->name
    ('subscription/plan');
    Route::post('checkFreePlan',  [PlansController::class, 'checkFreePlan'])->name('checkFreePlan');

    Route::resource('credits', CreditListController::class);
    Route::get('credits', [CreditListController::class, 'show'])->name('credits/list');
    Route::post('updateUserCredits',  [CreditListController::class, 'updateUserCredits'])->name('updateUserCredits');

    Route::group(['prefix' => 'subscription'], function (){
        Route::get('list', [UserListController::class, 'index'])->name('subscription/list');
    });

    Route::get('systemSetting',  [SystemSettingController::class, 'systemSetting'])->name('systemSetting');
    Route::post('updateSiteSetting', [SystemSettingController::class, 'updateSiteSetting'])->name('updateSiteSetting');
    Route::post('updateEmailSetting', [SystemSettingController::class, 'updateEmailSetting'])->name('updateEmailSetting');
    Route::post('updateCaptchaSetting', [SystemSettingController::class, 'updateCaptchaSetting'])->name('updateCaptchaSetting');

    Route::get('setting',  [SettingController::class, 'setting'])->name('setting');
    Route::post('update_setting',  [SettingController::class, 'update_setting'])->name('update_setting');
});

/* Route Pages */
Route::group(['prefix' => 'page'], function () {
    // Miscellaneous Pages With Page Prefix
    Route::get('coming-soon', [MiscellaneousController::class, 'coming_soon'])->name('misc-coming-soon');
    Route::get('not-authorized', [MiscellaneousController::class, 'not_authorized'])->name('misc-not-authorized');
    Route::get('maintenance', [MiscellaneousController::class, 'maintenance'])->name('misc-maintenance');
    Route::get('license', [PagesController::class, 'license'])->name('page-license');
});

/* Route Pages */
Route::get('/error', [MiscellaneousController::class, 'error'])->name('error');
