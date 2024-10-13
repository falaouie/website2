// ||**************** General Functions **********||

function isDisplay(thisID, thisDisplay) {
  var display = document.getElementById(thisID).style.display;
  if (display == thisDisplay) {
    return true;
  }
}

function isDisabled(thisID) {
  var disabled = document.getElementById(thisID).disabled;
  return disabled;
}

function isChecked(thisID) {
  var checked = document.getElementById(thisID).checked;
  return checked;
}

function setStyleOutline(thisID, thisValue) {
  document.getElementById(thisID).style.outline = thisValue;
}

function setInnerHTML(thisID, thisValue) {
  document.getElementById(thisID).innerHTML = thisValue;
}

function getInnerHTML(thisID) {
  result = document.getElementById(thisID).innerHTML;
  return result;
}

function getSelectedInnerHTML(thisID) {
  selected = document.getElementById(thisID);
  result = selected.options[selected.selectedIndex].innerHTML;
  return result;
}

function setClassName(thisID, thisClass) {
  document.getElementById(thisID).className = thisClass;
}

function setValue(thisID, thisValue) {
  document.getElementById(thisID).value = thisValue;
}

function getValue(thisID) {
  thisValue = document.getElementById(thisID).value;
  return thisValue;
}

function valueLength(thisID) {
  var thisValueLength = document.getElementById(thisID).value.length;
  return thisValueLength;
}

function displayNone(thisID) {
  document.getElementById(thisID).style.display = 'none';
}

function displayInline(thisID) {
  document.getElementById(thisID).style.display = 'inline';
}

function displayBlock(thisID) {
  document.getElementById(thisID).style.display = 'block';
}

function displayTableRow(thisID) {
  document.getElementById(thisID).style.display = 'table-row';
}

function displayTable(thisID) {
  document.getElementById(thisID).style.display = 'table';
}

function enableThis(thisID) {
  document.getElementById(thisID).disabled = false;
}

function disableThis(thisID) {
  document.getElementById(thisID).disabled = true;
}

function checkThis(thisID) {
  document.getElementById(thisID).checked = true;
}

function unCheckThis(thisID) {
  document.getElementById(thisID).checked = false;
}

function requiredTrue(thisID) {
  document.getElementById(thisID).required = true;
}

function requiredFalse(thisID) {
  document.getElementById(thisID).required = false;
}

function setBgColor(thisID, bgColor) {
  document.getElementById(thisID).style.background = bgColor;
}

function submitThisForm(thisID) {
  document.getElementById(thisID).submit();
}

function tConvert(time) {
  // Check correct time format and split into components
  time = time.toString().match(/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [
    time,
  ];

  if (time.length > 1) {
    // If time format correct
    time = time.slice(1); // Remove full string match value
    time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
    time[0] = +time[0] % 12 || 12; // Adjust hours
  }
  return time.join(''); // return adjusted time or original string
}
