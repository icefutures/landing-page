<?php
/**
 * Test Token Generation
 * File ini untuk testing lokal sebelum deploy
 * 
 * Cara pakai:
 * 1. php test-token.php
 * 2. Copy token yang di-generate
 * 3. Test di browser atau curl
 */

// Shared secret (gunakan yang sama dengan config.php)
$secret = 'YOUR-SECRET-HERE';

if ($argc > 1 && $argv[1] === '--secret') {
    // Generate new secret
    echo "Generated new secret:\n";
    echo bin2hex(random_bytes(32)) . "\n";
    exit;
}

// Generate token
$timestamp = time();
$random = bin2hex(random_bytes(16));
$signature = hash_hmac('sha256', $timestamp . '.' . $random, $secret);
$token = $timestamp . '.' . $random . '.' . $signature;

echo "=== Token Generator Test ===\n\n";
echo "Timestamp: $timestamp\n";
echo "Random: $random\n";
echo "Signature: $signature\n\n";
echo "Full Token:\n$token\n\n";

echo "Test URL:\n";
echo "https://fx.idnads.pro/invest?t=$token&utm_source=test\n\n";

// Validate token
echo "=== Token Validation Test ===\n";
$parts = explode('.', $token);
if (count($parts) === 3) {
    list($ts, $rnd, $sig) = $parts;
    $expectedSig = hash_hmac('sha256', $ts . '.' . $rnd, $secret);
    
    echo "Token format: ✓ Valid\n";
    echo "Signature match: " . (hash_equals($expectedSig, $sig) ? '✓ Valid' : '✗ Invalid') . "\n";
    
    $age = time() - (int)$ts;
    echo "Token age: $age seconds\n";
    echo "Expiry (5min): " . ($age < 300 ? '✓ Valid' : '✗ Expired') . "\n";
} else {
    echo "Token format: ✗ Invalid\n";
}

echo "\n=== Commands ===\n";
echo "Generate new secret: php test-token.php --secret\n";
echo "Test with curl:\n";
echo "  curl -I \"https://fx.idnads.pro/invest?t=$token\"\n";
