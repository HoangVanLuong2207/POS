<?php
// routes/api.php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route đăng nhập không cần xác thực
Route::post('/login', [AuthController::class, 'login']);

// Group các route cần xác thực
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Luồng Admin: Chỉ tài khoản có role là 'admin' mới được truy cập
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        // ... Thêm các route quản lý khác chỉ dành cho admin tại đây
    });

    // Luồng Staff: Mọi tài khoản đã đăng nhập đều có thể truy cập
    Route::get('/products', [ProductController::class, 'index']);
    // ... Thêm các route chung cho staff tại đây
});
?>
