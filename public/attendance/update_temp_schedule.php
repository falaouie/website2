<?php

session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/connection/connection.inc.php';
require_once '../../functions/attendance.funcs.php';
if (isset($_POST['tempSchdSubmit'])) {
//    echo 'wosil';
    $staffIDArray = $_POST['staffID'];
    $tempWorkDate = $_POST['workDate'];
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
    if (!empty($_POST['tempSchdReason'])) {
        $tempSchdReason = $_POST['tempSchdReason'];
    } else {
        $tempSchdReason = '';
    }
    if ($tempSchdReason == 'other') {
        $tempSchdReason = $_POST['tempSchdReasonSpecify'];
    }
     $staffCount = count($staffIDArray);
     $updateResult = 'false';

    for ($i=0; $i < $staffCount; $i++) {
        $staffID = $staffIDArray[$i];
            $result = updateInsertTempSchedule($conn,$staffID,$tempWorkDate,$fromTime,$toTime,$dayOff,$tempSchdReason);
            if ($result != false) {
                $updateResult = 'true';
            }
    }
    if (isset($_SESSION['tempUpdate'])) {
        unset($_SESSION['tempWorkDate']);
        unset($_SESSION['tempUpdate']);
        unset($_SESSION['tempUpdateMsg']);
    }
    $_SESSION['tempWorkDate'] = $tempWorkDate;

    if ($updateResult != 'false') {
        $_SESSION['tempUpdate'] = 'true';
        $_SESSION['tempUpdateMsg'] = 'Temporary Schedule Updated Successfully !';
        header("location: index.php");
        exit();
    } else {
        $_SESSION['tempUpdate'] = 'false';
        $_SESSION['tempUpdateMsg'] = 'No Changes Made or Error In Updating Temporary Schedule !';
        header("location: index.php");
        exit();
    }
}
