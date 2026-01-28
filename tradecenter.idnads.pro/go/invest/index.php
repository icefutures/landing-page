<?php
/**
 * Token Generator for tradecenter.idnads.pro
 * Path: /go/invest/index.php
 * 
 * This script generates a signed token and redirects to fx.idnads.pro
 * with all query parameters (utm_*, fbclid, gclid, etc.) preserved
 */

// Load configuration
$config = require __DIR__ . '/../../config.php';

/**
 * Generate signed token
 * Format: timestamp.random.signature
 */
function generateToken($secret) {
    $timestamp = time();
    $random = bin2hex(random_bytes(16));
    $signature = hash_hmac('sha256', $timestamp . '.' . $random, $secret);
    
    return $timestamp . '.' . $random . '.' . $signature;
}

// Generate token
$token = generateToken($config['shared_secret']);

// Build redirect URL with token and preserve all query parameters
$redirectUrl = $config['redirect_url'];
$queryParams = $_GET;
$queryParams['t'] = $token;

// Build final URL
$finalUrl = $redirectUrl . '?' . http_build_query($queryParams);

// Log for debugging (optional, remove in production)
if ($config['environment'] === 'development') {
    error_log("Generated token: $token");
    error_log("Redirecting to: $finalUrl");
}

// Perform 302 redirect
header("Location: $finalUrl", true, 302);
exit;
