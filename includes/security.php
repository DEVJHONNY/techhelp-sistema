<?php
session_start();

// Configurações de segurança da sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
session_regenerate_id(true);

// Headers de segurança
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-strict-when-upgrade");
header("Content-Security-Policy: default-src 'self'");

class Security {
    private static $instance = null;
    private $rate_limits = [];
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Sanitização de entrada
    public function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(strip_tags(trim($data)));
    }

    // Proteção CSRF
    public function generateToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateToken($token) {
        if (empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            $this->logAttempt('CSRF Attack Detected');
            die('Invalid CSRF Token');
        }
    }

    // Rate Limiting
    public function checkRateLimit($key, $limit = 10, $minutes = 1) {
        $current = time();
        $this->cleanOldRateLimits();
        
        if (!isset($this->rate_limits[$key])) {
            $this->rate_limits[$key] = ['count' => 0, 'reset' => $current + ($minutes * 60)];
        }
        
        if ($this->rate_limits[$key]['count'] >= $limit) {
            $this->logAttempt('Rate Limit Exceeded: ' . $key);
            http_response_code(429);
            die('Too Many Requests');
        }
        
        $this->rate_limits[$key]['count']++;
    }

    // Logging
    public function logAttempt($message) {
        $log = date('Y-m-d H:i:s') . " | {$_SERVER['REMOTE_ADDR']} | $message\n";
        error_log($log, 3, __DIR__ . '/../logs/security.log');
    }

    // Senha segura
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}

// Inicializar segurança
$security = Security::getInstance();
