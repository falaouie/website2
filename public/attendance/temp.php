<?php

?>

<label for="newCompany">Company Name</label>
<input id="newCompany" list="newCompanyName" name="newCompanyName" maxlength="40" size="30" value="<?= $newCompanyName ?>" placeholder="Official Customer Name" autocomplete="off" autofocus="true">
<datalist id="newCompanyName">
  <?php
    if ($customersListCount > 0) {
      for ($i=0; $i < $customersListCount; $i++) {
      ?>
      <option value="<?php echo($customersList[$i]['companyName']) ?>"><?php echo($customersList[$i]['companyName']) ?></option>
      <?php
      }
    }
    ?>
</datalist>


<div class="div-group">
    <div class="div-item">
        Staff Status
    </div>
    <div id="activeStatusDiv" class="schedule-option" style="background-color: <?php echo (isset($_SESSION['staffStatus']) && $_SESSION['staffStatus'] == '1') ? 'limegreen' : 'white'; ?>">
        <strong><label for="activeStatus">Active</label></strong>
        <input class="screen-only" id="activeStatus" type="radio" name="staffStatus" value="1" 
        <?php echo (isset($_SESSION['staffStatus']) && $_SESSION['staffStatus'] == '1') ? 'checked' : ''; ?>
        onchange="this.form.submit();">
    </div>
    <div id="inactiveStatusDiv" class="schedule-option" style="background-color: <?php echo isset($_SESSION['staffStatus']) && $_SESSION['staffStatus'] == '0' ? 'limegreen' : 'white'; ?>">
        <strong><label for="inactiveStatus">In-Active</label></strong>
        <input class="screen-only" id="inactiveStatus" type="radio" name="staffStatus" value="0" 
        <?php echo isset($_SESSION['staffStatus']) && $_SESSION['staffStatus'] == '0' ? 'checked' : ''; ?>
        onchange="this.form.submit();">
    </div>
    <div id="allStatusDiv" class="schedule-option" style="background-color: <?php echo isset($_SESSION['staffStatus']) && $_SESSION['staffStatus'] == 'allStaff' ? 'limegreen' : 'white'; ?>">
        <strong><label for="allStatus">All</label></strong>
        <input class="screen-only" id="allStatus" type="radio" name="staffStatus" value="allStaff" 
        <?php echo isset($_SESSION['staffStatus']) && $_SESSION['staffStatus'] == 'allStaff' ? 'checked' : ''; ?>
        onchange="this.form.submit();">
    </div>
</div>

<?php


array(11) { [0]=> array(8)  {   
        ["id"]=> int(77) 
        ["staff_id"]=> int(1024) 
        ["work_date"]=> string(10) "2023-04-01" 
        ["work_in"]=> string(8) "09:05:14" 
        ["work_off"]=> string(8) "15:13:32" 
        ["hours_worked"]=> string(4) "6.14" 
        ["first_name"]=> string(3) "Ali" 
        ["last_name"]=> string(4) "Rizk" 
    } 
        
        [1]=> array(8) {
             ["id"]=> int(78) 
             ["staff_id"]=> int(1021) 
             ["work_date"]=> string(10) "2023-04-01" 
             ["work_in"]=> string(8) "09:05:17" 
             ["work_off"]=> string(8) "15:13:33" 
             ["hours_worked"]=> string(4) "6.14" 
             ["first_name"]=> string(4) "Riwa" 
             ["last_name"]=> string(5) "Saade"  
        } [2]=> array(8) {
             ["id"]=> int(79) 
             ["staff_id"]=> int(1021) 
             ["work_date"]=> string(10) "2023-04-03" 
             ["work_in"]=> string(8) "08:41:41" 
             ["work_off"]=> string(8) "16:33:07" 
             ["hours_worked"]=> string(4) "7.86"  
             ["first_name"]=> string(4) "Riwa" 
             ["last_name"]=> string(5) "Saade" 
        } [3]=> array(19) { 
            ["id"]=> int(80) ["staff_id"]=> int(1024) ["work_date"]=> string(10) "2023-04-03" ["work_in"]=> string(8) "08:49:25" ["work_off"]=> string(8) "16:33:48" ["hours_worked"]=> string(4) "7.74" ["expected_work_in"]=> NULL ["expected_work_off"]=> NULL ["expected_hours"]=> NULL ["status"]=> int(0) ["title_id"]=> int(4) ["first_name"]=> string(3) "Ali" ["last_name"]=> string(4) "Rizk" ["attendance_req"]=> int(1) ["joining_date"]=> string(10) "2022-11-16" ["termination_date"]=> NULL ["phone_number"]=> string(8) "76819145" ["email_address"]=> NULL ["system_access"]=> int(1) } [4]=> array(19) { ["id"]=> int(81) ["staff_id"]=> int(1006) ["work_date"]=> string(10) "2023-04-03" ["work_in"]=> string(8) "08:55:07" ["work_off"]=> string(8) "15:11:06" ["hours_worked"]=> string(4) "6.27" ["expected_work_in"]=> NULL ["expected_work_off"]=> NULL ["expected_hours"]=> NULL ["status"]=> int(1) ["title_id"]=> int(4) ["first_name"]=> string(5) "Hanan" ["last_name"]=> string(5) "Ajami" ["attendance_req"]=> int(1) ["joining_date"]=> string(10) "2015-07-01" ["termination_date"]=> string(10) "2024-01-15" ["phone_number"]=> string(8) "70759302" ["email_address"]=> NULL ["system_access"]=> int(1) } [5]=> array(19) { ["id"]=> int(82) ["staff_id"]=> int(1021) ["work_date"]=> string(10) "2023-04-04" ["work_in"]=> string(8) "08:08:31" ["work_off"]=> string(8) "16:37:12" ["hours_worked"]=> string(4) "8.48" ["expected_work_in"]=> NULL ["expected_work_off"]=> NULL ["expected_hours"]=> NULL ["status"]=> int(1) ["title_id"]=> int(4) ["first_name"]=> string(4) "Riwa" ["last_name"]=> string(5) "Saade" ["attendance_req"]=> int(1) ["joining_date"]=> string(10) "2022-07-04" ["termination_date"]=> NULL ["phone_number"]=> string(8) "71543526" ["email_address"]=> string(20) "riwasaade6@gmail.com" ["system_access"]=> int(1) } [6]=> array(19) { ["id"]=> int(83) ["staff_id"]=> int(1006) ["work_date"]=> string(10) "2023-04-04" ["work_in"]=> string(8) "08:57:36" ["work_off"]=> string(8) "15:07:18" ["hours_worked"]=> string(4) "6.16" ["expected_work_in"]=> NULL ["expected_work_off"]=> NULL ["expected_hours"]=> NULL ["status"]=> int(1) ["title_id"]=> int(4) ["first_name"]=> string(5) "Hanan" ["last_name"]=> string(5) "Ajami" ["attendance_req"]=> int(1) ["joining_date"]=> string(10) "2015-07-01" ["termination_date"]=> string(10) "2024-01-15" ["phone_number"]=> string(8) "70759302" ["email_address"]=> NULL ["system_access"]=> int(1) } [7]=> array(19) { ["id"]=> int(84) ["staff_id"]=> int(1024) ["work_date"]=> string(10) "2023-04-04" ["work_in"]=> string(8) "09:06:11" ["work_off"]=> string(8) "16:37:10" ["hours_worked"]=> string(4) "7.52" ["expected_work_in"]=> NULL ["expected_work_off"]=> NULL ["expected_hours"]=> NULL ["status"]=> int(0) ["title_id"]=> int(4) ["first_name"]=> string(3) "Ali" ["last_name"]=> string(4) "Rizk" ["attendance_req"]=> int(1) ["joining_date"]=> string(10) "2022-11-16" ["termination_date"]=> NULL ["phone_number"]=> string(8) "76819145" ["email_address"]=> NULL ["system_access"]=> int(1) } [8]=> array(19) { ["id"]=> int(85) ["staff_id"]=> int(1021) ["work_date"]=> string(10) "2023-04-05" ["work_in"]=> string(8) "08:07:29" ["work_off"]=> string(8) "15:59:41" ["hours_worked"]=> string(4) "7.87" ["expected_work_in"]=> NULL ["expected_work_off"]=> NULL ["expected_hours"]=> NULL ["status"]=> int(1) ["title_id"]=> int(4) ["first_name"]=> string(4) "Riwa" ["last_name"]=> string(5) "Saade" ["attendance_req"]=> int(1) ["joining_date"]=> string(10) "2022-07-04" ["termination_date"]=> NULL ["phone_number"]=> string(8) "71543526" ["email_address"]=> string(20) "riwasaade6@gmail.com" ["system_access"]=> int(1) } [9]=> array(19) { ["id"]=> int(86) ["staff_id"]=> int(1006) ["work_date"]=> string(10) "2023-04-05" ["work_in"]=> string(8) "08:41:46" ["work_off"]=> string(8) "15:25:11" ["hours_worked"]=> string(4) "6.72" ["expected_work_in"]=> NULL ["expected_work_off"]=> NULL ["expected_hours"]=> NULL ["status"]=> int(1) ["title_id"]=> int(4) ["first_name"]=> string(5) "Hanan" ["last_name"]=> string(5) "Ajami" ["attendance_req"]=> int(1) ["joining_date"]=> string(10) "2015-07-01" ["termination_date"]=> string(10) "2024-01-15" ["phone_number"]=> string(8) "70759302" ["email_address"]=> NULL ["system_access"]=> int(1) } [10]=> array(19) { ["id"]=> int(87) ["staff_id"]=> int(1024) ["work_date"]=> string(10) "2023-04-05" ["work_in"]=> string(8) "09:04:06" ["work_off"]=> string(8) "17:20:57" ["hours_worked"]=> string(4) "8.28" ["expected_work_in"]=> NULL ["expected_work_off"]=> NULL ["expected_hours"]=> NULL ["status"]=> int(0) ["title_id"]=> int(4) ["first_name"]=> string(3) "Ali" ["last_name"]=> string(4) "Rizk" ["attendance_req"]=> int(1) ["joining_date"]=> string(10) "2022-11-16" ["termination_date"]=> NULL ["phone_number"]=> string(8) "76819145" ["email_address"]=> NULL ["system_access"]=> int(1) } 
}
