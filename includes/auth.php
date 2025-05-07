<?php
require_once 'db.php';
require_once 'functions.php';

class Auth {

/**
 * Authenticate user login
 * 
 * @param string $username Username or email
 * @param string $password Password
 * @return array|bool User data or false if authentication fails
 */
    public static function login($username, $password) {
    // Check if username is email or username
    $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
    $field = $isEmail ? 'email' : 'username';
    
    // Get user from database
    $sql = "SELECT * FROM users WHERE $field = :username AND status = 'active'";
    $user = db()->fetch($sql, ['username' => $username]);
    
    if (!$user) {
        return false;
    }
    
    // Verify password (plain text comparison as requested)
    if ($password !== $user['password']) {
        return false;
    }
    
    // Update last login time
    $updateSql = "UPDATE users SET last_login = NOW() WHERE user_id = :user_id";
    db()->query($updateSql, ['user_id' => $user['user_id']]);
    
    // Log activity
    self::logActivity($user['user_id'], 'login', 'users', $user['user_id'], 'User logged in');
    
    // Return the user data (DON'T set session variables here)
    // Remove password from user data before returning
    unset($user['password']);
    return $user;
}
    
    /**
     * Register a new user
     * 
     * @param array $userData User data
     * @return int|bool User ID or false if registration fails
     */
    public static function register($userData) {
        // Validate required fields
        $requiredFields = ['username', 'email', 'password', 'first_name', 'last_name', 'role'];
        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                return false;
            }
        }
        
        // Check if username or email already exists
        $checkSql = "SELECT COUNT(*) as count FROM users WHERE username = :username OR email = :email";
        $result = db()->fetch($checkSql, [
            'username' => $userData['username'],
            'email' => $userData['email']
        ]);
        
        if ($result['count'] > 0) {
            return false;
        }
        
        /* Hash password
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT); */

        // Store plain text password (not recommended for security)
        $plainPassword = $userData['password'];

        
        // Insert new user
        $sql = "INSERT INTO users (username, email, password, first_name, last_name, role, date_registered) 
                VALUES (:username, :email, :password, :first_name, :last_name, :role, NOW())";
        
        $params = [
            'username' => $userData['username'],
            'email' => $userData['email'],
            'password' => $plainPassword,
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'role' => $userData['role']
        ];
        
        db()->query($sql, $params);
        $userId = db()->lastInsertId();
        
        // Log activity
        self::logActivity($userId, 'register', 'users', $userId, 'New user registered');
        
        return $userId;
    }
    
    /**
     * Log out current user
     */
    public static function logout() {
        // Log activity before destroying session
        if (isset($_SESSION['user_id'])) {
            self::logActivity($_SESSION['user_id'], 'logout', 'users', $_SESSION['user_id'], 'User logged out');
        }
        
        // Destroy session
        session_unset();
        session_destroy();
        
        // Set session cookie to expire
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get current logged in user data
     * 
     * @return array|null User data or null if not logged in
     */
    public static function getCurrentUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        $sql = "SELECT * FROM users WHERE user_id = :user_id";
        $user = db()->fetch($sql, ['user_id' => $_SESSION['user_id']]);
        
        if ($user) {
            unset($user['password']);
        }
        
        return $user;
    }
    
    /**
     * Check if current user has specified role
     * 
     * @param string|array $roles Role or array of roles to check
     * @return bool
     */
    public static function hasRole($roles) {
        $user = self::getCurrentUser();
        
        if (!$user) {
            return false;
        }
        
        if (is_array($roles)) {
            return in_array($user['role'], $roles);
        }
        
        return $user['role'] === $roles;
    }
    
    /**
     * Generate password reset token
     * 
     * @param string $email User email
     * @return string|bool Reset token or false if failed
     */
    public static function generateResetToken($email) {
        // Check if email exists
        $sql = "SELECT user_id FROM users WHERE email = :email AND status = 'active'";
        $user = db()->fetch($sql, ['email' => $email]);
        
        if (!$user) {
            return false;
        }
        
        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry
        
        // Save token to database
        $updateSql = "UPDATE users SET reset_token = :token, reset_expires = :expires 
                    WHERE user_id = :user_id";
        db()->query($updateSql, [
            'token' => $token,
            'expires' => $expires,
            'user_id' => $user['user_id']
        ]);
        
        return $token;
    }
    
    /**
     * Reset user password using token
     * 
     * @param string $token Reset token
     * @param string $password New password
     * @return bool Success or failure
     */
    public static function resetPassword($token, $password) {
        // Validate token
        $sql = "SELECT user_id FROM users WHERE reset_token = :token AND reset_expires > NOW()";
        $user = db()->fetch($sql, ['token' => $token]);
        
        if (!$user) {
            return false;
        }
        
        // Verify password (Hindi secure bc it uses plain text password)
        if ($password !== $user['password']) {
        return false;
        }
        
        // Update password and clear token
        $updateSql = "UPDATE users SET password = :password, reset_token = NULL, reset_expires = NULL 
                    WHERE user_id = :user_id";
        db()->query($updateSql, [
            'password' => $password,
            'user_id' => $user['user_id']
        ]);
        
        // Log activity
        self::logActivity($user['user_id'], 'reset_password', 'users', $user['user_id'], 'Password reset using token');
        
        return true;
    }
    
    /**
     * Change user password
     * 
     * @param int $userId User ID
     * @param string $currentPassword Current password
     * @param string $newPassword New password
     * @return bool Success or failure
     */
    public static function changePassword($userId, $currentPassword, $newPassword) {
        // Get user data
        $sql = "SELECT password FROM users WHERE user_id = :user_id";
        $user = db()->fetch($sql, ['user_id' => $userId]);
        
        if (!$user) {
            return false;
        }
        
        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return false;
        }
        
        // Verify password (plain text comparison as requested)
        if ($password !== $user['password']) {
        return false;
        }
        
        // Update password
        $updateSql = "UPDATE users SET password = :password WHERE user_id = :user_id";
        db()->query($updateSql, [
            'password' => $password,
            'user_id' => $userId
        ]);
        
        // Log activity
        self::logActivity($userId, 'change_password', 'users', $userId, 'Password changed');
        
        return true;
    }
    
    /**
     * Log user activity
     * 
     * @param int $userId User ID
     * @param string $action Action performed
     * @param string $entityType Entity type
     * @param int $entityId Entity ID
     * @param string $description Description
     */
    public static function logActivity($userId, $action, $entityType, $entityId, $description) {
        $sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent) 
                VALUES (:user_id, :action, :entity_type, :entity_id, :description, :ip_address, :user_agent)";
        
        $params = [
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ];
        
        db()->query($sql, $params);
    }
    
    /**
     * Create a notification for a user
     * 
     * @param int $userId User ID
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string $type Notification type
     * @param int $relatedId Related entity ID
     */
    public static function createNotification($userId, $title, $message, $type, $relatedId = null) {
        $sql = "INSERT INTO notifications (user_id, title, message, notification_type, related_id) 
                VALUES (:user_id, :title, :message, :type, :related_id)";
        
        $params = [
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'related_id' => $relatedId
        ];
        
        db()->query($sql, $params);
    }
}