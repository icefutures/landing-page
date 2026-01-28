<?php
/**
 * Simple redirect to fx.idnads.pro landing page
 * Path: /go/invest/index.php
 * 
 * Preserves all query parameters (utm_*, fbclid, gclid, etc.)
 */

// Load configuration
$config = require __DIR__ . '/../../config.php';

// Build redirect URL and preserve all query parameters
$redirectUrl = $config['redirect_url'];
$queryParams = $_GET;

// Build final URL
$finalUrl = $redirectUrl;
if (!empty($queryParams)) {
    $finalUrl .= '?' . http_build_query($queryParams);
}

// Perform 302 redirect
header("Location: $finalUrl", true, 302);
exit;
