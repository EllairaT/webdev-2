<?php

session_start();
header('Content-Type: application/json');

include 'dbcon.php';
$conn = Connect();

function search()
{
    $sql = 'SELECT
    `bookings`.`BOOKING_REF`,
    booking_information.CUSTOMER_NAME,
    booking_information.CUSTOMER_PH,
    booking_information.ORIGIN_SUBURB_ID,
	booking_information.DESTINATION_SUBURB,
    booking_information.PICKUP_DATE,
    booking_information.PICKUP_TIME,
    bookings.BOOKING_STATUS
FROM
    `bookings`,
    `booking_information`
WHERE
    bookings.BOOKING_REF = booking_information.BOOKING_REF';
}
function searchTwoHours()
{
    $sql = 'SELECT
    `bookings`.`BOOKING_REF`,
    booking_information.CUSTOMER_NAME,
    booking_information.CUSTOMER_PH,
    booking_information.ORIGIN_SUBURB_ID,
	booking_information.DESTINATION_SUBURB,
    booking_information.PICKUP_DATE,
    booking_information.PICKUP_TIME,
    bookings.BOOKING_STATUS
FROM
    `bookings`,
    `booking_information`
WHERE
    booking_information.PICKUP_TIME < DATE_ADD(now(),interval 2 hour)';
}

function updateStatus()
{
}

$response = ['status' => 0, 'details' => '', 'data' => ''];

if (isset($_GET['bsearch'])) {
    $search = '%'.$_GET['bsearch'].'%';
    if ('' == $search) {
        searchTwoHours();
    } else {
        search();
    }
}
