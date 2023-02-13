use App\Http\Controllers\PaymentMethod\SslCommerzPaymentController;

//--------------------------------------------------------------------------//
//                              SSLCOMMERZ ROUTES                           //
//--------------------------------------------------------------------------//
Route::post('/pay',                             [SslCommerzPaymentController::class, 'index'])->name('sslcommerz-pay');
Route::post('/pay-via-ajax',                    [SslCommerzPaymentController::class, 'payViaAjax']);
Route::post('/success',                         [SslCommerzPaymentController::class, 'success']);
Route::post('/fail',                            [SslCommerzPaymentController::class, 'fail']);
Route::post('/cancel',                          [SslCommerzPaymentController::class, 'cancel']);
Route::post('/ipn',                             [SslCommerzPaymentController::class, 'ipn']);
Route::get('/example1',                         [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
