<?php

session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/connection/connection.inc.php';
require_once '../../functions/attendance.funcs.php';
if (isset($_POST['fixedSchdSubmit'])) {
    $staffIDArray = $_POST['staffID'];
    $daysArray = $_POST['dayID'];
    if (!empty($_POST['fromTime']) && !empty($_POST['toTime'])) {
        $fromTime = $_POST['fromTime'];
        $toTime = $_POST['toTime'];
    } else {
        $fromTime = NULL;
        $toTime = NULL;
    }
    if (!empty($_POST['dayOff'])) {
        $dayOff = $_POST['dayOff'];
    } else {
        $dayOff = 0;
    }
     $staffCount = count($staffIDArray);
     $daysCount = count($daysArray);
     $updateResult = 'false';
    for ($i=0; $i < $staffCount; $i++) {
        $staffID = $staffIDArray[$i];
        for ($j=0; $j < $daysCount; $j++) {
            $dayID = $daysArray[$j];
            $result = updateInsertStaffSchedule($conn,$staffID,$dayID,$fromTime,$toTime,$dayOff);
            if ($result != false) {
                $updateResult = 'true';
            }
        }
    }
    if ($updateResult != 'false') {
        if (isset($_SESSION['fxdUpdate'])) {
            unset($_SESSION['fxdUpdate']);
            unset($_SESSION['fxdUpdateMsg']);
        }
        $_SESSION['fxdUpdate'] = 'true';
        $_SESSION['fxdUpdateMsg'] = 'Schedule Updated Successfully !';
        header("location: index.php");
        exit();
    } else {
        if (isset($_SESSION['fxdUpdate'])) {
            unset($_SESSION['fxdUpdate']);
            unset($_SESSION['fxdUpdateMsg']);
        }
        $_SESSION['fxdUpdate'] = 'false';
        $_SESSION['fxdUpdateMsg'] = 'No Changes Made or Error In Updating Schedule !';
        header("location: index.php");
        exit();
    }
}
