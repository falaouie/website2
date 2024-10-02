<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUserByUsername($username) {
        $query = "SELECT u.*, s.first_name, s.last_name 
                  FROM users_tbl u 
                  LEFT JOIN staff_tbl s ON u.staff_id = s.staff_id 
                  WHERE u.username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isAdmin($userId) {
        $query = "SELECT staff_id FROM users_tbl WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['staff_id'] === null;
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

    public function resetPassword($username) {
        $query = "UPDATE users_tbl SET password = :password, password_reset = 1 WHERE username = :username";
        $hashedPassword = password_hash($username, PASSWORD_DEFAULT);
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':username', $username);
        
        return $stmt->execute();
    }

    public function isPasswordReset($username) {
        $query = "SELECT password_reset FROM users_tbl WHERE username = :username";
        
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
        
        $query = "UPDATE users_tbl SET password = :password, password_reset = 0 WHERE username = :username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':username', $username);
        
        return $stmt->execute();
    }

    public function logLogin($userId) {
        // First, get the staff_id for this user (if it exists)
        $query = "SELECT staff_id FROM users_tbl WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $staffId = $result['staff_id'] ?? null;

        // Now log the login, allowing NULL for staff_id (which will be the case for admin)
        $query = "INSERT INTO user_login_log (staff_id, login_time) VALUES (:staff_id, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staffId, PDO::PARAM_INT);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function logLogout($userId) {
        // First, get the staff_id for this user (if it exists)
        $query = "SELECT staff_id FROM users_tbl WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $staffId = $result['staff_id'] ?? null;

        // Now log the logout
        $query = "UPDATE user_login_log SET logout_time = NOW() 
                  WHERE staff_id = :staff_id AND logout_time IS NULL 
                  ORDER BY login_time DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_id', $staffId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function isUserTableEmpty() {
        $query = "SELECT COUNT(*) FROM users_tbl";
        $stmt = $this->conn->query($query);
        return (int)$stmt->fetchColumn() === 0;
    }
    
    public function createAdminUser($firstName, $lastName, $phoneNumber, $email) {
        $this->conn->beginTransaction();
    
        try {
            // Insert into admin_tbl
            $query = "INSERT INTO admin_tbl (first_name, last_name, phone_number, email) VALUES (:first_name, :last_name, :phone_number, :email)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':phone_number', $phoneNumber);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
    
            $adminId = $this->conn->lastInsertId();
    
            // Insert into users_tbl
            $username = 'admin';
            $password = password_hash('admin', PASSWORD_DEFAULT);
            $query = "INSERT INTO users_tbl (username, password, staff_id, password_reset) VALUES (:username, :password, NULL, 1)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
    
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return "Error creating admin user: " . $e->getMessage();
        }
    }
}