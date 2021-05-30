$(function () {
  $("#searchBtn").on("click", function (e) {
    e.preventDefault();
    search($("#bookingSearch"));
  });
});
var xhr = new XMLHttpRequest();
var url = "adminprocess.php";

function assignTaxi(ref) {
  console.log("hey! " + $(ref).attr("id"));
  var params = "ref=" + $(ref).attr("id") + "&action=assign";
  xhr.open("POST", url);
  xhr.responseType = "json";
  xhr.onload = function () {
    if (this.status == 200) {
    } else {
      console.log("bad request");
    }
  };

  xhr.send(params);
}

function search(val) {
  xhr.open("GET", url + "?" + val.attr("name") + "=" + val.val());
  xhr.responseType = "text";
  xhr.onload = function () {
    if (this.status == 200) {
      $("#rows").html(this.response);
    } else {
      console.log("bad request");
    }
  };
  xhr.send();
}
