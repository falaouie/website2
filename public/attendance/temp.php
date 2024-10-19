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