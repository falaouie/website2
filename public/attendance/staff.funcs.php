<?php

    function ActiveStaff($conn) {
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
                                'staffLastName' => $result['staff_last_name']
                            );
                $i++;
            }
            return $row;

        } else {
            $result = false;
            return $result;
        }
    };
