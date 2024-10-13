<?php

session_start();

require_once '../../includes/functions.php';

require_once '../../includes/User.php';

require_once '../../includes/auth.php';

requireLogin();



$user = new User(getDbConnection());



    ?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Fixed Schedules - Silver System</title>

    <link rel="stylesheet" href="../assets/css/styles.css">

    <style>

        .schedule-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }

        .schedule-table th, .schedule-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }

        .schedule-table th { background-color: #f2f2f2; }

        .staff-name { text-align: left; }

        .day-off { color: red; }

        .hidden { display: none; }

        #scheduleOptions > div {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 10px;
            vertical-align: top;
        }
        #scheduleOptions label {
            display: inline-block;
            margin-right: 10px;
        }
        #scheduleOptions input[type="radio"],
        #scheduleOptions input[type="time"] {
            vertical-align: middle;
        }
        #submitButton {
            display: block;
            margin-top: 10px;
        }

    </style>

</head>
<body>
    <table>
        <tr>
            <td align="center">
                <strong>Staff Schedule</strong>
            </td>
        </tr>
        <tr>
            <td align="center">
               <div class="inline" id="fixedSchdDiv" style="background: orange;padding: 10px;outline: 1px solid black;">
                   <strong><label for="fixedSchd">FIXED</label></strong>
                   <input class="screen-only" id="fixedSchd" type="radio" name="staffSchd" value="fixed" onclick="editSchedule();"  style="outline: 1px solid black;">
               </div>
                <div class="inline" id="tempSchdDiv" style="background: orange;padding: 10px;outline: 1px solid black;">
                    <strong><label for="tempSchd">TEMPORARY</label></strong>
                    <input class="screen-only" id="tempSchd" type="radio" name="staffSchd" value="temp" onclick="editSchedule();" style="outline: 1px solid black;">
                </div>
            </td>
        </tr>
        <tr class="screen-only" id="updateFixedMsgTr" style="display:none">
            <td align="center"  rowspan="2">
                <div class="screen-only inline">
                    <div class="" id="fxdUpdateMsg">
                        <strong>
                            <?php
                                echo $fxdUpdateMsg;
                             ?>
                        </strong>&nbsp;
                    </div>
                    <div class="inline">
                        <input class="deleteButton" type="submit" name="" value="" onclick="displayNone('updateFixedMsgTr')">
                    </div>
                </div>
            </td>
        </tr>
        <tr class="screen-only" id="updateTempMsgTr" style="display:none">
            <td align="center"  rowspan="2">
                <div class="screen-only">
                    <div class="inline" id="tempUpdateMsg">
                        <strong>
                            <?php
                                echo $tempUpdateMsg;
                             ?>
                        </strong>&nbsp;
                    </div>
                    <div class="inline">
                        <input class="deleteButton" type="submit" name="" value="" onclick="displayNone('updateTempMsgTr')">
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <table id="fxdSchdTbl" border="1" style="background: white;display: none;">
            <form class="" action="update_fixed_schedule.php" method="post">
            <input id="staffCnt" type="hidden" name="" value="<?= $staffScheduleCnt ?>">
        <tr>
            <td align="right" style="padding: 5px;" colspan="2">
                <div id="checkAllDaysDiv" class="screen-only">
                    <strong>Check All Days</strong>
                    <input id="allDaysCheck" type="checkbox" onclick="checkAllDays();">
                </div>
            </td>
            <?php
            for ($i=1; $i <= 7; $i++) {
                $weekDay = dayName($i);
                ?>
                <td id="dayIDtd<?= $i ?>" align="center" colspan="2" style="padding: 5px;">
                    <div>
                        <div class="">
                            <strong><?= $weekDay; ?></strong>
                        </div>
                        <div id="dayIDDiv<?= $i ?>" class="screen-only">
                            <input id="dayID<?= $i ?>" type="checkbox" name="dayID[]" value="<?= $i ?>" onclick="daysCheckBox(<?= $i ?>);">
                        </div>
                    </div>
                </td>
                <?php
            }
             ?>
        </tr>
        <tr>
            <td align="center" style="padding: 5px;" colspan="2">
                <div id="checkAllStaffDiv" class="screen-only">
                    <strong>Check All Staff</strong>
                    <input id="staffCheckAll" type="checkbox" onclick="checkAllStaff();">
                </div>
            </td>
            <?php
            for ($i=0; $i < 7 ; $i++) {
                ?>
                <td align="center"><strong>From</strong></td>
                <td align="center"><strong>To</strong></td>
                <?php
            }
             ?>
        </tr>
        <?php
        for ($i=0; $i < $staffScheduleCnt; $i++) {
            $staffID = $weekSchedule[$i+1][$i]['staffID'];
            $staffFirstName = $weekSchedule[$i+1][$i]['staffFirstName'];
            $staffLastName = $weekSchedule[$i+1][$i]['staffLastName'];
            $userName = $staffFirstName.' '.$staffLastName;
            ?>
            <tr>
                <td id="staffCheckBoxTd<?= $i ?>" align="left" style="padding: 5px;">
                    <div>
                        <strong><?= $userName ?></strong>
                    </div>
                </td>
                <td>
                    <div id="staffCheckBoxDiv<?= $i ?>" class="screen-only">
                        <input id="staffCheckBox<?= $i ?>" type="checkbox" name="staffID[]" value="<?= $staffID ?>" onclick="staffCheckBox(<?= $i ?>);">
                    </div>
                </td>
                <?php
                for ($j=1; $j <= 7; $j++) {
                    $staffScheduledIn = $weekSchedule[$j][$i]['staffScheduledIn'];
                    $staffScheduledOut = $weekSchedule[$j][$i]['staffScheduledOut'];
                    $staffDayOff = $weekSchedule[$j][$i]['staffDayOff'];
                    $staffStatus = $weekSchedule[$j][$i]['staffStatus'];
                    if ($staffStatus === '2') {
                        ?>
                        <td id="staffWorkIN<?=$i ?>day<?= $j ?>" align="center">
                            VACATION
                        </td>
                        <td id="staffWorkOFF<?=$i ?>day<?= $j ?>">
                            VACATION
                        </td>
                        <?php
                    } elseif ($staffDayOff  === '1') {
                        ?>
                        <td id="staffWorkIN<?=$i ?>day<?= $j ?>" align="center" style="padding: 10px;">
                            DAY OFF
                        </td>
                        <td id="staffWorkOFF<?=$i ?>day<?= $j ?>" align="center" style="padding: 10px;">
                            DAY OFF
                        </td>
                        <?php
                    } else {
                        ?>
                        <td id="staffWorkIN<?=$i ?>day<?= $j ?>" align="center" style="padding: 10px;">
                            <?php
                            if ($staffScheduledIn != null) {
                                echo date('h:i A',strtotime($staffScheduledIn));
                            }
                             ?>
                        </td>
                        <td id="staffWorkOFF<?=$i ?>day<?= $j ?>" align="center" style="padding: 10px;">
                            <?php
                            if ($staffScheduledOut != null) {
                                echo date('h:i A',strtotime($staffScheduledOut));
                            }
                             ?>
                        </td>
                        <?php
                    }
                }
                 ?>
            </tr>
            <?php
        }
         ?>
         <tr id="fxdSelectTr" class="screen-only" style="display:none;">
             <td align="center" colspan="16" style="padding: 10px;">
                 <div id="fixedSelectionsDiv" style="display: none;">
                     <div>
                        <strong>Open Schedule</strong>&nbsp;
                        <div id="openSchdNoDiv" style="padding: 10px;outline: 1px solid black;">
                            <strong><label for="openSchdNo">No</label></strong>
                            <input id="openSchdNo" type="radio" name="setSchd" value="0" onclick="openSchedule();">
                        </div>
                         <div id="openSchdYesDiv" style="padding: 10px;outline: 1px solid black;">
                             <strong><label for="openSchdYes">Yes</label></strong>
                             <input id="openSchdYes" type="radio" name="setSchd" value="1" onclick="openSchedule();">
                         </div>
                     </div>
                     <div id="dayOffDiv" style="display: none;">
                        &nbsp;<strong>Day OFF</strong>&nbsp;
                        <div id="dayOffNoDiv" style="padding: 10px;outline: 1px solid black;">
                            <strong><label for="dayOffNo">No</label></strong>
                            <input id="dayOffNo" disabled type="radio" name="dayOff" value="0" onclick="dayOffCheck();">
                        </div>
                         <div id="dayOffYesDiv" style="padding: 10px;outline: 1px solid black;">
                             <strong><label for="dayOffYes">Yes</label></strong>
                             <input id="dayOffYes" disabled type="radio" name="dayOff" value="1" onclick="dayOffCheck();">
                         </div>
                     </div>
                     <div id="timeDiv" style="padding: 10px;outline: 1px solid black; display: none;">
                         <div>
                            <strong>Work IN</strong>&nbsp;
                             <input disabled id="staffFromTime" type="time" name="fromTime" value="" onchange="fromTimeChange();">
                         </div>
                         <div>
                            <strong>Work OFF</strong>&nbsp;
                             <input disabled id="staffToTime" type="time" name="toTime" value="" onchange="toTimeChange();">
                         </div>
                     </div>
                     <div id="fxdSubmitDiv" style="display: none;">
                         <button class="submit-green-button" type="submit" name="fixedSchdSubmit">SUBMIT</button>
                        </form>
                     </div>
                 </div>
             </td>
         </tr>
    </table>
    <table id="tempSchdTbl" border="1" style="display: none;background:white;">
         <tr class="screen-only">
             <td align="left" colspan="7">
                <form id="histDateSelectForm" action="" method="post">
                    Select Date
                    <input id="tempSchdDateInput" type="date" name="selectTempDate" min="<?= $formattedToday ?>" max="9999-12-31" value="<?= $tempWorkDate ?>" style="background:orange;" onchange="dateInputSubmit('tempSchdDateInput','tempSchdSubmitBtn')" required>
                 <div>
                     <button disabled id="tempSchdSubmitBtn" class="small-green-btn" type="submit" style="display:none;">SUBMIT</button>
                    </form>
                 </div>
             </td>
         </tr>
         <tr>
             <td align="left" width="25%">&nbsp;</td>
             <td class="screen-only" width="5%">&nbsp;</td>
             <td align="center" colspan="3">
                 <div class="greenMsg">
                   <strong>
                     <?= date_format($createDate, 'l'); ?>
                     <?= date_format($createDate, 'd-m-Y'); ?>
                   </strong>
                 </div>
             </td>
             <td class="screen-only" align="center" width="30%" colspan="2">
                 <div class="">
                    <strong><?= date_format($createDate, 'l'); ?></strong>
                 </div>
                 <div class="">
                     Fixed Schedule
                 </div>
             </td>
         </tr>
         <tr>
             <td align="left">
                 Staff Name
             </td>
             <td class="screen-only" align="center">
                 <input id="tempAllStaffCheck" type="checkbox" onclick="tempSelectAllStaff();">&nbsp;All
             </td>
             <td align="center">From Time</td>
             <td align="center">To Time</td>
             <td align="center">Reason</td>
             <td class="screen-only" align="center">From Time</td>
             <td class="screen-only" align="center">To Time</td>
         </tr>
        <?php
            for ($i=0; $i < $staffScheduleCnt; $i++) {
                $staffID = $weekSchedule[$i+1][$i]['staffID'];
                $staffFirstName = $weekSchedule[$i+1][$i]['staffFirstName'];
                $staffLastName = $weekSchedule[$i+1][$i]['staffLastName'];
                $userName = $staffFirstName.' '.$staffLastName;
                $staffScheduledIn = $weekSchedule[$workDayID][$i]['staffScheduledIn'];
                $staffScheduledOut = $weekSchedule[$workDayID][$i]['staffScheduledOut'];
                $staffDayOff = $weekSchedule[$workDayID][$i]['staffDayOff'];
                $staffStatus = $weekSchedule[$workDayID][$i]['staffStatus'];

            $staffTempIn = $tempAttendanceList[$i]['staffTempIn'];
            $staffTempOut = $tempAttendanceList[$i]['staffTempOut'];
            $staffTempDayOff = $tempAttendanceList[$i]['staffTempDayOff'];
            $staffTempSchdReason = $tempAttendanceList[$i]['staffTempSchdReason'];
            ?>
            <form class="" action="update_temp_schedule.php" method="post">
            <tr>
                <td id="tempStaffCheckBoxTd<?= $i ?>" align="left" style="padding: 5px;">
                    <div>
                        <strong><?= $userName ?></strong>
                    </div>
                </td>
                <td class="screen-only" align="center">
                    <div id="tempStaffCheckBoxDiv<?= $i ?>" class="screen-only">
                        <input id="tempStaffCheckBox<?= $i ?>" type="checkbox" name="staffID[]" value="<?= $staffID ?>" onclick="tempStaffCheckBox(<?= $i ?>);">
                    </div>
                </td>
                <td id="tempStaffIN<?= $i ?>" align="center">
                    <div class="">
                        <?php
                        if ($staffTempDayOff  === '1') {
                            ?>
                            DAY OFF
                            <?php
                        } elseif ($staffTempIn != null) {
                            echo date('h:i A',strtotime($staffTempIn));
                        }
                         ?>
                    </div>
                </td>
                <td id="tempStaffOUT<?= $i ?>" align="center">
                    <div class="">
                        <?php
                        if ($staffTempDayOff  === '1') {
                            ?>
                            DAY OFF
                            <?php
                        } elseif ($staffTempOut != null) {
                            echo date('h:i A',strtotime($staffTempOut));
                        }
                         ?>
                    </div>
                </td>
                <td id="tempTDSchdReason<?= $i ?>" align="center">
                    <?php
                    echo $staffTempSchdReason;
                     ?>
                </td>
                <td class="screen-only" align="center">
                    <div class="">
                        <?php
                        if ($staffDayOff  === '1') {
                            ?>
                            DAY OFF
                            <?php
                        } elseif ($staffScheduledIn != null) {
                            echo date('h:i A',strtotime($staffScheduledIn));
                        }
                         ?>
                    </div>
                </td>
                <td class="screen-only" align="center">
                    <div class="">
                        <?php
                        if ($staffDayOff  === '1') {
                            ?>
                            DAY OFF
                            <?php
                        } elseif ($staffScheduledOut != null) {
                            echo date('h:i A',strtotime($staffScheduledOut));
                        }
                         ?>
                    </div>
                </td>
            </tr>
            <?php
            }
         ?>
             <input type="hidden" name="workDate" value="<?= $tempWorkDate ?>">
         <tr class="screen-only">
             <td align="center" colspan="7" style="padding: 10px;">
                 <div id="tempSelectionsDiv" style="display:none">
                     <div style="padding: 10px;outline: 1px solid black;">
                         <strong>Select Reason</strong>
                     </div>
                     <div id="tempSchdReasonSelectDiv" style="padding: 10px;outline: 1px solid black;background:orange;">
                         <select id="tempSchdReasonSelect" name="tempSchdReason" required onchange="tempSchdReasonSelection()">
                             <option value="">Select</option>
                             <option value="Doctor Appointment">Doctor Appointment</option>
                             <option value="Family Emergency">Family Emergency</option>
                             <option value="Holiday">Public Holiday</option>
                             <option value="other">Other</option>
                         </select>
                     </div>
                     <div id="tempSchdReasonSpecifyDiv" style="display:none;padding: 10px;outline: 1px solid black;">
                         Please Specify
                         <input id="tempSchdReasonInput" disabled type="text" name="tempSchdReasonSpecify" value="" required="false" autocomplete="off" onchange="tempSchdReasonSelection()">
                     </div>
                     <div id="tempDayOffDiv" style="display:none">
                        &nbsp;<strong>Day OFF</strong>&nbsp;
                        <div id="tempDayOffNoDiv" style="padding: 10px;outline: 1px solid black;">
                            <strong><label for="dayOffNo">No</label></strong>
                            <input id="tempDayOffNo" disabled type="radio" name="dayOff" value="0" onclick="tempDayOffCheck();">
                        </div>
                         <div id="tempDayOffYesDiv" style="padding: 10px;outline: 1px solid black;">
                             <strong><label for="dayOffYes">Yes</label></strong>
                             <input id="tempDayOffYes" disabled type="radio" name="dayOff" value="1" onclick="tempDayOffCheck();">
                         </div>
                     </div>
                     <div id="tempTimeDiv" style="padding: 10px;outline: 1px solid black;display:none;">
                         <div>
                            <strong>Work IN</strong>&nbsp;
                             <input disabled id="tempStaffFromTime" type="time" name="fromTime" value="" onchange="tempFromTimeChange();">
                         </div>
                         <div>
                            <strong>Work OFF</strong>&nbsp;
                             <input disabled id="tempStaffToTime" type="time" name="toTime" value="" onchange="tempToTimeChange();">
                         </div>
                     </div>
                     <div id="tempSubmitDiv" style="display:none">
                         <button id="tempSchdSubmit" class="submit-green-button" type="submit" name="tempSchdSubmit">SAVE</button>
                        </form>
                     </div>
                 </div>
             </td>
         </tr>
    </table>

</body>
</html>
