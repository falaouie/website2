<?php

    function dayID($dateInput) {
        $dayID;

        switch (date_format($dateInput, 'l')) {
            case 'Monday':
                $dayID = 1;
                break;
            case 'Tuesday':
                $dayID = 2;
                break;
            case 'Wednesday':
                $dayID = 3;
                break;
            case 'Thursday':
                $dayID = 4;
                break;
            case 'Friday':
                $dayID = 5;
                break;
            case 'Saturday':
                $dayID = 6;
                break;
            case 'Sunday':
                $dayID = 7;
                break;
        }
        return $dayID;
    }

    function dayName($dayID) {
        $dayName;

        switch ($dayID) {
            case 1:
                $dayName = 'Monday';
                break;
            case 2:
                $dayName = 'Tuesday';
                break;
            case 3:
                $dayName = 'Wednesday';
                break;
            case 4:
                $dayName = 'Thursday';
                break;
            case 5:
                $dayName = 'Friday';
                break;
            case 6:
                $dayName = 'Saturday';
                break;
            case 7:
                $dayName = 'Sunday';
                break;
        }
        return $dayName;
    }
