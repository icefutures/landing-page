<?php
/**
 * Landing Page dengan Token & Session Management
 * Path: /invest/index.php atau /invest (dengan .htaccess rewrite)
 */

// Start session with secure settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);

// Load configuration and database
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

$config = require __DIR__ . '/../config.php';
$db = new TokenDB($config);

// Helper function to get client IP
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ipaddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
        $ipaddress = $_SERVER['HTTP_X_REAL_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    }
    return $ipaddress;
}

// Helper function to redirect with query string preservation
function redirectWithQuery($url, $excludeParams = []) {
    $queryParams = $_GET;
    foreach ($excludeParams as $param) {
        unset($queryParams[$param]);
    }
    
    if (!empty($queryParams)) {
        $url .= '?' . http_build_query($queryParams);
    }
    
    header("Location: $url", true, 302);
    exit;
}

// Get client info
$ipAddress = getClientIP();
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

// Check for existing session cookie
$cookieName = $config['session_cookie_name'];
$hasValidSession = false;

if (isset($_COOKIE[$cookieName])) {
    $sessionId = $_COOKIE[$cookieName];
    
    if ($db->validateSession($sessionId, $ipAddress, $userAgent)) {
        $hasValidSession = true;
    } else {
        // Invalid session, clear cookie
        setcookie($cookieName, '', time() - 3600, '/', '', true, true);
    }
}

// If valid session exists, show landing page
if ($hasValidSession) {
    // Periodic cleanup (1% chance)
    if (rand(1, 100) === 1) {
        $db->cleanup();
    }
    
    // Include the landing page HTML
    include __DIR__ . '/landing-page.php';
    exit;
}

// No valid session, check for token parameter
$token = $_GET['t'] ?? null;

if (!$token) {
    // No token and no valid session -> redirect to expired
    redirectWithQuery($config['expired_page'], []);
}

// Validate token signature
if (!$db->validateTokenSignature($token)) {
    // Invalid token signature or expired
    redirectWithQuery($config['expired_page'], []);
}

// Check if token already used
if ($db->isTokenUsed($token)) {
    // Token already consumed
    redirectWithQuery($config['expired_page'], []);
}

// Token is valid and not used yet -> consume it
$sessionId = $db->consumeToken($token, $ipAddress, $userAgent);

if (!$sessionId) {
    // Failed to consume token (race condition?)
    redirectWithQuery($config['expired_page'], []);
}

// Set session cookie
$cookieExpiry = time() + $config['session_lifetime'];
setcookie(
    $cookieName,
    $sessionId,
    [
        'expires' => $cookieExpiry,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]
);

// Redirect to same URL without 't' parameter (preserve other params like utm_*, fbclid, gclid)
redirectWithQuery('/invest', ['t']);
