<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserByUsername($username) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserRoles($userId) {
        $query = "SELECT r.role_name FROM user_roles ur
                  JOIN roles r ON ur.role_id = r.id
                  WHERE ur.user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function validatePassword($password, $username) {
        if (strlen($password) < 4) {
            return "Password must be at least 4 characters long.";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return "Password must contain at least one capital letter.";
        }
        if (ctype_digit($password)) {
            return "Password cannot be all numbers.";
        }
        if (strtolower($password) === strtolower($username)) {
            return "Password cannot be the same as the username.";
        }
        return true;
    }

    public function createUser($username, $password, $email, $firstName, $lastName) {
        $passwordValidation = self::validatePassword($password, $username);
        if ($passwordValidation !== true) {
            return $passwordValidation; // Return the error message
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (username, password, email, first_name, last_name) 
                  VALUES (:username, :password, :email, :first_name, :last_name)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        
        return $stmt->execute();
    }

    public function resetPassword($username) {
        $hashedPassword = password_hash($username, PASSWORD_DEFAULT);
        
        $query = "UPDATE users SET password = :password, password_reset = 1 WHERE username = :username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':username', $username);
        
        return $stmt->execute();
    }

    public function isPasswordReset($username) {
        $query = "SELECT password_reset FROM users WHERE username = :username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }

    public function updatePassword($username, $newPassword) {
        $passwordValidation = self::validatePassword($newPassword, $username);
        if ($passwordValidation !== true) {
            return $passwordValidation; // Return the error message
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $query = "UPDATE users SET password = :password, password_reset = 0 WHERE username = :username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':username', $username);
        
        return $stmt->execute();
    }

    public function logActivity($userId, $action) {
        $query = "INSERT INTO user_activity_log (user_id, action, timestamp) VALUES (:user_id, :action, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':action', $action);
        return $stmt->execute();
    }
}