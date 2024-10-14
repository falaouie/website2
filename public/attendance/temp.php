<?php foreach ($fixedSchedules as $schedule): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($schedule['first_name'] . ' ' . $schedule['last_name']); ?></td>
                        <th><input type="checkbox"></th> <!-- check staff -->
                        <td></td> <!-- temp scheduled_in -->
                        <td></td> <!-- temp scheduled_out -->
                        <td></td> <!-- reason text -->
                        <td>
                            <?php
                            if ($schedule['open_schedule']) {
                                echo "Open";
                            } elseif ($schedule['day_off']) {
                                echo "DAY OFF";
                            } else {
                                echo htmlspecialchars($schedule['start_time']);
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($schedule['open_schedule']) {
                                echo "Open";
                            } elseif ($schedule['day_off']) {
                                echo "DAY OFF";
                            } else {
                                echo htmlspecialchars($schedule['end_time']);
                            }
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>