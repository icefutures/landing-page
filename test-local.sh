#!/bin/bash
# Local Testing Script
# Jalankan: bash test-local.sh

echo "==================================="
echo "   Local Testing - Token System"
echo "==================================="
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP not found. Please install PHP 8.0+"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✓ PHP Version: $PHP_VERSION"
echo ""

# Generate shared secret if not exists
if [ ! -f ".secret" ]; then
    echo "🔑 Generating shared secret..."
    php -r "echo bin2hex(random_bytes(32));" > .secret
    echo "✓ Secret saved to .secret"
else
    echo "✓ Using existing secret from .secret"
fi

SECRET=$(cat .secret)
echo "Secret: $SECRET"
echo ""

# Update config files
echo "📝 Updating config files..."

# Update fx config
cat > fx.idnads.pro/config.php <<EOF
<?php
return [
    'shared_secret' => '$SECRET',
    'db_path' => __DIR__ . '/data/tokens.db',
    'session_lifetime' => 86400,
    'session_cookie_name' => 'fx_session',
    'token_expiry' => 300,
    'expired_page' => '/expired.html',
    'environment' => 'development',
];
EOF
echo "✓ Updated fx.idnads.pro/config.php"

# Update tradecenter config
cat > tradecenter.idnads.pro/config.php <<EOF
<?php
return [
    'shared_secret' => '$SECRET',
    'redirect_url' => 'http://localhost:8001/invest',
    'environment' => 'development',
];
EOF
echo "✓ Updated tradecenter.idnads.pro/config.php"

# Create data directory
mkdir -p fx.idnads.pro/data
echo "✓ Created data directory"
echo ""

# Generate test token
echo "🎫 Generating test token..."
php -r "
\$config = require 'tradecenter.idnads.pro/config.php';
\$timestamp = time();
\$random = bin2hex(random_bytes(16));
\$signature = hash_hmac('sha256', \$timestamp . '.' . \$random, \$config['shared_secret']);
\$token = \$timestamp . '.' . \$random . '.' . \$signature;
echo \"Token: \$token\n\";
echo \"Test URL: http://localhost:8001/invest?t=\$token&utm_source=test\n\";
" > .test-token
cat .test-token
echo ""

echo "==================================="
echo "   Ready to test!"
echo "==================================="
echo ""
echo "1. Start fx.idnads.pro server (Terminal 1):"
echo "   cd fx.idnads.pro && php -S localhost:8001"
echo ""
echo "2. Start tradecenter server (Terminal 2):"
echo "   cd tradecenter.idnads.pro && php -S localhost:8002"
echo ""
echo "3. Test URLs:"
echo "   - Generate token: http://localhost:8002/go/invest/index.php?utm_source=test"
echo "   - Direct with token: $(tail -n 1 .test-token)"
echo "   - Expired page: http://localhost:8001/expired.html"
echo "   - DB Inspector: http://localhost:8001/db-inspector.php (user:admin pass:change-this-password)"
echo ""
echo "4. Test flow:"
echo "   a) Open: http://localhost:8002/go/invest/index.php?utm_source=test"
echo "   b) You'll be redirected to fx with token"
echo "   c) Then redirected again without token"
echo "   d) Landing page should appear with cookie set"
echo "   e) Refresh → should show landing page immediately"
echo "   f) Open same token URL in incognito → should show expired.html"
echo ""
