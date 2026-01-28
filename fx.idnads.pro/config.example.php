<?php
/**
 * Configuration file example for fx.idnads.pro
 * Copy this file to config.php and update the values
 */

return [
    // Shared secret between tradecenter and fx for token validation
    'shared_secret' => 'YOUR-RANDOM-SECRET-KEY-CHANGE-THIS-IN-PRODUCTION',
    
    // Database path (SQLite)
    'db_path' => __DIR__ . '/data/tokens.db',
    
    // Session configuration
    'session_lifetime' => 86400, // 24 hours in seconds
    'session_cookie_name' => 'fx_session',
    
    // Token configuration
    'token_expiry' => 300, // 5 minutes (token valid for 5 minutes after generation)
    
    // Paths
    'expired_page' => '/expired.html',
    
    // Environment
    'environment' => 'production', // 'production' or 'development'
];
