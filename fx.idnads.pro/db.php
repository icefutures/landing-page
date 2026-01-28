<?php
/**
 * Database functions for token and session management
 */

class TokenDB {
    private $pdo;
    private $config;
    
    public function __construct($config) {
        $this->config = $config;
        $this->initDatabase();
    }
    
    private function initDatabase() {
        $dbPath = $this->config['db_path'];
        $dbDir = dirname($dbPath);
        
        // Create data directory if not exists
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        // Connect to SQLite
        $this->pdo = new PDO('sqlite:' . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create tables if not exist
        $this->createTables();
    }
    
    private function createTables() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS tokens (
                token TEXT PRIMARY KEY,
                used INTEGER DEFAULT 0,
                session_id TEXT,
                created_at INTEGER,
                used_at INTEGER,
                ip_address TEXT,
                user_agent TEXT
            )
        ");
        
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS sessions (
                session_id TEXT PRIMARY KEY,
                token TEXT,
                created_at INTEGER,
                last_activity INTEGER,
                ip_address TEXT,
                user_agent TEXT
            )
        ");
        
        // Create indexes
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_token_used ON tokens(used)");
        $this->pdo->exec("CREATE INDEX IF NOT EXISTS idx_session_activity ON sessions(last_activity)");
    }
    
    /**
     * Validate token signature (from tradecenter)
     */
    public function validateTokenSignature($token) {
        // Token format: timestamp.random.signature
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
        
        list($timestamp, $random, $signature) = $parts;
        
        // Check if timestamp is within expiry
        $tokenAge = time() - (int)$timestamp;
        if ($tokenAge > $this->config['token_expiry'] || $tokenAge < 0) {
            return false;
        }
        
        // Verify signature
        $expectedSignature = hash_hmac('sha256', $timestamp . '.' . $random, $this->config['shared_secret']);
        
        return hash_equals($expectedSignature, $signature);
    }
    
    /**
     * Check if token has been used
     */
    public function isTokenUsed($token) {
        $stmt = $this->pdo->prepare("SELECT used FROM tokens WHERE token = ?");
        $stmt->execute([$token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result && $result['used'] == 1;
    }
    
    /**
     * Mark token as used and create session
     */
    public function consumeToken($token, $ipAddress, $userAgent) {
        try {
            $this->pdo->beginTransaction();
            
            // Generate session ID
            $sessionId = bin2hex(random_bytes(32));
            $now = time();
            
            // Insert or update token as used
            $stmt = $this->pdo->prepare("
                INSERT INTO tokens (token, used, session_id, created_at, used_at, ip_address, user_agent)
                VALUES (?, 1, ?, ?, ?, ?, ?)
                ON CONFLICT(token) DO UPDATE SET
                    used = 1,
                    session_id = excluded.session_id,
                    used_at = excluded.used_at
                WHERE used = 0
            ");
            $stmt->execute([$token, $sessionId, $now, $now, $ipAddress, $userAgent]);
            
            // Check if token was actually consumed (not already used)
            if ($stmt->rowCount() == 0) {
                $this->pdo->rollBack();
                return false;
            }
            
            // Create session
            $stmt = $this->pdo->prepare("
                INSERT INTO sessions (session_id, token, created_at, last_activity, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$sessionId, $token, $now, $now, $ipAddress, $userAgent]);
            
            $this->pdo->commit();
            
            return $sessionId;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Token consumption error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate session
     */
    public function validateSession($sessionId, $ipAddress, $userAgent) {
        $stmt = $this->pdo->prepare("
            SELECT created_at, last_activity FROM sessions
            WHERE session_id = ? AND ip_address = ? AND user_agent = ?
        ");
        $stmt->execute([$sessionId, $ipAddress, $userAgent]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$session) {
            return false;
        }
        
        // Check if session expired
        $sessionAge = time() - $session['last_activity'];
        if ($sessionAge > $this->config['session_lifetime']) {
            return false;
        }
        
        // Update last activity
        $stmt = $this->pdo->prepare("UPDATE sessions SET last_activity = ? WHERE session_id = ?");
        $stmt->execute([time(), $sessionId]);
        
        return true;
    }
    
    /**
     * Clean up old tokens and sessions
     */
    public function cleanup() {
        $expiredTime = time() - $this->config['session_lifetime'] - 86400; // Keep for 1 extra day
        
        $this->pdo->exec("DELETE FROM tokens WHERE created_at < $expiredTime");
        $this->pdo->exec("DELETE FROM sessions WHERE last_activity < $expiredTime");
    }
}
