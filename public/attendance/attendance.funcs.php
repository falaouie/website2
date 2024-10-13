<?php

    function staffAttendanceList($conn,$workDayID,$todayDate) {

        $stmt = $conn->prepare("SELECT staff_id, staff_first_name, staff_last_name, staff_status
                                FROM staff_table
                                WHERE staff_status = :staffStatus
                                AND attendance_req = :attendReq
                                ORDER BY staff_first_name");

        $stmt->bindValue(':staffStatus', 1);
        $stmt->bindValue(':attendReq', 1);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            $i=0;
            while ($result = $stmt->fetch()) {
                $row[$i] = array('staffID' => $result['staff_id'],
                                'staffFirstName' => $result['staff_first_name'],
                                'staffLastName' => $result['staff_last_name'],
                                'staffStatus' => $result['staff_status']
                            );
                $staffID = $result['staff_id'];
                $staffTempFound = workTempDaySchedule($conn,$staffID,$todayDate);
                if ($staffTempFound  !== false) {
                    $row[$i]['staffScheduledIn'] = $staffTempFound[0]['staffTempIn'];
                    $row[$i]['staffScheduledOut'] = $staffTempFound[0]['staffTempOut'];
                    $row[$i]['staffDayOff'] = $staffTempFound[0]['staffTempDayOff'];
                } else {
                    $staffFound = workDaySchedule($conn,$staffID,$workDayID);
                    if ($staffFound !== false) {
                        $row[$i]['staffScheduledIn'] = $staffFound[0]['staffScheduledIn'];
                        $row[$i]['staffScheduledOut'] = $staffFound[0]['staffScheduledOut'];
                        $row[$i]['staffDayOff'] = $staffFound[0]['staffDayOff'];
                    } else {
                        $row[$i]['staffScheduledIn'] = null;
                        $row[$i]['staffScheduledOut'] = null;
                        $row[$i]['staffDayOff'] = 0;
                    }
                }
                $i++;
            }
            return $row;

        } else {
            $result = false;
            return $result;
        }
    };

    function staffFixedAttendanceList($conn,$workDayID) {
        $stmt = $conn->prepare("SELECT staff_id, staff_first_name, staff_last_name, staff_status
                                FROM staff_table
                                WHERE staff_status = :staffStatus
                                AND attendance_req = :attendReq
                                ORDER BY staff_first_name");

        $stmt->bindValue(':staffStatus', 1);
        $stmt->bindValue(':attendReq', 1);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            $i=0;
            while ($result = $stmt->fetch()) {
                $row[$i] = array('staffID' => $result['staff_id'],
                                'staffFirstName' => $result['staff_first_name'],
                                'staffLastName' => $result['staff_last_name'],
                                'staffStatus' => $result['staff_status']
                            );
                $staffID = $result['staff_id'];
                $staffFound = workDaySchedule($conn,$staffID,$workDayID);

                if ($staffFound !== false) {
                    $row[$i]['staffScheduledIn'] = $staffFound[0]['staffScheduledIn'];
                    $row[$i]['staffScheduledOut'] = $staffFound[0]['staffScheduledOut'];
                    $row[$i]['staffDayOff'] = $staffFound[0]['staffDayOff'];
                } else {
                    $row[$i]['staffScheduledIn'] = null;
                    $row[$i]['staffScheduledOut'] = null;
                    $row[$i]['staffDayOff'] = 0;
                }
                $i++;
            }
            return $row;

        } else {
            $result = false;
            return $result;
        }
    }

    function workDaySchedule($conn,$staffID,$workDayID) {

        $stmt = $conn->prepare("SELECT schedule_in_hour, schedule_out_hour, day_off
                              FROM attendance_schedule
                              WHERE week_day_id = :workDayID
                              AND staff_id = :staffID");

        $stmt->bindValue(':staffID', $staffID);
        $stmt->bindValue(':workDayID', $workDayID);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            while ($result = $stmt->fetch()) {
                $row[] = array( 'staffScheduledIn' => $result['schedule_in_hour'],
                                'staffScheduledOut' => $result['schedule_out_hour'],
                                'staffDayOff' => $result['day_off']
                              );
                            }
                return $row;
        } else {
            $result = false;
            return $result;
        }
    };

    function staffTempAttendanceList($conn,$tempDayDate) {

        $stmt = $conn->prepare("SELECT staff_id, staff_first_name, staff_last_name, staff_status
                                FROM staff_table
                                WHERE staff_status = :staffStatus
                                AND attendance_req = :attendReq
                                ORDER BY staff_first_name");

        $stmt->bindValue(':staffStatus', 1);
        $stmt->bindValue(':attendReq', 1);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            $i=0;
            while ($result = $stmt->fetch()) {
                $row[$i] = array('staffID' => $result['staff_id'],
                                'staffFirstName' => $result['staff_first_name'],
                                'staffLastName' => $result['staff_last_name'],
                                'staffStatus' => $result['staff_status']
                            );
                $staffID = $result['staff_id'];
                $staffTempFound = workTempDaySchedule($conn,$staffID,$tempDayDate);

                if ($staffTempFound !== false) {
                    $row[$i]['staffTempIn'] = $staffTempFound[0]['staffTempIn'];
                    $row[$i]['staffTempOut'] = $staffTempFound[0]['staffTempOut'];
                    $row[$i]['staffTempDayOff'] = $staffTempFound[0]['staffTempDayOff'];
                    $row[$i]['staffTempSchdReason'] = $staffTempFound[0]['staffTempSchdReason'];
                } else {
                    $row[$i]['staffTempIn'] = null;
                    $row[$i]['staffTempOut'] = null;
                    $row[$i]['staffTempDayOff'] = 0;
                    $row[$i]['staffTempSchdReason'] = '';
                }
                $i++;
            }
            return $row;

        } else {
            $result = false;
            return $result;
        }
    };

    function workTempDaySchedule($conn,$staffID,$tempDayDate) {

        $stmt = $conn->prepare("SELECT temp_schd_in_time, temp_schd_out_time, temp_schd_day_off, temp_schd_reason
                              FROM attendance_temp_schedule
                              WHERE temp_schd_date = :tempDayDate
                              AND staff_id = :staffID");

        $stmt->bindValue(':staffID', $staffID);
        $stmt->bindValue(':tempDayDate', $tempDayDate);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            while ($result = $stmt->fetch()) {
                $row[] = array( 'staffTempIn' => $result['temp_schd_in_time'],
                                'staffTempOut' => $result['temp_schd_out_time'],
                                'staffTempDayOff' => $result['temp_schd_day_off'],
                                'staffTempSchdReason' => $result['temp_schd_reason']
                              );
                            }
                return $row;
        } else {
            $result = false;
            return $result;
        }
    };

    function staffSchedule($conn,$staffID) {
        $stmt = $conn->prepare("SELECT staff_id, staff_first_name, staff_last_name,week_day_id,
                                        schedule_in_hour, schedule_out_hour, day_off
                              FROM staff_table
                              JOIN attendance_schedule USING(staff_id)
                              WHERE staff_id =:staffID
                              ORDER BY week_day_id");

        $stmt->bindValue(':staffID', $staffID);
        $stmt->execute();
        $resultCount=$stmt->rowCount();
        if ($resultCount > 0) {
            while ($result = $stmt->fetch()) {
                $row[] = array('workDayID' => $result['week_day_id'],
                                'workInTime' => $result['schedule_in_hour'],
                                'workOffTime' => $result['schedule_out_hour'],
                                'staffDayOff' => $result['day_off']);
                                }
                return $row;

        } else {
            $row[] = array('workDayID' => null,
                            'workInTime' => null,
                            'workOffTime' => null,
                            'staffDayOff' => 0);
            return $row;
            }
    }

    function updateInsertStaffSchedule($conn,$staffID,$dayID,$fromTime,$toTime,$dayOff) {

        $stmt = $conn->prepare("SELECT staff_id, week_day_id
                              FROM attendance_schedule
                              WHERE staff_id = :staffID
                              AND week_day_id = :dayID");

        $stmt->bindValue(':staffID', $staffID);
        $stmt->bindValue(':dayID', $dayID);
        $stmt->execute();
        $resultCount=$stmt->rowCount();
        if ($resultCount > 0) {
            $result = updateStaffSchedule($conn,$staffID,$dayID,$fromTime,$toTime,$dayOff);
            return $result;
        } else {
            $result = insertStaffSchedule($conn,$staffID,$dayID,$fromTime,$toTime,$dayOff);
            return $result;
        }
    }

    function updateStaffSchedule($conn,$staffID,$dayID,$fromTime,$toTime,$dayOff) {
        $stmt = $conn->prepare("UPDATE attendance_schedule
                  SET 	schedule_in_hour=:staffFromTime,schedule_out_hour=:staffToTime,day_off=:staffDayOff
                  WHERE staff_id=:staffID
                  AND week_day_id=:staffWorkDay");

        $stmt->bindValue(':staffID', $staffID);
        $stmt->bindValue(':staffWorkDay', $dayID);
        $stmt->bindValue(':staffFromTime', $fromTime);
        $stmt->bindValue(':staffToTime', $toTime);
        $stmt->bindValue(':staffDayOff', $dayOff);
        $stmt->execute();
        $resultCount=$stmt->rowCount();
        if ($resultCount > 0) {
            $result = true;
            return $result;
        } else {
            $result = false;
            return $result;
        }
    };

    function insertStaffSchedule($conn,$staffID,$dayID,$fromTime,$toTime,$dayOff) {
        $stmt = $conn->prepare("INSERT INTO attendance_schedule(staff_id ,week_day_id, schedule_in_hour, schedule_out_hour, day_off)
          VALUES (:staffID,:staffWorkDay,:staffFromTime,:staffToTime,:staffDayOff)");

        $stmt->bindValue(':staffID', $staffID);
        $stmt->bindValue(':staffWorkDay', $dayID);
        $stmt->bindValue(':staffFromTime', $fromTime);
        $stmt->bindValue(':staffToTime', $toTime);
        $stmt->bindValue(':staffDayOff', $dayOff);
        $stmt->execute();
        $resultCount=$stmt->rowCount();
        if ($resultCount > 0) {
            $result = true;
            return $result;
        } else {
            $result = false;
            return $result;
        }
    };

function updateInsertTempSchedule($conn,$staffID,$tempWorkDate,$fromTime,$toTime,$dayOff,$tempSchdReason) {

    $stmt = $conn->prepare("SELECT staff_id, temp_schd_date
                          FROM attendance_temp_schedule
                          WHERE staff_id = :staffID
                          AND temp_schd_date = :tempWorkDate");

    $stmt->bindValue(':staffID', $staffID);
    $stmt->bindValue(':tempWorkDate', $tempWorkDate);
    $stmt->execute();
    $resultCount=$stmt->rowCount();
    if ($resultCount > 0) {
        $result = updateTempSchedule($conn,$staffID,$tempWorkDate,$fromTime,$toTime,$dayOff,$tempSchdReason);
        return $result;
    } else {
        $result = insertTempSchedule($conn,$staffID,$tempWorkDate,$fromTime,$toTime,$dayOff,$tempSchdReason);
        return $result;
    }
};

function updateTempSchedule($conn,$staffID,$tempWorkDate,$fromTime,$toTime,$dayOff,$tempSchdReason) {
    $stmt = $conn->prepare("UPDATE attendance_temp_schedule
              SET 	temp_schd_in_time=:staffFromTime,temp_schd_out_time=:staffToTime,temp_schd_day_off=:staffDayOff,temp_schd_reason=:tempSchdReason
              WHERE staff_id=:staffID
              AND temp_schd_date=:tempWorkDate");

    $stmt->bindValue(':staffID', $staffID);
    $stmt->bindValue(':tempWorkDate', $tempWorkDate);
    $stmt->bindValue(':staffFromTime', $fromTime);
    $stmt->bindValue(':staffToTime', $toTime);
    $stmt->bindValue(':staffDayOff', $dayOff);
    $stmt->bindValue(':tempSchdReason', $tempSchdReason);
    $stmt->execute();
    $resultCount=$stmt->rowCount();
    if ($resultCount > 0) {
        $result = true;
        return $result;
    } else {
        $result = false;
        return $result;
    }
};


function insertTempSchedule($conn,$staffID,$tempWorkDate,$fromTime,$toTime,$dayOff,$tempSchdReason) {
    $stmt = $conn->prepare("INSERT INTO attendance_temp_schedule(staff_id , temp_schd_date, temp_schd_in_time, temp_schd_out_time, temp_schd_day_off, temp_schd_reason)
      VALUES (:staffID,:tempWorkDate,:staffFromTime,:staffToTime,:staffDayOff,:tempSchdReason)");

    $stmt->bindValue(':staffID', $staffID);
    $stmt->bindValue(':tempWorkDate', $tempWorkDate);
    $stmt->bindValue(':staffFromTime', $fromTime);
    $stmt->bindValue(':staffToTime', $toTime);
    $stmt->bindValue(':staffDayOff', $dayOff);
    $stmt->bindValue(':tempSchdReason', $tempSchdReason);
    $stmt->execute();
    $resultCount=$stmt->rowCount();
    if ($resultCount > 0) {
        $result = true;
        return $result;
    } else {
        $result = false;
        return $result;
    }
};

function staffAttendance($conn,$userID,$formattedToday) {
        unset($row);
        $stmt = $conn->prepare("SELECT work_in,work_off,TIMEDIFF(work_off, work_in) AS TimeDiff
              FROM attendance_sheet
              WHERE staff_id=:userID
              AND work_date=:workDate");

        $stmt->bindValue(':userID', $userID);
        $stmt->bindValue(':workDate', $formattedToday);

        $stmt->execute();
        $result = $stmt->fetch();
        $workInCount=$stmt->rowCount();
        if ($workInCount > 0) {
            $row['workIN'] = $result['work_in'];
            $row['workOff'] = $result['work_off'];
            $row['totalHours'] = $result['TimeDiff'];
        } else {
            $row['workIN'] = 0;
            $row['workOff'] = 0;
            $row['totalHours'] = 0;
        }
        $breakStatus = BreakStatus($conn,$userID,$formattedToday);
        $row['breakStatus'] = $breakStatus;
        $breakInTime = BreakTime($conn,$userID,$formattedToday);
        $row['breakInTime'] = $breakInTime;
        return $row;
    };

    function BreakStatus($conn,$userID,$formattedToday) {
        unset($result);
        $stmt = $conn->prepare("SELECT break_status
                                FROM attendance_breaks
                                WHERE staff_id=:userID
                                AND break_status = (SELECT MAX(break_status)
                                FROM attendance_breaks
                                WHERE staff_id=:userID
                                AND breaks_date=:breaksDate)");

        $stmt->bindValue(':userID', $userID);
        $stmt->bindValue(':breaksDate', $formattedToday);

        $stmt->execute();
        $result = $stmt->fetch();
        $breakStatusCount=$stmt->rowCount();
        if ($breakStatusCount > 0) {
            $breakStatus = $result['break_status'];
        } else {
            $breakStatus = 0;
        }
        return $breakStatus;
    };

    function BreakTime($conn,$userID,$formattedToday) {
        unset($result);
        $stmt = $conn->prepare("SELECT break_in_time
                                FROM attendance_breaks
                                WHERE staff_id=:userID
                                AND break_in_time = (SELECT MAX(break_in_time)
                                FROM attendance_breaks
                                WHERE staff_id=:userID
                                AND breaks_date=:breaksDate)");

        $stmt->bindValue(':userID', $userID);
        $stmt->bindValue(':breaksDate', $formattedToday);

        $stmt->execute();
        $result = $stmt->fetch();
        $breakStatusCount=$stmt->rowCount();
        if ($breakStatusCount > 0) {
            $breakInTime = $result['break_in_time'];
        } else {
            $breakInTime = 0;
        }
        return $breakInTime;
    };

    function staffAttendanceListByStatus($conn,$status) {
        $stmt = $conn->prepare("SELECT staff_id,staff_first_name,staff_last_name,staff_status
                                FROM staff_table
                                WHERE attendance_req = :attendReq
                                AND staff_status = :status
                                ORDER BY staff_first_name");

        $stmt->bindValue(':attendReq', 1);
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            while ($result = $stmt->fetch()) {
                $row[] = array('staffID' => $result['staff_id'],
                                'staffFirstName' => $result['staff_first_name'],
                                'staffLastName' => $result['staff_last_name']
                            );
            }
            return $row;

        } else {
            $result = false;
            return $result;
        }
    };

    function staffAttendanceListAll($conn) {
        $stmt = $conn->prepare("SELECT staff_id,staff_first_name,staff_last_name,staff_status
                                FROM staff_table
                                WHERE attendance_req = :attendReq
                                ORDER BY staff_first_name");

        $stmt->bindValue(':attendReq', 1);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            while ($result = $stmt->fetch()) {
                $row[] = array('staffID' => $result['staff_id'],
                                'staffFirstName' => $result['staff_first_name'],
                                'staffLastName' => $result['staff_last_name']
                            );
            }
            return $row;

        } else {
            $result = false;
            return $result;
        }
    };
/*
    function attendanceDatesRange($conn) {
        $stmt = $conn->prepare("SELECT work_date
                                FROM attendance_sheet
                                ORDER BY work_date ASC LIMIT 1");

        $stmt->execute();
        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            while ($result = $stmt->fetch()) {
                $row = $result['work_date'];
            }
            return $row;
        }
    };
*/
