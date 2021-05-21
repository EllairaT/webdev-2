//do this on page load
$(function () {
  var date = new Date(),
    h = date.getHours(),
    m = date.getMinutes();

  //set current date
  //max date is 2 months from the current date.
  //this is so that no one can book a year in advance.
  $("#dateID").datepicker({
    dateFormat: "dd/mm/yy",
    minDate: date,
    maxDate: "+2m",
  });
  $("#dateID").datepicker("setDate", date);

  $("#timeID").timepicker({
    timeFormat: "HH:mm",
    interval: 15,
    maxTime: date,
    defaultTime: "now",
    dynamic: true,
    dropdown: true,
    scrollbar: false,
    change: function () {
      validateTime();
    },
  });

  //update time to NOW when now btn is pressed
  $("#btnNowID").on("click", function (e) {
    e.preventDefault();
    $("#timeID").timepicker("setTime", new Date());
  });

  // suggest suburb name
  $("#sb-nameID").on("keyup", function () {
    getSuburbData($("#sb-nameID"));
  });

  // suggest destination suburb
  $("#ds-nameID").on("keyup", function () {
    getSuburbData($("#ds-nameID"));
  });

  //validate input once user focuses out of input
  $("#sb-nameID").on("focusout", function () {
    validateSuburb($("#sb-nameID"));
  });

  $("#ds-nameID").on("focusout", function () {
    validateSuburb($("#ds-nameID"));
  });

  $("#phoneID").on("change", function () {
    validatePhone($("#phoneID"));
  });

  //validate form before submitting
  $("#submitID").on("click", function (e) {
    e.preventDefault();

    if (validateForm()) {
      console.log("submitted!");
    }
  });
});

var xhr = new XMLHttpRequest();
var url = "bookingprocess.php";

//return all suburb names
function getSuburbData(val) {
  xhr.open("GET", url + "?" + val.attr("name") + "=" + val.val());
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
        // if field is empty, add a false flag to array
        if (!$(this).val()) {
          $(this).addClass("is-invalid");
          flags.push(false);
        } else {
          $(this).removeClass("is-invalid");
          flags.push(true);
        }
      }
    });

  if (validateTime() && validatePhone()) {
    flags.push(true);
  } else {
    flags.push(false);
  }
  if (flags.includes(false)) {
    return false;
  } else {
    return true;
  }
}

// send entire form as json to server
function sendFormData() {
  var dataToSend = $("#booking-form").serialize();

  xhr.open("POST", url);
  xhr.onreadystatechange = function () {};
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

function validateTime() {
  var date = new Date();
  var selectedTime = $("#timeID").val();
  var currentTime = date.toLocaleTimeString([], { hour12: false }).slice(0, -3);
  var regexp = new RegExp("^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$");
  if (regexp.test(selectedTime)) {
    if (selectedTime >= currentTime) {
      $("#timeID").addClass("is-valid");
      $("#timeID").removeClass("is-invalid");
      return true;
    } else {
      $("#timeID").addClass("is-invalid");
      $("#timeID").removeClass("is-valid");
    }
  } else {
    $("#timeID").addClass("is-invalid");
    $("#timeID").removeClass("is-valid");
  }
  return false;
}

function validatePhone() {
  var val = $("#phoneID");
  var regexp = new RegExp("^(+0?1s)?(?d{3})?[s.-]d{3}[s.-]d{4}$");
  if (regexp.test(val.val())) {
    showValidation(val, true);
    return true;
  } else {
    showValidation(val, false);
    return false;
  }
}
