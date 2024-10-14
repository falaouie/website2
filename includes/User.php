<?php

class User {

    private $conn;



    public function __construct($db) {

        $this->conn = $db;

    }



    // Existing functions



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



    public function resetPassword($userId) {

        $query = "SELECT username FROM users_tbl WHERE id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);

        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);



        if (!$user) {

            return "User not found.";

        }



        $newPassword = $user['username']; // Set password to username

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);



        $query = "UPDATE users_tbl SET password = :password, password_reset = 1 WHERE id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':password', $hashedPassword);

        $stmt->bindParam(':user_id', $userId);



        if ($stmt->execute()) {

            return true;

        } else {

            return "Error resetting password: " . implode(", ", $stmt->errorInfo());

        }

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

        $query = "SELECT staff_id FROM users_tbl WHERE id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        

        $staffId = $result['staff_id'] ?? null;

    

        $query = "INSERT INTO user_login_log (staff_id, login_time) VALUES (:staff_id, NOW())";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':staff_id', $staffId, PDO::PARAM_INT);

        $stmt->execute();

        return $this->conn->lastInsertId();

    }

    

    public function logLogout($userId, $reason = 'manual') {

        $query = "SELECT staff_id FROM users_tbl WHERE id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        

        $staffId = $result['staff_id'] ?? null;

    

        $query = "UPDATE user_login_log 

                  SET logout_time = NOW(), logout_reason = :reason

                  WHERE (staff_id = :staff_id OR (:staff_id IS NULL AND staff_id IS NULL))

                  AND logout_time IS NULL 

                  ORDER BY login_time DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':staff_id', $staffId, PDO::PARAM_INT);

        $stmt->bindParam(':reason', $reason);

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



    public function getAdminFirstName() {

        $query = "SELECT first_name FROM admin_tbl LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt->fetchColumn();

    }



    public function getAllUsers() {

        $query = "SELECT u.*, s.first_name, s.last_name 

                  FROM users_tbl u 

                  LEFT JOIN staff_tbl s ON u.staff_id = s.staff_id 

                  ORDER BY u.username";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }



    public function getUserById($userId) {

        $query = "SELECT u.*, s.first_name, s.last_name 

                  FROM users_tbl u

                  LEFT JOIN staff_tbl s ON u.staff_id = s.staff_id

                  WHERE u.id = :user_id";

        

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        $stmt->execute();

        

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }



    public function getAllStaff() {

        $query = "SELECT s.*, t.title_name FROM staff_tbl s

                  JOIN titles_tbl t ON s.title_id = t.title_id

                  ORDER BY s.last_name, s.first_name";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }



    public function getStaffById($staffId) {

        $query = "SELECT s.*, t.title_name 

                  FROM staff_tbl s

                  JOIN titles_tbl t ON s.title_id = t.title_id

                  WHERE s.staff_id = :staff_id";

        

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':staff_id', $staffId, PDO::PARAM_INT);

        $stmt->execute();

        

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }



    public function getAllTitles() {

        $query = "SELECT * FROM titles_tbl ORDER BY title_name";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }



    public function getStaffWithoutUsers() {

        $query = "SELECT s.staff_id, s.first_name, s.last_name 

                  FROM staff_tbl s

                  LEFT JOIN users_tbl u ON s.staff_id = u.staff_id

                  WHERE u.id IS NULL AND s.system_access = 1

                  ORDER BY s.last_name, s.first_name";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    

    public function addStaff($data) {

        $this->conn->beginTransaction();

    

        try {

            // Check for existing staff with the same name

            $checkNameQuery = "SELECT COUNT(*) FROM staff_tbl WHERE first_name = :first_name AND last_name = :last_name";

            $checkNameStmt = $this->conn->prepare($checkNameQuery);

            $checkNameStmt->bindParam(':first_name', $data['first_name']);

            $checkNameStmt->bindParam(':last_name', $data['last_name']);

            $checkNameStmt->execute();

            if ($checkNameStmt->fetchColumn() > 0) {

                throw new Exception("A staff member with this name already exists.");

            }

    

            // Check for existing phone number

            $checkPhoneQuery = "SELECT COUNT(*) FROM staff_tbl WHERE phone_number = :phone_number";

            $checkPhoneStmt = $this->conn->prepare($checkPhoneQuery);

            $checkPhoneStmt->bindParam(':phone_number', $data['phone_number']);

            $checkPhoneStmt->execute();

            if ($checkPhoneStmt->fetchColumn() > 0) {

                throw new Exception("This phone number is already in use.");

            }

    

            // Check for existing email if provided

            if (!empty($data['email_address'])) {

                $checkEmailQuery = "SELECT COUNT(*) FROM staff_tbl WHERE email_address = :email_address";

                $checkEmailStmt = $this->conn->prepare($checkEmailQuery);

                $checkEmailStmt->bindParam(':email_address', $data['email_address']);

                $checkEmailStmt->execute();

                if ($checkEmailStmt->fetchColumn() > 0) {

                    throw new Exception("This email address is already in use.");

                }

            }

    

            $query = "INSERT INTO staff_tbl (title_id, first_name, last_name, status, attendance_req, joining_date, phone_number, email_address, system_access) 

                      VALUES (:title_id, :first_name, :last_name, :status, :attendance_req, :joining_date, :phone_number, :email_address, :system_access)";

            

            $stmt = $this->conn->prepare($query);

            

            $stmt->bindParam(':title_id', $data['title_id'], PDO::PARAM_INT);

            $stmt->bindParam(':first_name', $data['first_name']);

            $stmt->bindParam(':last_name', $data['last_name']);

            $stmt->bindParam(':status', $data['status'], PDO::PARAM_INT);

            $stmt->bindParam(':attendance_req', $data['attendance_req'], PDO::PARAM_INT);

            $stmt->bindParam(':joining_date', $data['joining_date']);

            $stmt->bindParam(':phone_number', $data['phone_number']);

            $stmt->bindParam(':email_address', $data['email_address']);

            $stmt->bindParam(':system_access', $data['system_access'], PDO::PARAM_INT);

            

            $stmt->execute();

            $this->conn->commit();

            return true;

        } catch (Exception $e) {

            $this->conn->rollBack();

            return "Error adding staff member: " . $e->getMessage();

        }

    }

    

    public function editStaff($data) {

        $this->conn->beginTransaction();

    

        try {

            // Check for existing staff with the same name (excluding the current staff member)

            $checkNameQuery = "SELECT COUNT(*) FROM staff_tbl WHERE first_name = :first_name AND last_name = :last_name AND staff_id != :staff_id";

            $checkNameStmt = $this->conn->prepare($checkNameQuery);

            $checkNameStmt->bindParam(':first_name', $data['first_name']);

            $checkNameStmt->bindParam(':last_name', $data['last_name']);

            $checkNameStmt->bindParam(':staff_id', $data['staff_id'], PDO::PARAM_INT);

            $checkNameStmt->execute();

            if ($checkNameStmt->fetchColumn() > 0) {

                throw new Exception("A staff member with this name already exists.");

            }

    

            // Check for existing phone number (excluding the current staff member)

            $checkPhoneQuery = "SELECT COUNT(*) FROM staff_tbl WHERE phone_number = :phone_number AND staff_id != :staff_id";

            $checkPhoneStmt = $this->conn->prepare($checkPhoneQuery);

            $checkPhoneStmt->bindParam(':phone_number', $data['phone_number']);

            $checkPhoneStmt->bindParam(':staff_id', $data['staff_id'], PDO::PARAM_INT);

            $checkPhoneStmt->execute();

            if ($checkPhoneStmt->fetchColumn() > 0) {

                throw new Exception("This phone number is already in use.");

            }

    

            // Check for existing email if provided (excluding the current staff member)

            if (!empty($data['email_address'])) {

                $checkEmailQuery = "SELECT COUNT(*) FROM staff_tbl WHERE email_address = :email_address AND staff_id != :staff_id";

                $checkEmailStmt = $this->conn->prepare($checkEmailQuery);

                $checkEmailStmt->bindParam(':email_address', $data['email_address']);

                $checkEmailStmt->bindParam(':staff_id', $data['staff_id'], PDO::PARAM_INT);

                $checkEmailStmt->execute();

                if ($checkEmailStmt->fetchColumn() > 0) {

                    throw new Exception("This email address is already in use.");

                }

            }

    

            $query = "UPDATE staff_tbl SET 

                      title_id = :title_id,

                      first_name = :first_name,

                      last_name = :last_name,

                      status = :status,

                      attendance_req = :attendance_req,

                      joining_date = :joining_date,

                      termination_date = :termination_date,

                      phone_number = :phone_number,

                      email_address = :email_address,

                      system_access = :system_access

                      WHERE staff_id = :staff_id";

            

            $stmt = $this->conn->prepare($query);

            

            $stmt->bindParam(':staff_id', $data['staff_id'], PDO::PARAM_INT);

            $stmt->bindParam(':title_id', $data['title_id'], PDO::PARAM_INT);

            $stmt->bindParam(':first_name', $data['first_name']);

            $stmt->bindParam(':last_name', $data['last_name']);

            $stmt->bindParam(':status', $data['status'], PDO::PARAM_INT);

            $stmt->bindParam(':attendance_req', $data['attendance_req'], PDO::PARAM_INT);

            $stmt->bindParam(':joining_date', $data['joining_date']);

            

            // Handle termination_date

            if (empty($data['termination_date'])) {

                $stmt->bindValue(':termination_date', null, PDO::PARAM_NULL);

            } else {

                $stmt->bindParam(':termination_date', $data['termination_date']);

            }

            

            $stmt->bindParam(':phone_number', $data['phone_number']);

            

            // Handle email_address

            if (empty($data['email_address'])) {

                $stmt->bindValue(':email_address', null, PDO::PARAM_NULL);

            } else {

                $stmt->bindParam(':email_address', $data['email_address']);

            }

            

            $stmt->bindParam(':system_access', $data['system_access'], PDO::PARAM_INT);

            

            $stmt->execute();

            $this->conn->commit();

            return true;

        } catch (Exception $e) {

            $this->conn->rollBack();

            return "Error updating staff member: " . $e->getMessage();

        }

    }



    public function addUser($data) {

        try {

            $username = $data['username'];

            $password = password_hash($username, PASSWORD_DEFAULT); // Set initial password as username

            $staffId = $data['staff_id'] ?: null;

        

            $query = "INSERT INTO users_tbl (username, password, staff_id, password_reset) VALUES (:username, :password, :staff_id, 1)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':username', $username);

            $stmt->bindParam(':password', $password);

            $stmt->bindParam(':staff_id', $staffId);

        

            $stmt->execute();

            return true;

        } catch (PDOException $e) {

            if ($e->getCode() == '23000') {

                return "Username already exists. Please choose a different username.";

            }

            return "Error adding user: " . $e->getMessage();

        }

    }



    public function editUser($data) {

        try {

            $query = "UPDATE users_tbl SET 

                      username = :username,

                      staff_id = :staff_id

                      WHERE id = :id";

            

            $stmt = $this->conn->prepare($query);

            

            $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);

            $stmt->bindParam(':username', $data['username']);

            $stmt->bindParam(':staff_id', $data['staff_id'], PDO::PARAM_INT);

            

            $stmt->execute();

            return true;

        } catch (PDOException $e) {

            if ($e->getCode() == '23000') {

                return "Username already exists. Please choose a different username.";

            }

            return "Error updating user: " . $e->getMessage();

        }

    }



    public function staffHasUser($staffId) {

        $query = "SELECT COUNT(*) FROM users_tbl WHERE staff_id = :staff_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':staff_id', $staffId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchColumn() > 0;

    }



    // New attendance-related functions



    public function getStaffWithAttendanceRequired() {

        $query = "SELECT s.staff_id, s.first_name, s.last_name 

                  FROM staff_tbl s

                  WHERE s.attendance_req = 1

                  AND s.status = 1

                  ORDER BY s.first_name, s.last_name";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }



    public function getCurrentFixedSchedules() {
        $query = "SELECT s.staff_id, s.first_name, s.last_name, 
                         sch.work_day, sch.start_time, sch.end_time, sch.day_off, sch.open_schedule
                  FROM staff_tbl s
                  LEFT JOIN schedules sch ON s.staff_id = sch.staff_id
                  WHERE s.attendance_req = 1
                  AND s.status = 1
                  ORDER BY s.first_name, s.last_name, sch.work_day";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $schedules = [];
        foreach ($results as $row) {
            if (!isset($schedules[$row['staff_id']])) {
                $schedules[$row['staff_id']] = [
                    'staff_id' => $row['staff_id'],
                    'name' => $row['first_name'] . ' ' . $row['last_name'],
                    'schedule' => []
                ];
            }
            
            if ($row['work_day'] !== null) {
                $schedules[$row['staff_id']]['schedule'][$row['work_day']] = [
                    'start' => $row['start_time'],
                    'end' => $row['end_time'],
                    'day_off' => $row['day_off'],
                    'open_schedule' => $row['open_schedule']
                ];
            }
        }
    
        // Fill in missing days with open schedule
        foreach ($schedules as $staffId => $staffSchedule) {
            for ($i = 0; $i < 7; $i++) {
                if (!isset($staffSchedule['schedule'][$i])) {
                    $schedules[$staffId]['schedule'][$i] = [
                        'start' => null,
                        'end' => null,
                        'day_off' => 0,
                        'open_schedule' => 1
                    ];
                }
            }
        }
    
        return array_values($schedules);
    }



    public function updateFixedSchedule($data) {
        $this->conn->beginTransaction();
        try {
            $staffIds = $data['staff'];
            $days = $data['days'];
            $openSchedule = $data['open_schedule'];
            $dayOff = $data['day_off'] ?? 0;
            $workIn = $data['work_in'] ?? null;
            $workOff = $data['work_off'] ?? null;
    
            foreach ($staffIds as $staffId) {
                foreach ($days as $day) {
                    if ($openSchedule == 1) {
                        // For open schedule, insert a record with null times and open_schedule = 1
                        $stmt = $this->conn->prepare("INSERT INTO schedules (staff_id, work_day, start_time, end_time, day_off, open_schedule) 
                                                      VALUES (:staff_id, :work_day, NULL, NULL, 0, 1)
                                                      ON DUPLICATE KEY UPDATE 
                                                      start_time = NULL, 
                                                      end_time = NULL, 
                                                      day_off = 0,
                                                      open_schedule = 1");
                    } else {
                        $stmt = $this->conn->prepare("INSERT INTO schedules (staff_id, work_day, start_time, end_time, day_off, open_schedule) 
                                                      VALUES (:staff_id, :work_day, :start_time, :end_time, :day_off, 0)
                                                      ON DUPLICATE KEY UPDATE 
                                                      start_time = VALUES(start_time), 
                                                      end_time = VALUES(end_time), 
                                                      day_off = VALUES(day_off),
                                                      open_schedule = 0");
                        
                        $stmt->bindParam(':start_time', $workIn);
                        $stmt->bindParam(':end_time', $workOff);
                        $stmt->bindParam(':day_off', $dayOff);
                    }
                    
                    $stmt->bindParam(':staff_id', $staffId);
                    $stmt->bindParam(':work_day', $day);
                    $stmt->execute();
                }
            }
    
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return "Error updating fixed schedule: " . $e->getMessage();
        }
    }

    public function getTemporarySchedules($date) {
        $query = "SELECT ts.*, s.first_name, s.last_name, r.text as reason_text
                  FROM temp_schedule ts
                  JOIN staff_tbl s ON ts.staff_id = s.staff_id
                  LEFT JOIN reason_tbl r ON ts.reason_id = r.id
                  WHERE ts.date = :date
                  ORDER BY s.first_name, s.last_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $schedules = [];
        foreach ($results as $row) {
            $schedules[$row['staff_id']] = $row;
        }
        return $schedules;
    }

    public function getFixedSchedulesForDay($day) {
        $dayOfWeek = date('w', strtotime($day));
        // Adjust for systems where 0 is Sunday (shift index)
        $dayOfWeek = (date('w', strtotime($day)) + 6) % 7;
        $query = "SELECT s.staff_id, s.first_name, s.last_name, 
                         sch.start_time, sch.end_time, sch.day_off, sch.open_schedule
                  FROM staff_tbl s
                  LEFT JOIN schedules sch ON s.staff_id = sch.staff_id
                  WHERE s.attendance_req = 1
                  AND s.status = 1
                  AND sch.work_day = :day_of_week
                  ORDER BY s.first_name, s.last_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':day_of_week', $dayOfWeek, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $schedules = [];
        foreach ($results as $row) {
            $schedules[$row['staff_id']] = $row;
        }
        return $schedules;
    }

    public function updateTemporarySchedule($data) {
        $this->conn->beginTransaction();
        try {
            $date = $data['temp_date'];
            $staffIds = $data['temp_staff'] ?? [];
    
            foreach ($staffIds as $staffId) {
                $scheduledIn = !empty($data['work_in']) ? $data['work_in'] : null;
                $scheduledOut = !empty($data['work_off']) ? $data['work_off'] : null;
                
                // Handle day_off and open_schedule explicitly
                $dayOff = isset($data['day_off']) && $data['day_off'] == 1 ? 1 : 0;
                $openSchedule = isset($data['open_schedule']) && $data['open_schedule'] == 1 ? 1 : 0;
    
                $reasonId = $data['temp_reason'] ?? null;
    
                $stmt = $this->conn->prepare("INSERT INTO temp_schedule 
                (staff_id, date, scheduled_in, scheduled_out, day_off, open_schedule, reason_id)
                VALUES (:staff_id, :date, :scheduled_in, :scheduled_out, :day_off, :open_schedule, :reason_id)
                ON DUPLICATE KEY UPDATE 
                scheduled_in = VALUES(scheduled_in),
                scheduled_out = VALUES(scheduled_out),
                day_off = VALUES(day_off),
                open_schedule = VALUES(open_schedule),
                reason_id = VALUES(reason_id)");
    
                $stmt->bindParam(':staff_id', $staffId);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':scheduled_in', $scheduledIn);
                $stmt->bindParam(':scheduled_out', $scheduledOut);
                $stmt->bindParam(':day_off', $dayOff, PDO::PARAM_INT);
                $stmt->bindParam(':open_schedule', $openSchedule, PDO::PARAM_INT);
                $stmt->bindParam(':reason_id', $reasonId);
    
                $stmt->execute();
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return "Error updating temporary schedule: " . $e->getMessage();
        }
    }
    

    public function getReasons() {

        $query = "SELECT * FROM reason_tbl ORDER BY text";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

}