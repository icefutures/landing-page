<?php
/**
 * Configuration file example for tradecenter.idnads.pro
 * Copy this file to config.php and update the values
 */

return [
    // Shared secret between tradecenter and fx (MUST match fx.idnads.pro config)
    'shared_secret' => 'YOUR-RANDOM-SECRET-KEY-CHANGE-THIS-IN-PRODUCTION',
    
    // Redirect target URL
    'redirect_url' => 'https://fx.idnads.pro/invest',
    
    // Environment
    'environment' => 'production', // 'production' or 'development'
];
