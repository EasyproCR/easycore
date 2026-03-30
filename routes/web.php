<?php

use App\Filament\Pages\CustomPropertyPage;
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

Route::get('/', function () {
    return redirect('/personal');
});

Route::get('/custom-property-page', CustomPropertyPage::class)->name('custom.property.page');
Route::view('/legal', 'pages.legal-notice-public')->name('legal.notice');

Route::prefix('ad-requests')->name('ad-requests.')->group(function () {
    Route::get('{adRequest}/pay', [\App\Http\Controllers\StripePaymentController::class, 'checkout'])->name('pay');
    Route::get('payment/success/{ad_request_id}', [\App\Http\Controllers\StripePaymentController::class, 'success'])->name('payment.success');
    Route::get('payment/cancel/{ad_request_id}', [\App\Http\Controllers\StripePaymentController::class, 'cancel'])->name('payment.cancel');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/easychat-redirect', [\App\Http\Controllers\EasyChatController::class, 'redirect'])->name('easychat');
});
