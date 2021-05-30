$(function () {
  $("#bookingSearch").on("click", function () {
    search($("#bookingSearch"));
  });
});
var xhr = new XMLHttpRequest();
var url = "adminprocess.php";
function login() {}

function search(val) {
  xhr.open("GET", url + "?" + val.attr("name") + "=" + val.val());
  xhr.responseType = "json";
  xhr.onload = function () {};
  xhr.send();
}
