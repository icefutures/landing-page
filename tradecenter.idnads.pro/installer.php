<?php
/**
 * INSTALLER - Setup Otomatis untuk Sistem Link Iklan
 * 
 * Jalankan file ini PERTAMA KALI setelah upload:
 * 1. Akses: https://tradecenter.idnads.pro/installer.php
 * 2. Copy shared secret yang muncul
 * 3. Upload file secret.txt ke fx.idnads.pro
 * 4. Akses: https://fx.idnads.pro/installer.php
 * 5. Hapus installer.php di kedua domain
 * 
 * ATAU gunakan mode otomatis dengan shared file
 */

// Security: Disable jika sudah setup
if (file_exists(__DIR__ . '/.installed')) {
    die('System already installed! Delete .installed file to reinstall.');
}

$domain = $_SERVER['HTTP_HOST'];
$isFx = strpos($domain, 'fx.') !== false;
$isTradecenter = strpos($domain, 'tradecenter.') !== false;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer - Link Iklan System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 { color: #2d3748; margin-bottom: 10px; }
        .domain { color: #667eea; font-size: 18px; margin-bottom: 30px; }
        .step {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .step h3 { color: #2d3748; margin-bottom: 10px; }
        .step p { color: #4a5568; line-height: 1.6; margin-bottom: 10px; }
        .secret-box {
            background: #1a202c;
            color: #48bb78;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            margin: 15px 0;
            position: relative;
        }
        .secret-label {
            color: #a0aec0;
            font-size: 12px;
            margin-bottom: 10px;
        }
        button, .button {
            background: #667eea;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
            transition: all 0.3s;
        }
        button:hover, .button:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }
        .success {
            background: #c6f6d5;
            border: 2px solid #48bb78;
            color: #22543d;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .warning {
            background: #fefcbf;
            border: 2px solid #ecc94b;
            color: #744210;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .code {
            background: #f7fafc;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            color: #e53e3e;
        }
        .checklist {
            list-style: none;
            margin: 20px 0;
        }
        .checklist li {
            padding: 10px;
            margin: 5px 0;
            background: #f7fafc;
            border-radius: 5px;
            position: relative;
            padding-left: 35px;
        }
        .checklist li:before {
            content: "✓";
            position: absolute;
            left: 10px;
            color: #48bb78;
            font-weight: bold;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Installer - Link Iklan System</h1>
        <div class="domain">Domain: <?= htmlspecialchars($domain) ?></div>

        <?php
        $action = $_POST['action'] ?? 'show';
        
        if ($action === 'generate'):
            // Generate shared secret
            $secret = bin2hex(random_bytes(32));
            
            // Save to file
            file_put_contents(__DIR__ . '/secret.txt', $secret);
            
            // Create config.php
            if ($isTradecenter || (!$isFx && !$isTradecenter)):
                $configContent = "<?php\nreturn [\n    'shared_secret' => '$secret',\n    'redirect_url' => 'https://fx.idnads.pro/invest',\n    'environment' => 'production',\n];";
                file_put_contents(__DIR__ . '/config.php', $configContent);
            endif;
            
            if ($isFx || (!$isFx && !$isTradecenter)):
                $configContent = "<?php\nreturn [\n    'shared_secret' => '$secret',\n    'db_path' => __DIR__ . '/data/tokens.db',\n    'session_lifetime' => 86400,\n    'session_cookie_name' => 'fx_session',\n    'token_expiry' => 300,\n    'expired_page' => '/expired.html',\n    'environment' => 'production',\n];";
                file_put_contents(__DIR__ . '/config.php', $configContent);
                
                // Create data folder
                if (!is_dir(__DIR__ . '/data')) {
                    mkdir(__DIR__ . '/data', 0755, true);
                }
            endif;
            
            ?>
            <div class="success">
                <strong>✓ Setup Berhasil!</strong><br>
                Config.php telah dibuat dan shared secret telah di-generate.
            </div>

            <div class="step">
                <h3>📋 Shared Secret Anda:</h3>
                <div class="secret-box">
                    <div class="secret-label">Copy secret ini untuk domain lain:</div>
                    <?= $secret ?>
                </div>
                <button onclick="navigator.clipboard.writeText('<?= $secret ?>'); alert('Secret copied!')">
                    📋 Copy Secret
                </button>
            </div>

            <?php if ($isTradecenter || (!$isFx && !$isTradecenter)): ?>
            <div class="step">
                <h3>➡️ Langkah Selanjutnya:</h3>
                <ol style="margin-left: 20px; line-height: 2;">
                    <li>Download file <code class="code">secret.txt</code> dari domain ini</li>
                    <li>Upload <code class="code">secret.txt</code> ke <strong>fx.idnads.pro</strong></li>
                    <li>Akses: <a href="https://fx.idnads.pro/installer.php" target="_blank">https://fx.idnads.pro/installer.php</a></li>
                    <li>Klik "Use Shared Secret dari File"</li>
                    <li>Hapus installer.php dan secret.txt di kedua domain</li>
                </ol>
                <a href="secret.txt" download class="button">💾 Download secret.txt</a>
            </div>
            <?php endif; ?>

            <div class="step">
                <h3>🧪 Testing</h3>
                <p>Setelah setup di kedua domain, test dengan:</p>
                <ul style="margin-left: 20px; line-height: 2;">
                    <li><a href="https://tradecenter.idnads.pro/go/invest?utm_source=test" target="_blank">Test tradecenter</a></li>
                    <li><a href="https://fx.idnads.pro/expired.html" target="_blank">Test fx expired page</a></li>
                </ul>
            </div>

            <form method="POST" onsubmit="return confirm('Yakin sudah selesai setup?')">
                <input type="hidden" name="action" value="finish">
                <button type="submit">✓ Selesai Setup (Disable Installer)</button>
            </form>

        <?php elseif ($action === 'use_shared'):
            // Use shared secret from file
            if (file_exists(__DIR__ . '/secret.txt')):
                $secret = trim(file_get_contents(__DIR__ . '/secret.txt'));
                
                // Create config.php for fx
                if ($isFx || (!$isFx && !$isTradecenter)):
                    $configContent = "<?php\nreturn [\n    'shared_secret' => '$secret',\n    'db_path' => __DIR__ . '/data/tokens.db',\n    'session_lifetime' => 86400,\n    'session_cookie_name' => 'fx_session',\n    'token_expiry' => 300,\n    'expired_page' => '/expired.html',\n    'environment' => 'production',\n];";
                    file_put_contents(__DIR__ . '/config.php', $configContent);
                    
                    // Create data folder
                    if (!is_dir(__DIR__ . '/data')) {
                        mkdir(__DIR__ . '/data', 0755, true);
                    }
                endif;
                ?>
                <div class="success">
                    <strong>✓ Setup Berhasil!</strong><br>
                    Config.php untuk fx.idnads.pro telah dibuat dengan shared secret yang sama.
                </div>

                <div class="step">
                    <h3>📋 Verifikasi Secret:</h3>
                    <div class="secret-box">
                        <?= htmlspecialchars($secret) ?>
                    </div>
                    <p><small>Secret ini harus SAMA dengan tradecenter.idnads.pro</small></p>
                </div>

                <div class="step">
                    <h3>🧪 Testing</h3>
                    <ul style="margin-left: 20px; line-height: 2;">
                        <li><a href="https://tradecenter.idnads.pro/go/invest?utm_source=test" target="_blank">Test Full Flow</a></li>
                        <li><a href="https://fx.idnads.pro/expired.html" target="_blank">Test Expired Page</a></li>
                    </ul>
                </div>

                <form method="POST" onsubmit="return confirm('Yakin sudah selesai setup?')">
                    <input type="hidden" name="action" value="finish">
                    <button type="submit">✓ Selesai Setup (Disable Installer)</button>
                </form>
            <?php else: ?>
                <div class="warning">
                    <strong>⚠️ File secret.txt tidak ditemukan!</strong><br>
                    Pastikan Anda sudah upload file secret.txt dari tradecenter.idnads.pro
                </div>
                <a href="installer.php" class="button">← Kembali</a>
            <?php endif; ?>

        <?php elseif ($action === 'finish'):
            // Mark as installed
            file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s'));
            ?>
            <div class="success">
                <h2>🎉 Setup Selesai!</h2>
                <p style="margin-top: 15px;">Installer telah dinonaktifkan. Sistem siap digunakan!</p>
            </div>

            <div class="warning">
                <strong>⚠️ PENTING - Hapus File Ini!</strong>
                <ul class="checklist">
                    <li>Hapus <code class="code">installer.php</code> di tradecenter.idnads.pro</li>
                    <li>Hapus <code class="code">installer.php</code> di fx.idnads.pro</li>
                    <li>Hapus <code class="code">secret.txt</code> (jika ada)</li>
                </ul>
            </div>

            <div class="step">
                <h3>🚀 URL untuk Iklan:</h3>
                <div class="secret-box" style="background: #2d3748; color: white;">
                    https://tradecenter.idnads.pro/go/invest
                </div>
                <p>Gunakan URL ini di Facebook Ads, Google Ads, atau platform iklan lainnya.</p>
            </div>

        <?php else: ?>
            <!-- Initial Setup Screen -->
            <div class="step">
                <h3>🎯 Pilih Metode Setup:</h3>
                <p>Sistem ini memerlukan shared secret yang SAMA di kedua domain (tradecenter & fx).</p>
            </div>

            <?php if ($isTradecenter || (!$isFx && !$isTradecenter)): ?>
            <div class="step">
                <h3>📍 Anda di: tradecenter.idnads.pro</h3>
                <p><strong>Langkah 1:</strong> Generate shared secret di sini</p>
                <form method="POST">
                    <input type="hidden" name="action" value="generate">
                    <button type="submit">🔑 Generate Shared Secret & Setup</button>
                </form>
            </div>
            <?php endif; ?>

            <?php if ($isFx || (!$isFx && !$isTradecenter)): ?>
            <div class="step">
                <h3>📍 Anda di: fx.idnads.pro</h3>
                <p><strong>Langkah 2:</strong> Upload secret.txt dari tradecenter, lalu:</p>
                <form method="POST">
                    <input type="hidden" name="action" value="use_shared">
                    <button type="submit">🔗 Use Shared Secret dari File</button>
                </form>
                <p style="margin-top: 15px;"><small>Atau jika belum, setup tradecenter dulu.</small></p>
            </div>
            <?php endif; ?>

            <div class="warning">
                <strong>📝 Urutan Setup:</strong>
                <ol style="margin-left: 20px; margin-top: 10px; line-height: 2;">
                    <li>Akses <code class="code">installer.php</code> di <strong>tradecenter.idnads.pro</strong></li>
                    <li>Generate shared secret</li>
                    <li>Download <code class="code">secret.txt</code></li>
                    <li>Upload <code class="code">secret.txt</code> ke <strong>fx.idnads.pro</strong></li>
                    <li>Akses <code class="code">installer.php</code> di <strong>fx.idnads.pro</strong></li>
                    <li>Use shared secret</li>
                    <li>Test sistem</li>
                    <li>Hapus installer.php di kedua domain</li>
                </ol>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
