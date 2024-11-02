<?php

if ($staffID == 'allStaff' && $attendanceList) {
        ?>
        <div class="div-group">
            <div class="print-only">
                Attendance History
            </div>
            <div class="div-item">
                All Staff
            </div>
            <div class="div-item">
                &nbsp;
            </div>
            <?php
                if ($fromDate == $toDate) {
                    ?>
                    <div class="div-item greenText">
                        <?php
                            echo date('l', strtotime($fromDate));
                        ?>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="div-item greenText">
                        <?php
                        if (isset($_SESSION['fromDate'])) {
                            echo $fromDateFormatted;
                        } else {
                            echo $todayFormatted;
                        }
                        ?>
                    </div>
                    <div class="div-item">
                        &nbsp;
                    </div>
                    <div class="div-item">
                        To 
                    </div>
                    <div class="div-item greenText">
                        <?php
                        if (isset($_SESSION['toDate'])) {
                            echo $toDateFormatted;
                        } else {
                            echo $todayFormatted;
                        }
                        ?>
                    </div>
                    <?php
                } 
            ?>
            <div class="div-item">
                &nbsp;
            </div>
            <div class="div-item">
                <button class="btn btn-print screen-only" onclick="window.print()">Print</button>
            </div> 
        </div>



        <?php
            if ($attendanceList) {
                $totalHours = 0;
                foreach ($attendanceList as $row) {
                    if ($row['hours_worked']) {
                        ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars(formatTime($row['work_in'])); ?>
                            </td>
                            <td>
                                <?php
                                    if ($row['work_off']) {
                                        echo htmlspecialchars(formatTime($row['work_off']));
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    $hoursWorked = $row['hours_worked'];
                                    $totalHours = $totalHours + $hoursWorked;
                                    echo $hoursWorked;
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
            } else {
                ?>
                <tr>
                    <td class="redText" colspan="4">
                        <div class="div-group">
                            <strong>No Records Found</strong>
                        </div>
                    </td>
                </tr>
                <?php
            }
            
            ?>






        <table>
            <thead>
                <tr>
                    <th>
                        Name         
                    </th>
                    <th>
                        Work In
                    </th>
                    <th>
                        Work Off    
                    </th>
                    <th>
                        Hours
                    </th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
        <?php 
    // }
    
}