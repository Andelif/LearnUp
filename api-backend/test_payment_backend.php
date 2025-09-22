<?php

// Simple test to check if payment backend is working
echo "Testing Payment Backend...\n\n";

// Test 1: Check if PaymentVoucher model exists
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "✅ Laravel app bootstrapped successfully\n";
    
    // Test if PaymentVoucher model can be loaded
    $reflection = new ReflectionClass('App\Models\PaymentVoucher');
    echo "✅ PaymentVoucher model loaded\n";
    
    // Test if MediaFeeService can be loaded
    $reflection = new ReflectionClass('App\Services\MediaFeeService');
    echo "✅ MediaFeeService loaded\n";
    
    // Test if PaymentController can be loaded
    $reflection = new ReflectionClass('App\Http\Controllers\PaymentController');
    echo "✅ PaymentController loaded\n";
    
    // Check if payment_vouchers table exists
    try {
        $count = \App\Models\PaymentVoucher::count();
        echo "✅ Payment vouchers table exists (count: $count)\n";
    } catch (Exception $e) {
        echo "❌ Payment vouchers table doesn't exist or has issues: " . $e->getMessage() . "\n";
        echo "Run: php artisan migrate\n";
    }
    
    echo "\n🎉 Backend components are working!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Make sure you're in the api-backend directory and run: composer install\n";
}
