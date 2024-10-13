function setArraySession() {
    var staffCnt = getValue('staffCnt');
    // think of another way to comunicate staffCnt
    // staffCnt = staffCnt();
    var sessionArray = {}; // dictionary
    for (var i = 0; i < staffCnt; i++) {
        for (var j = 1; j <= 7; j++) {
            var resultIN = getInnerHTML('staffWorkIN'+i+'day'+j);
            var resultOUT = getInnerHTML('staffWorkOFF'+i+'day'+j);
            sessionArray['staffWorkIN'+i+'day'+j] = resultIN;
            sessionArray['staffWorkOFF'+i+'day'+j] = resultOUT;
        }
    }
 return sessionArray;
};

function setTempArraySession() {
    var staffCnt = getValue('staffCnt');
    // think of another way to comunicate staffCnt
    // staffCnt = staffCnt();
    var tempSessionArray = {}; // dictionary
    for (var i = 0; i < staffCnt; i++) {
            var tempResultIN = getInnerHTML('tempStaffIN'+i);
            var tempResultOUT = getInnerHTML('tempStaffOUT'+i);
            var tempReason = getInnerHTML('tempTDSchdReason'+i);
            tempSessionArray['tempStaffIN'+i] = tempResultIN;
            tempSessionArray['tempStaffOUT'+i] = tempResultOUT;
            tempSessionArray['tempTDSchdReason'+i] = tempReason;
    }
return tempSessionArray;
};

function editSchedule() {
    var x = getValue('staffCnt');

    if (isChecked('fixedSchd')) {
        displayTable('fxdSchdTbl');
        displayNone('tempSchdTbl');
        setBgColor('fixedSchdDiv','limegreen');
        setBgColor('tempSchdDiv','white');
        displayNone('updateFixedMsgTr');
        setClassName('tempSchdDiv','screen-only');
        setClassName('fixedSchdDiv','');
        unCheckThis('tempAllStaffCheck');
        for (var i = 0; i < x; i++) {
            unCheckThis('tempStaffCheckBox'+i);
            setBgColor('tempStaffCheckBoxTd'+i,'');
            setStyleOutline('tempStaffIN'+i,'');
            setStyleOutline('tempStaffOUT'+i,'');
            setStyleOutline('tempTDSchdReason'+i,'');
        }
        fillTempSchedule('clear');
        tempHideSelectionsDiv();
        //setArraySession();
    }

    if (isChecked('tempSchd')) {
        displayTable('tempSchdTbl');
        setBgColor('tempSchdDiv','limegreen');
        setBgColor('fixedSchdDiv','white');
        displayNone('fxdSchdTbl');
        displayNone('updateFixedMsgTr');
        setClassName('fixedSchdDiv','screen-only');
        setClassName('tempSchdDiv','');
        unCheckThis('allDaysCheck');
        unCheckThis('staffCheckAll');
        for (var j = 1; j <= 7; j++) {
            unCheckThis('dayID'+j);
            setBgColor('dayIDtd'+j,'');
        }
        var x = getValue('staffCnt');
        for (var i = 0; i < x; i++) {
            unCheckThis('staffCheckBox'+i);
            setBgColor('staffCheckBoxTd'+i,'');
            for (var j = 1; j <= 7; j++) {
                setStyleOutline('staffWorkIN'+i+'day'+j,'');
                setStyleOutline('staffWorkOFF'+i+'day'+j,'');
                setInnerHTML('staffWorkIN'+i+'day'+j,sessionArray['staffWorkIN'+i+'day'+j]);
                setInnerHTML('staffWorkOFF'+i+'day'+j,sessionArray['staffWorkOFF'+i+'day'+j]);
            }
        }
        hideOpenSchedule();
    }
};

function fixedUpdateSuccess() {
    displayTable('fxdSchdTbl')
    checkThis('fixedSchd');
    displayNone('tempSchdTbl');
    setBgColor('fixedSchdDiv','limegreen');
    setBgColor('tempSchdDiv','white');
    displayTableRow('updateFixedMsgTr');
    setClassName('fxdUpdateMsg','greenMsg');
    setClassName('tempSchdDiv','screen-only');
    setClassName('fixedSchdDiv','');

};

function fixedUpdateError() {
    displayTable('fxdSchdTbl')
    checkThis('fixedSchd');
    displayNone('tempSchdTbl');
    setBgColor('fixedSchdDiv','limegreen');
    setBgColor('tempSchdDiv','white');
    displayTableRow('updateFixedMsgTr');
    setClassName('fxdUpdateMsg','redMsg');
    setClassName('tempSchdDiv','screen-only');
    setClassName('fixedSchdDiv','');
};

function checkAllDays() {
    if (isChecked('allDaysCheck')) {
        for (var j = 1; j <= 7; j++) {
            checkThis('dayID'+j)
            setBgColor('dayIDtd'+j,'limegreen');
        }
        staffAllCheck();
    } else {
        var x = getValue('staffCnt');
        for (var i = 0; i < x; i++) {
                for (var j = 1; j <= 7; j++) {
                    unCheckThis('dayID'+j);
                    setBgColor('dayIDtd'+j,'')
                    setStyleOutline('staffWorkIN'+i+'day'+j,'');
                    setStyleOutline('staffWorkOFF'+i+'day'+j,'');
                    setInnerHTML('staffWorkIN'+i+'day'+j,sessionArray['staffWorkIN'+i+'day'+j]);
                    setInnerHTML('staffWorkOFF'+i+'day'+j,sessionArray['staffWorkOFF'+i+'day'+j]);
            }
        }
        hideOpenSchedule();
    }
};

function staffAllCheck() {
    var x = getValue('staffCnt');
    var checked = false;
    for (var i = 0; i < x; i++) {
        if (isChecked('staffCheckBox'+i)) {
            for (var j = 1; j <= 7; j++) {
                if (isChecked('dayID'+j)) {
                    if (isDisplay('fxdSelectTr','table-row')) {
                        if (isChecked('openSchdYes')) {
                            checkAllSelections('openSchdYes','OPEN');
                        } else if (isChecked('dayOffYes')) {
                            checkAllSelections('dayOffYes','DAY OFF');
                        } else if (valueLength('staffFromTime') > 0) {
                            var displayFromTime = tConvert(getValue('staffFromTime'));
                            checkAllSelections('staffFromTime',displayFromTime);
                            if (valueLength('staffToTime') > 0) {
                                var displayToTime = tConvert(getValue('staffToTime'));
                                checkAllSelections('staffToTime',displayToTime);
                            }
                        } else if (valueLength('staffToTime') > 0) {
                            var displayToTime = tConvert(getValue('staffToTime'));
                            checkAllSelections('staffToTime',displayToTime);
                            if (valueLength('staffToTime') > 0) {
                                var displayToTime = tConvert(getValue('staffToTime'));
                                checkAllSelections('staffToTime',displayToTime);
                            }
                        }
                    } else {
                        setStyleOutline('staffWorkIN'+i+'day'+j,'2px solid red');
                        setStyleOutline('staffWorkOFF'+i+'day'+j,'2px solid red');
                    }
                    checked = true;
                }
            }
        }
    }
    if (checked == true) {
        showOpenSchedule();
    } else {
        hideOpenSchedule();
    }
};

function daysCheckBox(d) {
    if (isChecked('dayID'+d)) {
        setBgColor('dayIDtd'+d,'limegreen');
        staffCheck(d);
        if (isDisplay('fxdSelectTr','table-row')) {
            if (isChecked('openSchdYes')) {
                checkAllSelections('openSchdYes','OPEN');
            } else if (isChecked('dayOffYes')) {
                checkAllSelections('dayOffYes','DAY OFF');
            } else if (valueLength('staffFromTime') > 0) {
                var displayFromTime = tConvert(getValue('staffFromTime'));
                checkAllSelections('staffFromTime',displayFromTime);
                if (valueLength('staffToTime') > 0) {
                    var displayToTime = tConvert(getValue('staffToTime'));
                    checkAllSelections('staffToTime',displayToTime);
                }
            } else if (valueLength('staffToTime') > 0) {
                var displayToTime = tConvert(getValue('staffToTime'));
                checkAllSelections('staffToTime',displayToTime);
                if (valueLength('staffToTime') > 0) {
                    var displayToTime = tConvert(getValue('staffToTime'));
                    checkAllSelections('staffToTime',displayToTime);
                }
            }
        }
        checkDaysCheck();
    } else {
        if (isChecked('allDaysCheck')) {
            unCheckThis('allDaysCheck');
        }
        setBgColor('dayIDtd'+d,'');
        var staffSelection = false;
        var dayselection = false;
        var x = getValue('staffCnt');
        for (var i = 0; i < x; i++) {
            if (isChecked('staffCheckBox'+i)) {
                staffSelection = true;
                setStyleOutline('staffWorkIN'+i+'day'+d,'');
                setStyleOutline('staffWorkOFF'+i+'day'+d,'');
                setInnerHTML('staffWorkIN'+i+'day'+d,sessionArray['staffWorkIN'+i+'day'+d]);
                setInnerHTML('staffWorkOFF'+i+'day'+d,sessionArray['staffWorkOFF'+i+'day'+d]);
            }
            for (var j = 1; j <= 7; j++) {
                if (isChecked('dayID'+j)) {
                    dayselection = true;
                }
            }
        }
        if (staffSelection == false || dayselection == false) {
            hideOpenSchedule();
        }
    }
};

function checkDaysCheck() {
    var checkDays = true;
    for (var j = 1; j <= 7; j++) {
        if (!isChecked('dayID'+j)) {
            checkDays = false;
        }
    }
    if (checkDays == true) {
        checkThis('allDaysCheck');
    }
};

function checkStaffCheck() {
    var checkStaff = true;
    var x = getValue('staffCnt');
    for (var i = 0; i < x; i++) {
        if (!isChecked('staffCheckBox'+i)) {
            checkStaff = false;
        }
    }

    if (checkStaff == true) {
        checkThis('staffCheckAll');
    }
};

function staffCheck(d) {
    var x = getValue('staffCnt');
    var checked = false;
    for (var i = 0; i < x; i++) {
        if (isChecked('staffCheckBox'+i)) {
            setStyleOutline('staffWorkIN'+i+'day'+d,'2px solid red');
            setStyleOutline('staffWorkOFF'+i+'day'+d,'2px solid red');
            checked = true;
        }
    }
    if (checked == true) {
        showOpenSchedule();
    } else {
        hideOpenSchedule();
    }
};

function checkAllStaff() {
    var x = getValue('staffCnt');
    if (isChecked('staffCheckAll')) {
        for (var i = 0; i < x; i++) {
            checkThis('staffCheckBox'+i);
            setBgColor('staffCheckBoxTd'+i,'limegreen');
        }
        staffAllCheck();
    } else {
        for (var i = 0; i < x; i++) {
            unCheckThis('staffCheckBox'+i);
            setBgColor('staffCheckBoxTd'+i,'');
            for (var j = 1; j <= 7; j++) {
                if (isChecked('dayID'+j)) {
                    setStyleOutline('staffWorkIN'+i+'day'+j,'');
                    setStyleOutline('staffWorkOFF'+i+'day'+j,'');
                }
            }
        }
        hideOpenSchedule();
    }
};

function staffCheckBox(s) {
    if (isChecked('staffCheckBox'+s)) {
        setBgColor('staffCheckBoxTd'+s,'limegreen');
        daysCheck(s);
        if (isDisplay('fxdSelectTr','table-row')) {
            // open schedule
            if (isChecked('openSchdYes')) {
                checkAllSelections('openSchdYes','OPEN');
            } else if (isChecked('dayOffYes')) {
                checkAllSelections('dayOffYes','DAY OFF');
            } else if (valueLength('staffFromTime') > 0) {
                var displayFromTime = tConvert(getValue('staffFromTime'));
                checkAllSelections('staffFromTime',displayFromTime);
                if (valueLength('staffToTime') > 0) {
                    var displayToTime = tConvert(getValue('staffToTime'));
                    checkAllSelections('staffToTime',displayToTime);
                }
            } else if (valueLength('staffToTime') > 0) {
                var displayToTime = tConvert(getValue('staffToTime'));
                checkAllSelections('staffToTime',displayToTime);
                if (valueLength('staffToTime') > 0) {
                    var displayToTime = tConvert(getValue('staffToTime'));
                    checkAllSelections('staffToTime',displayToTime);
                }
            }
        }
        checkStaffCheck();
    } else {
        setBgColor('staffCheckBoxTd'+s,'');
        staffUnCheck();
    }
};

function daysCheck(s) {
    var checked = false;
    for (var j = 1; j <= 7; j++) {
        if (isChecked('dayID'+j)) {
            setStyleOutline('staffWorkIN'+s+'day'+j,'2px solid red');
            setStyleOutline('staffWorkOFF'+s+'day'+j,'2px solid red');
            checked = true;
        }
    }
    if (checked == true) {
        showOpenSchedule();
    } else {
        hideOpenSchedule();
    }
};

function staffUnCheck() {
    var staffSelection = false;
    var dayselection = false;
    var x = getValue('staffCnt');
    for (var i = 0; i < x; i++) {
        if (isChecked('staffCheckBox'+i)) {
            staffSelection = true;
        } else {
            if (isChecked('staffCheckAll')) {
                unCheckThis('staffCheckAll');
            }
            for (var j = 1; j <= 7; j++) {
                if (isChecked('dayID'+j)) {
                    dayselection = true;
                    setStyleOutline('staffWorkIN'+i+'day'+j,'');
                    setStyleOutline('staffWorkOFF'+i+'day'+j,'');
                    setInnerHTML('staffWorkIN'+i+'day'+j,sessionArray['staffWorkIN'+i+'day'+j]);
                    setInnerHTML('staffWorkOFF'+i+'day'+j,sessionArray['staffWorkOFF'+i+'day'+j]);
                }
            }
        }
    }

    if (staffSelection == false || dayselection == false) {
        hideOpenSchedule();
    }
};

function openSchedule() {
    if (isChecked('openSchdYes')) {
        setBgColor('openSchdYesDiv','limegreen');
        setBgColor('openSchdNoDiv','white');
        displayInline('fxdSubmitDiv');
        displayNone('dayOffDiv');
        disableThis('dayOffNo');
        disableThis('dayOffYes');
        displayNone('timeDiv');
        disableThis('staffFromTime');
        disableThis('staffToTime');
        setValue('staffFromTime',null);
        setValue('staffToTime',null);
        checkAllSelections('openSchdYes','OPEN');
    } else {
        setBgColor('openSchdNoDiv','limegreen');
        setBgColor('openSchdYesDiv','white');
        displayNone('fxdSubmitDiv');
        displayInline('dayOffDiv');
        enableThis('dayOffNo');
        enableThis('dayOffYes');
        unCheckThis('dayOffYes');
        unCheckThis('dayOffNo');
        setBgColor('dayOffNoDiv','orange');
        setBgColor('dayOffYesDiv','orange');
        setStyleOutline('dayOffYesDiv','1px solid black');
        setStyleOutline('dayOffNoDiv','1px solid black');
        checkAllSelections('openSchdNo','');
    }
};

function showOpenSchedule() {
    if (isDisplay('fxdSelectTr','none')) {
        displayTableRow('fxdSelectTr');
        displayInline('fixedSelectionsDiv');
        setBgColor('openSchdYesDiv','orange');
        setBgColor('openSchdNoDiv','orange');
    }
};

function hideOpenSchedule() {
    displayNone('fxdSelectTr');
    displayNone('fixedSelectionsDiv');
    unCheckThis('openSchdYes');
    unCheckThis('openSchdNo');
    displayNone('dayOffDiv');
    disableThis('dayOffNo');
    disableThis('dayOffYes');
    unCheckThis('dayOffYes');
    unCheckThis('dayOffNo');
    setStyleOutline('dayOffNoDiv','orange');
    setStyleOutline('dayOffYesDiv','orange');
    displayNone('timeDiv');
    disableThis('staffFromTime');
    disableThis('staffToTime');
    setValue('staffFromTime',null);
    setValue('staffToTime',null);
};

function dayOffCheck() {
    if (isChecked('dayOffYes')) {
        setBgColor('dayOffYesDiv','limegreen');
        setBgColor('dayOffNoDiv','white');
        setStyleOutline('dayOffYesDiv','1px solid black');
        setStyleOutline('dayOffNoDiv','1px solid black');
        displayInline('fxdSubmitDiv');
        displayNone('timeDiv');
        disableThis('staffFromTime');
        disableThis('staffToTime');
        checkAllSelections('dayOffYes','DAY OFF');
    } else {
        setBgColor('dayOffYesDiv','white');
        setBgColor('dayOffNoDiv','limegreen');
        setStyleOutline('dayOffYesDiv','1px solid black');
        setStyleOutline('dayOffNoDiv','1px solid black');
        displayNone('fxdSubmitDiv');
        displayInline('timeDiv');
        setBgColor('staffFromTime','orange');
        setBgColor('staffToTime','orange');
        enableThis('staffFromTime');
        enableThis('staffToTime');
        checkAllSelections('dayOffNo','');
    }
};

function fromTimeChange() {
    if (valueLength('staffFromTime') > 0 && valueLength('staffToTime') > 0) {
        displayInline('fxdSubmitDiv');
    } else {
        displayNone('fxdSubmitDiv');
    }
    if (valueLength('staffFromTime') > 0) {
        setBgColor('staffFromTime','limegreen');
        var displayFromTime = tConvert(getValue('staffFromTime'));
        checkAllSelections('staffFromTime',displayFromTime);
    }
};

function toTimeChange() {
    if (valueLength('staffFromTime') > 0 || valueLength('staffToTime') > 0) {
        checkThis('dayOffNo');
    }
    if (valueLength('staffFromTime') > 0 && valueLength('staffToTime') > 0) {
        displayInline('fxdSubmitDiv');
    } else {
        displayNone('fxdSubmitDiv');
    }
    if (valueLength('staffToTime') > 0) {
        setBgColor('staffToTime','limegreen');
        var displayToTime = tConvert(getValue('staffToTime'));
        checkAllSelections('staffToTime',displayToTime);
    }
};

function checkAllSelections(s,v) {
    //unset(selectionsArray);
    if (s == 'openSchdNo' || s == 'dayOffNo') {
        selections = getSelectionsArray();
        for (var key in selections) {
        // check if the property/key is defined in the object itself, not in parent
            if (selections.hasOwnProperty(key)) {
                setStyleOutline(selections[key],'2px solid red');
                setStyleOutline(selections[key],'2px solid red');
                setInnerHTML(selections[key],sessionArray[selections[key]]);
                setInnerHTML(selections[key],sessionArray[selections[key]]);
            }
        }
    } else if (s == 'openSchdYes' || s == 'dayOffYes') {
        selections = getSelectionsArray();
        for (var key in selections) {
        // check if the property/key is defined in the object itself, not in parent
            if (selections.hasOwnProperty(key)) {
                setStyleOutline(selections[key],'2px solid limegreen');
                setStyleOutline(selections[key],'2px solid limegreen');
                setInnerHTML(selections[key],v);
                setInnerHTML(selections[key],v);
            }
        }
    } else if (s == 'staffFromTime') {
        selections = getSelectionsArray();
        for (var key in selections) {
        // check if the property/key is defined in the object itself, not in parent
            if (selections.hasOwnProperty(key)) {
                var str = selections[key];
                var res = str.substring(0, 11);
            //    alert('res '+res);
                if (res == 'staffWorkIN') {
                    setStyleOutline(selections[key],'2px solid limegreen');
                    setInnerHTML(selections[key],v);
                }
            }
        }
    } else if (s == 'staffToTime') {
        selections = getSelectionsArray();
        for (var key in selections) {
        // check if the property/key is defined in the object itself, not in parent
            if (selections.hasOwnProperty(key)) {
                var str = selections[key];
                var res = str.substring(0, 12);
                if (res == 'staffWorkOFF') {
                    setStyleOutline(selections[key],'2px solid limegreen');
                    setInnerHTML(selections[key],v);
                }
            }
        }
    }
};

function getSelectionsArray() {
    var staffCnt = getValue('staffCnt');
    // think of another way to comunicate staffCnt
    // staffCnt = staffCnt();
    var selectionsArray = {}; // dictionary
    for (var i = 0; i < staffCnt; i++) {
        if (isChecked('staffCheckBox'+i)) {
            for (var j = 1; j <= 7; j++) {
                if (isChecked('dayID'+j)) {
                    var selectedIn = 'staffWorkIN'+i+'day'+j;
                    var selectedOut = 'staffWorkOFF'+i+'day'+j;
                    selectionsArray['staffWorkIN'+i+'day'+j] = selectedIn;
                    selectionsArray['staffWorkOFF'+i+'day'+j] = selectedOut;
                }
            }
        }
    }
    return selectionsArray;
}

/**************************************************************|
//******************* Temporary SCHEDULE FUNCTIONS ************|
***************************************************************/


function tempDateSelect() {
    displayTable('tempSchdTbl','table');
    checkThis('tempSchd');
    displayNone('fxdSchdTbl');
    setBgColor('tempSchdDiv','limegreen');
    setBgColor('fixedSchdDiv','white');
    setClassName('tempSchdDiv','');
    setClassName('fixedSchdDiv','screen-only');
};

function tempUpdateSuccess() {
    displayTable('tempSchdTbl','table');
    checkThis('tempSchd');
    displayNone('fxdSchdTbl');
    setBgColor('tempSchdDiv','limegreen');
    setBgColor('fixedSchdDiv','white');
    displayTableRow('updateTempMsgTr');
    setClassName('tempUpdateMsg','greenMsg');
    setClassName('tempSchdDiv','');
    setClassName('fixedSchdDiv','screen-only');
};

function tempUpdateError() {
    displayTable('tempSchdTbl','table');
    checkThis('tempSchd');
    displayNone('fxdSchdTbl');
    setBgColor('tempSchdDiv','limegreen');
    setBgColor('fixedSchdDiv','white');
    displayTableRow('updateTempMsgTr');
    setClassName('tempUpdateMsg','redMsg');
    setClassName('tempSchdDiv','');
    setClassName('fixedSchdDiv','screen-only');
};

function displayButton(thisBtn,thisDiv) {
    displayInline(thisBtn);
    setBgColor(thisDiv,'limegreen');
};

function tempSelectAllStaff() {
    var x = getValue('staffCnt');
    if (isChecked('tempAllStaffCheck')) {
        for (var i = 0; i < x; i++) {
            checkThis('tempStaffCheckBox'+i);
            setBgColor('tempStaffCheckBoxTd'+i,'limegreen');
            setStyleOutline('tempStaffIN'+i,'2px solid red');
            setStyleOutline('tempStaffOUT'+i,'2px solid red');
            setStyleOutline('tempTDSchdReason'+i,'2px solid red');
            if (isDisplay('tempSelectionsDiv','inline')) {
                if (isChecked('tempDayOffYes') && !isDisabled('tempDayOffYes')) {
                    setInnerHTML('tempStaffIN'+i,'DAY OFF');
                    setInnerHTML('tempStaffOUT'+i,'DAY OFF');
                    setStyleOutline('tempStaffIN'+i,'2px solid limegreen');
                    setStyleOutline('tempStaffOUT'+i,'2px solid limegreen');
                }
                var tempFromTime = getValue('tempStaffFromTime');
                if (valueLength('tempStaffFromTime') > 0 && !isDisabled('tempStaffFromTime')) {
                    setInnerHTML('tempStaffIN'+i,tConvert(tempFromTime));
                    setStyleOutline('tempStaffIN'+i,'2px solid limegreen');
                    setStyleOutline('tempStaffOUT'+i,'2px solid limegreen');
                }

                var tempToTime = getValue('tempStaffToTime');
                if (valueLength('tempStaffToTime') > 0 && !isDisabled('tempStaffToTime')) {
                    setInnerHTML('tempStaffOUT'+i,tConvert(tempToTime));
                    setStyleOutline('tempStaffIN'+i,'2px solid limegreen');
                    setStyleOutline('tempStaffOUT'+i,'2px solid limegreen');
                }

                var reasonText = getValue('tempSchdReasonSelect');
                if (reasonText != '') {
                    if (reasonText == 'other') {
                        if (valueLength('tempSchdReasonInput') > 0) {
                            reasonText = getValue('tempSchdReasonInput');
                            setStyleOutline('tempTDSchdReason'+i,'2px solid limegreen');
                            setInnerHTML('tempTDSchdReason'+i,reasonText);
                        }
                    } else {
                        reasonText = getSelectedInnerHTML('tempSchdReasonSelect');
                        setStyleOutline('tempTDSchdReason'+i,'2px solid limegreen');
                        setInnerHTML('tempTDSchdReason'+i,reasonText);
                    }
                }
            } else {
                displayInline('tempSelectionsDiv');
                setBgColor('tempDayOffNoDiv','orange');
                setBgColor('tempDayOffYesDiv','orange');
            }
        }
    } else {
        for (var i = 0; i < x; i++) {
            unCheckThis('tempStaffCheckBox'+i);
            setBgColor('tempStaffCheckBoxTd'+i,'');
            setStyleOutline('tempStaffIN'+i,'');
            setStyleOutline('tempStaffOUT'+i,'');
            setStyleOutline('tempTDSchdReason'+i,'');
            setInnerHTML('tempStaffIN'+i,tempSessionArray['tempStaffIN'+i]);
            setInnerHTML('tempStaffOUT'+i,tempSessionArray['tempStaffOUT'+i]);
            setInnerHTML('tempTDSchdReason'+i,tempSessionArray['tempTDSchdReason'+i]);
        }
        fillTempSchedule('clear');
        tempHideSelectionsDiv();
    }
};

function fillTempSchedule(selection) {
    if (selection == 'dayOffYes') {
        var x = getValue('staffCnt');
        for (var i = 0; i < x; i++) {
            if (isChecked('tempStaffCheckBox'+i)) {
                setInnerHTML('tempStaffIN'+i,'DAY OFF');
                setInnerHTML('tempStaffOUT'+i,'DAY OFF');
                setStyleOutline('tempStaffIN'+i,'2px solid limegreen');
                setStyleOutline('tempStaffOUT'+i,'2px solid limegreen');
            }
        }
    } else if (selection == 'dayOffNo') {
        var x = getValue('staffCnt');
        for (var i = 0; i < x; i++) {
            if (isChecked('tempStaffCheckBox'+i)) {
                setInnerHTML('tempStaffIN'+i,'');
                setInnerHTML('tempStaffOUT'+i,'');
                setStyleOutline('tempStaffIN'+i,'2px solid red');
                setStyleOutline('tempStaffOUT'+i,'2px solid red');
            }
        }
    } else if (selection == 'fromTimeTotime') {
        var x = getValue('staffCnt');
        var fromTime = getValue('tempStaffFromTime');
        var toTime = getValue('tempStaffToTime');
        for (var i = 0; i < x; i++) {
                if (isChecked('tempStaffCheckBox'+i)) {
                    setInnerHTML('tempStaffIN'+i,tConvert(fromTime));
                    setInnerHTML('tempStaffOUT'+i,tConvert(toTime));
                }
        }
    } else if (selection == 'fromTime') {
        var x = getValue('staffCnt');
        var fromTime = getValue('tempStaffFromTime');
        for (var i = 0; i < x; i++) {
                if (isChecked('tempStaffCheckBox'+i)) {
                    setInnerHTML('tempStaffIN'+i,tConvert(fromTime));
                    setStyleOutline('tempStaffIN'+i,'2px solid limegreen');
                }
        }
    } else if (selection == 'toTime') {
        var x = getValue('staffCnt');
        var toTime = getValue('tempStaffToTime');
        for (var i = 0; i < x; i++) {
            if (isChecked('tempStaffCheckBox'+i)) {
                setInnerHTML('tempStaffOUT'+i,tConvert(toTime));
                setStyleOutline('tempStaffOUT'+i,'2px solid limegreen');
            }
        }
    } else if (selection == 'clear') {
        var x = getValue('staffCnt');
        for (var i = 0; i < x; i++) {
                if (!isChecked('tempStaffCheckBox'+i)) {
                    setStyleOutline('tempStaffIN'+i,'');
                    setStyleOutline('tempStaffOUT'+i,'');
                    setStyleOutline('tempTDSchdReason'+i,'');
                    setInnerHTML('tempStaffIN'+i,tempSessionArray['tempStaffIN'+i] );
                    setInnerHTML('tempStaffOUT'+i,tempSessionArray['tempStaffOUT'+i]);
                    setInnerHTML('tempTDSchdReason'+i,tempSessionArray['tempTDSchdReason'+i]);
                }
        }
    } else if (selection == 'Select') {
        var x = getValue('staffCnt');
        for (var i = 0; i < x; i++) {
            if (isChecked('tempStaffCheckBox'+i)) {
                setInnerHTML('tempTDSchdReason'+i,tempSessionArray['tempTDSchdReason'+i]);
                setStyleOutline('tempTDSchdReason'+i,'2px solid red');
            }
        }
    }  else if (selection == 'other') {
        var x = getValue('staffCnt');
        reasonText = getValue('tempSchdReasonInput');
        if (valueLength('tempSchdReasonInput') > 0) {
            for (var i = 0; i < x; i++) {
                if (isChecked('tempStaffCheckBox'+i)) {
                    setInnerHTML('tempTDSchdReason'+i,reasonText);
                    setStyleOutline('tempTDSchdReason'+i,'2px solid limegreen');
                }
            }
        } else {
            for (var i = 0; i < x; i++) {
                if (isChecked('tempStaffCheckBox'+i)) {
                    setInnerHTML('tempTDSchdReason'+i,tempSessionArray['tempTDSchdReason'+i]);
                    setStyleOutline('tempTDSchdReason'+i,'2px solid red');
                }
            }
        }
    } else if (selection == 'getSelection') {
        reasonText = getSelectedInnerHTML('tempSchdReasonSelect');
        var x = getValue('staffCnt');
        for (var i = 0; i < x; i++) {
            if (isChecked('tempStaffCheckBox'+i)) {
                setInnerHTML('tempTDSchdReason'+i,reasonText);
                setStyleOutline('tempTDSchdReason'+i,'2px solid limegreen');
            }
        }
    }

/*
    else {
    //    reasonText = getSelectedInnerHTML('tempSchdReasonSelect');
        reasonText = getInnerHTML('tempSchdReasonInput');
        alert('reasonText '+reasonText);
        var x = getValue('staffCnt');
        for (var i = 0; i < x; i++) {
            if (isChecked('tempStaffCheckBox'+i)) {
                setInnerHTML('tempTDSchdReason'+i,reasonText);
                setStyleOutline('tempTDSchdReason'+i,'2px solid limegreen');
            }
        }
    }

    */
};

function tempHideSelectionsDiv() {
    setValue('tempSchdReasonSelect','');
    setBgColor('tempSchdReasonSelectDiv','orange');
    displayNone('tempSelectionsDiv');
    displayNone('tempDayOffDiv');
    unCheckThis('tempDayOffYes');
    unCheckThis('tempDayOffNo');
    setBgColor('tempDayOffNoDiv','orange');
    setBgColor('tempDayOffYesDiv','orange');
    displayNone('tempTimeDiv');
    disableThis('tempStaffFromTime');
    disableThis('tempStaffToTime');
    setValue('tempStaffFromTime','');
    setValue('tempStaffToTime','');
//    setClassName('tempDate','redMsg');
    displayNone('tempSubmitDiv');
    disableThis('tempSchdSubmit');
//    fillTempSchedule('clear');
};

function tempStaffCheckBox(s) {
    if (isChecked('tempStaffCheckBox'+s)) {
        setBgColor('tempStaffCheckBoxTd'+s,'limegreen');
        setStyleOutline('tempStaffIN'+s,'2px solid red');
        setStyleOutline('tempStaffOUT'+s,'2px solid red');
        setStyleOutline('tempTDSchdReason'+s,'2px solid red');

        if (isDisplay('tempSelectionsDiv','inline')) {
            if (isChecked('tempDayOffYes') && !isDisabled('tempDayOffYes')) {
                setInnerHTML('tempStaffIN'+s,'DAY OFF');
                setInnerHTML('tempStaffOUT'+s,'DAY OFF');
                setStyleOutline('tempStaffIN'+s,'2px solid limegreen');
                setStyleOutline('tempStaffOUT'+s,'2px solid limegreen');
            }

            var tempFromTimeValue = getValue('tempStaffFromTime');

            if (valueLength('tempStaffFromTime') > 0 && !isDisabled('tempStaffFromTime')) {
                var displayFromTime = tConvert(tempFromTimeValue);
                setStyleOutline('tempStaffOUT'+s,'2px solid limegreen');
                setStyleOutline('tempStaffOUT'+s,'2px solid limegreen');
                setInnerHTML('tempStaffIN'+s,displayFromTime);
            }

            var tempToTimeValue = getValue('tempStaffToTime');

            if (valueLength('tempStaffToTime') > 0 && !isDisabled('tempStaffToTime')) {
                var displayToTime = tConvert(tempToTimeValue);
                setStyleOutline('tempStaffIN'+s,'2px solid limegreen');
                setStyleOutline('tempStaffOUT'+s,'2px solid limegreen');
                setInnerHTML('tempStaffOUT'+s,displayToTime);
            }

            var reasonText = getValue('tempSchdReasonSelect');
            if (reasonText != '') {
                if (reasonText == 'other') {
                    if (valueLength('tempSchdReasonInput') > 0) {
                        reasonText = getValue('tempSchdReasonInput');
                        setStyleOutline('tempTDSchdReason'+s,'2px solid limegreen');
                        setInnerHTML('tempTDSchdReason'+s,reasonText);
                    }
                } else {
                    reasonText = getSelectedInnerHTML('tempSchdReasonSelect');
                    setStyleOutline('tempTDSchdReason'+s,'2px solid limegreen');
                    setInnerHTML('tempTDSchdReason'+s,reasonText);
                }
            }

        } else {
            displayInline('tempSelectionsDiv');
            setBgColor('tempDayOffNoDiv','orange');
            setBgColor('tempDayOffYesDiv','orange');
        }
        var x = getValue('staffCnt');
        for (var i = 0; i < x; i++) {
            var checked = isChecked('tempStaffCheckBox'+i);
            if (!isChecked('tempStaffCheckBox'+i)) {
                break;
            }

            if (i == x-1) {
                checkThis('tempAllStaffCheck');
            }
        }
    } else {
        setBgColor('tempStaffCheckBoxTd'+s,'');
        setStyleOutline('tempStaffIN'+s,'');
        setStyleOutline('tempStaffOUT'+s,'');
        setStyleOutline('tempTDSchdReason'+s,'');
        setInnerHTML('tempStaffIN'+s,tempSessionArray['tempStaffIN'+s] );
        setInnerHTML('tempStaffOUT'+s,tempSessionArray['tempStaffOUT'+s]);
        setInnerHTML('tempTDSchdReason'+s,tempSessionArray['tempTDSchdReason'+s]);
        unCheckThis('tempAllStaffCheck');
        var hideSelectionsDiv = true;
        var x = getValue('staffCnt');
        for (var i = 0; i < x; i++) {
            if (isChecked('tempStaffCheckBox'+i)) {
                hideSelectionsDiv = false;
            }
        }

        if (hideSelectionsDiv) {
            tempHideSelectionsDiv();
        }
    }
};

function tempDayOffCheck() {
    if (isChecked('tempDayOffYes')) {
        setBgColor('tempDayOffYesDiv','limegreen');
        setBgColor('tempDayOffNoDiv','white');
        displayInline('tempSubmitDiv');
        enableThis('tempSchdSubmit');
        displayNone('tempTimeDiv');
        disableThis('tempStaffFromTime');
        disableThis('tempStaffToTime');
        setValue('tempStaffFromTime','');
        setValue('tempStaffToTime','');
    //    setClassName('tempDate','greenMsg');
        fillTempSchedule('dayOffYes');
    } else {
        setBgColor('tempDayOffYesDiv','white');
        setBgColor('tempDayOffNoDiv','limegreen');
        displayNone('tempSubmitDiv');
        disableThis('tempSchdSubmit');
        displayInline('tempTimeDiv');
        setBgColor('tempStaffFromTime','orange');
        enableThis('tempStaffFromTime');
        setBgColor('tempStaffToTime','orange');
        enableThis('tempStaffToTime');
    //    setClassName('tempDate','redMsg');
        fillTempSchedule('dayOffNo');
    }
};

function tempFromTimeChange() {
    var fromTime = valueLength('tempStaffFromTime');
    var toTime = valueLength('tempStaffToTime');
    if (fromTime > 0 && toTime > 0) {
        displayInline('tempSubmitDiv');
        enableThis('tempSchdSubmit');
    //    setClassName('tempDate','greenMsg');
    } else {
        displayNone('tempSubmitDiv');
        disableThis('tempSchdSubmit');
    //    setClassName('tempDate','redMsg');
    }
    if (fromTime > 0) {
        setBgColor('tempStaffFromTime','limegreen');
        fillTempSchedule('fromTime');
    }
};

function tempToTimeChange() {
    var fromTime = valueLength('tempStaffFromTime');
    var toTime = valueLength('tempStaffToTime');
    if (fromTime > 0 || toTime > 0) {
        checkThis('tempDayOffNo');
    }
    if (fromTime > 0 && toTime > 0) {
        displayInline('tempSubmitDiv');
        enableThis('tempSchdSubmit');
    //    setClassName('tempDate','greenMsg');
    } else {
        displayNone('tempSubmitDiv');
        disableThis('tempSchdSubmit');
    //    setClassName('tempDate','redMsg');
    }
    if (toTime > 0) {
        setBgColor('tempStaffToTime','limegreen');
        fillTempSchedule('toTime');
    }
};

function tempSchdReasonSelection() {
    var reason = getValue('tempSchdReasonSelect');
    if (reason == '') {
        setBgColor('tempSchdReasonSelectDiv','orange');
    //    setBgColor('tempSchdSubmitBtnDiv','orange');
        displayNone('tempSchdReasonSpecifyDiv');
        disableThis('tempSchdReasonInput');
        requiredFalse('tempSchdReasonInput');
        displayNone('tempDayOffDiv');
        displayNone('tempTimeDiv');
        displayNone('tempSubmitDiv');
        disableThis('tempSchdSubmit');
        disableThis('tempDayOffNo');
        unCheckThis('tempDayOffNo');
        setBgColor('tempDayOffNoDiv','orange');
        disableThis('tempDayOffYes');
        unCheckThis('tempDayOffYes');
        setBgColor('tempDayOffYesDiv','orange');
        fillTempSchedule('Select');
    } else if (reason == 'other') {
        setBgColor('tempSchdReasonSelectDiv','limegreen');
    //    setBgColor('tempSchdSubmitBtnDiv','orange');
        setBgColor('tempSchdReasonSpecifyDiv','orange');
        displayInline('tempSchdReasonSpecifyDiv');
        enableThis('tempSchdReasonInput');
        requiredTrue('tempSchdReasonInput');
        if (valueLength('tempSchdReasonInput') == 0) {
            disableThis('tempDayOffNo');
            unCheckThis('tempDayOffNo');
            setBgColor('tempDayOffNoDiv','orange');
            disableThis('tempDayOffYes');
            unCheckThis('tempDayOffYes');
            setBgColor('tempDayOffYesDiv','orange');
            displayNone('tempDayOffDiv');
            displayNone('tempSubmitDiv');
            disableThis('tempSchdSubmit');
            displayNone('tempTimeDiv');
            setValue('tempStaffFromTime','');
            setValue('tempStaffToTime','');
            setBgColor('tempStaffFromTime','orange');
            setBgColor('tempStaffToTime','orange');
            fillTempSchedule('other');
        } else {
            setBgColor('tempSchdReasonSpecifyDiv','limegreen');
        //    setBgColor('tempSchdSubmitBtnDiv','limegreen');
            displayInline('tempDayOffDiv');
            enableThis('tempDayOffNo');
            enableThis('tempDayOffYes');
        //    tempReason = getInnerHTML('tempSchdReasonInput');
            fillTempSchedule('other');
        }
    } else {
        displayNone('tempSchdReasonSpecifyDiv');
        disableThis('tempSchdReasonInput');
        setValue('tempSchdReasonInput','');
        requiredFalse('tempSchdReasonInput');
        setBgColor('tempSchdReasonSelectDiv','limegreen');
    //    setBgColor('tempSchdSubmitBtnDiv','limegreen');
        displayInline('tempDayOffDiv');
        enableThis('tempDayOffNo');
        enableThis('tempDayOffYes');
        fillTempSchedule('getSelection');
    }
};

function dateInputBgColor(thisID) {
    var dateInput = getValue(thisID);
//    var fromDate = getValue('fromDateInput');
    var tryDate = new Date(dateInput);
    if (tryDate == 'Invalid Date') {
        setBgColor(thisID,'orange');
    }

    if (tryDate !== undefined) {
        tryDate = tryDate.toString();
        var firstDigit = tryDate.slice(10,12);
        if (firstDigit > 0) {
            setBgColor(thisID,'limegreen');
        }
    }
};

function selectInputBgColor(divID,thisID,thisRow,submitRow) {
    var selectedInput = getValue(thisID);
    if (selectedInput == '') {
        setBgColor(divID,'orange');
        displayNone(thisRow);
        displayNone(submitRow);
    } else {
        setBgColor(divID,'limegreen');
        displayTableRow(thisRow);
        displayTableRow(submitRow);
    }
};

function dateInputSubmit(thisID,thisSubmitBtn) {
    var dateInput = getValue(thisID);
    var tryDate = new Date(dateInput);
    if (tryDate == 'Invalid Date') {
        setBgColor(thisID,'orange');
    }

    if (tryDate !== undefined) {
        tryDate = tryDate.toString();
        var firstDigit = tryDate.slice(11,12);
        if (firstDigit > 0) {
            setBgColor(thisID,'limegreen');
            displayInline(thisSubmitBtn);
            enableThis(thisSubmitBtn);
        } else {
            setBgColor(thisID,'orange');
            displayNone(thisSubmitBtn);
            disableThis(thisSubmitBtn);
        }
    }
};
