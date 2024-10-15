<?php 

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


public function getScheduleForDay($day) {
  // Step 1: Fetch the day of the week for fixed schedules
  $dayOfWeek = (date('w', strtotime($day)) + 6) % 7;

  // Step 2: Get temporary schedules for the given date
  $queryTemp = "SELECT ts.staff_id, ts.scheduled_in AS start_time, ts.scheduled_out AS end_time, 
                       ts.day_off, ts.open_schedule, s.first_name, s.last_name
                FROM temp_schedule ts
                JOIN staff_tbl s ON ts.staff_id = s.staff_id
                WHERE ts.date = :date
                ORDER BY s.first_name, s.last_name";
  
  $stmtTemp = $this->conn->prepare($queryTemp);
  $stmtTemp->bindParam(':date', $day);
  $stmtTemp->execute();
  $tempResults = $stmtTemp->fetchAll(PDO::FETCH_ASSOC);

  // Organize temporary schedules by staff_id for quick lookup
  $tempSchedules = [];
  foreach ($tempResults as $row) {
      $tempSchedules[$row['staff_id']] = $row;
  }

  // Step 3: Fetch fixed schedules for the given day of the week
  $queryFixed = "SELECT s.staff_id, s.first_name, s.last_name, 
                        sch.start_time, sch.end_time, sch.day_off, sch.open_schedule
                 FROM staff_tbl s
                 LEFT JOIN schedules sch ON s.staff_id = sch.staff_id
                 WHERE s.attendance_req = 1
                 AND s.status = 1
                 AND sch.work_day = :day_of_week
                 ORDER BY s.first_name, s.last_name";
  
  $stmtFixed = $this->conn->prepare($queryFixed);
  $stmtFixed->bindParam(':day_of_week', $dayOfWeek, PDO::PARAM_INT);
  $stmtFixed->execute();
  $fixedResults = $stmtFixed->fetchAll(PDO::FETCH_ASSOC);

  // Step 4: Merge temporary and fixed schedules, prioritizing temporary values
  $schedules = [];
  foreach ($fixedResults as $row) {
      $staffId = $row['staff_id'];
      if (isset($tempSchedules[$staffId])) {
          // If a temporary schedule exists, use its values, overriding fixed schedule
          $schedules[$staffId] = $tempSchedules[$staffId];
      } else {
          // Otherwise, use the fixed schedule
          $schedules[$staffId] = $row;
      }
  }

  // Step 5: Include any temporary schedules that do not have corresponding fixed schedules
  foreach ($tempSchedules as $staffId => $tempSchedule) {
      if (!isset($schedules[$staffId])) {
          $schedules[$staffId] = $tempSchedule;
      }
  }

  return $schedules;
}
