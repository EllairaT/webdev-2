//do this on page load
var date = moment();
$(function () {
  //set current date

  //initialise date and time ------------
  $("#timeID").val(date.format("HH:mm"));
  $("#dateID").val(date.format("D/MM/YYYY"));

  $("#timeID").bootstrapMaterialDatePicker({
    date: false,
    format: "HH:mm",
    nowButton: true,
    okText: "Next",
    minDate: date,
  });

  $("#dateID").bootstrapMaterialDatePicker({
    weekStart: 0,
    time: false,
    minDate: date,
    format: "DD/MM/YYYY",
  });

  $("#dateID").on("change", function () {
    dateWasChanged();
  });
  // -------------------------------------

  //update time to NOW when now btn is pressed
  $("#btnNowID").on("click", function (e) {
    $("#timeID").val(date.format("HH:mm"));
    e.preventDefault();
  });

  $("#btnTodayID").on("click", function (e) {
    $("#dateID").val(date.format("D/MM/YYYY"));
    dateWasChanged();
    e.preventDefault();
  });

  // suggest suburb name
  $("#sb-nameID").on("keydown", function () {
    getSuburbData($("#sb-nameID"));
  });

  // suggest destination suburb
  $("#ds-nameID").on("keydown", function () {
    getSuburbData($("#ds-nameID"));
  });

  //validate inputs once user focuses out of input
  $("#sb-nameID").on("change", function () {
    if (!($("#sb-nameID").val() == "")) {
      validateSuburb($("#sb-nameID"));
    } else {
      console.log("null. not validating.");
    }
  });

  $("#ds-nameID").on("change", function () {
    if (!($("#ds-nameID").val() == "")) {
      validateSuburb($("#ds-nameID"));
    } else {
      console.log("null. not validating.");
    }
  });

  $("#phoneID").on("change", function () {
    validatePhone();
  });

  $("#timeID").on("change", function () {
    validateTime();
  });

  $("#dateID").on("change", function () {
    validateTime();
  });

  $("#submitID").on("click", function (e) {
    //validate form before submitting
    e.preventDefault();
    if (validateForm()) {
      sendFormData();
      console.log("submitted!");
    }
  });
});

var xhr = new XMLHttpRequest();
var url = "bookingprocess.php";

//return all suburb names
function getSuburbData(val) {
  xhr.open("GET", url + "?" + val.attr("name") + "=" + val.val());
  console.log(url + "?" + val.attr("name") + "=" + val.val());
  xhr.responseType = "json";
  xhr.onload = function () {
    if (this.status == 200) {
      //set suggestions based on values returned from db
      val.autocomplete({
        source: this.response.data,
      });
    }
  };
  xhr.send();
}

//check if entered suburb is valid
function validateSuburb(val) {
  xhr.open("GET", url + "?" + "validateSub" + "=" + val.val());
  xhr.responseType = "json";
  xhr.onload = function () {
    if (this.status == 200) {
      //check response status, then show appropriate validation message
      if (this.response.status) {
        showValidation(val, true);
      } else {
        showValidation(val, false);
      }
    } else {
      //show modal for this one
      console.log("bad request");
    }
  };
  xhr.send();
}

//check for empty required fields
function validateForm() {
  var flags = [];
  $("form#booking-form") //get form
    .find("input") //find all inputs
    .each(function () {
      //get required fields

      if ($(this).prop("required")) {
        console.log("checking: " + $(this).attr("name"));
        // if field is empty, add a false flag to array
        if (!$(this).val()) {
          $(this).addClass("is-invalid");
          flags.push(false);
        } else {
          $(this).removeClass("is-invalid");
          flags.push(true);
        }
        console.log($(flags).get(-1));
      }
    });

  if (validatePhone()) {
    flags.push(true);
  } else {
    flags.push(false);
  }

  console.log(flags);
  if (flags.includes(false)) {
    return false;
  } else {
    return true;
  }
}

// send entire form as json to server
function sendFormData() {
  var dataToSend = new FormData(document.getElementById("booking-form"));
  //extra parameter to let the server know that the form has been submitted.
  dataToSend.append("submitted", true);
  xhr.open("POST", url);
  xhr.responseType = "json";
  xhr.onload = function () {
    if (this.status == 200) {
      console.log(this.response);
    } else {
      console.log("Bad request");
    }
  };

  xhr.send(dataToSend);
}

function showValidation(val, bool) {
  //if input field is not empty, validate its contents
  if (val.val()) {
    if (bool) {
      val.removeClass("is-invalid");
      val.addClass("is-valid");
    } else {
      val.removeClass("is-valid");
      val.addClass("is-invalid");
    }
  } else {
    //if empty, remove validation classes
    val.removeClass("is-valid");
    val.removeClass("is-invalid");
  }
}

//if selected date is today, validate time based on time left in the day
function validateTime() {
  var selectedTime = moment($("#timeID").val(), "HH:mm", true);
  if (selectedTime.isValid()) {
    $("#timeID").val(selectedTime.format("HH:mm"));
    showValidation($("#timeID"), true);
  } else {
    showValidation($("#timeID"), false);
  }
  return false;
}

function dateWasChanged() {
  var currentDate = moment(date, "DD/MM/YYYY", true);
  var currentTime = moment(date, "HH:mm", true);
  var selectedDate = moment($("#dateID").val(), "DD/MM/YYYY", true);
  var selectedTime = moment($("#timeID").val(), "HH:mm", true);

  var nextDay = moment(date).add(1, "days");
  console.log(nextDay);

  // reset the time picker if the selected date is after current date
  if (selectedDate.isAfter(currentDate)) {
    $("#timeID").val("00:00");
    $("#timeID").bootstrapMaterialDatePicker("setMinDate", selectedDate);
  } else {
    console.log(selectedDate, currentDate);
    $("#timeID").val(currentTime.format("HH:mm"));
    $("#timeID").bootstrapMaterialDatePicker("setMinDate", currentDate);
  }
  return true;
}

function validatePhone() {
  var val = $("#phoneID");
  var regexp = new RegExp("^[ 0-9]{10,12}$");
  if (regexp.test(val.val())) {
    showValidation(val, true);
    return true;
  } else {
    showValidation(val, false);
    return false;
  }
}
