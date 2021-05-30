<?php

session_start();
header('Content-Type: application/json');

include 'dbcon.php';
$conn = Connect();

function validateSuburb($param)
{
    global $conn;

    $search_query = $conn->prepare('SELECT `SUBURB_NAME` FROM `suburbs` WHERE  `SUBURB_NAME` = ?');
    $search_query->bind_param('s', $searchvar);

    $searchvar = $param;

    $search_query->execute();
    $search_query->store_result();

    //check if db returns a result
    if ($search_query->num_rows() > 0) {
        return true;
    }

    return false;
}

function insertInfo($arr)
{
    global $conn;

    $sql_info = 'INSERT INTO `booking_information`(
    `BOOKING_REF`,
    `CUSTOMER_NAME`,
    `CUSTOMER_PH`,
    `UNIT_NUMBER`,
    `STREET_NUMBER`,
    `STREET_NAME`,
    `ORIGIN_SUBURB_ID`,
    `DESTINATION_SUBURB`,
    `PICKUP_DATE`,
    `PICKUP_TIME`
    )
    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

    $insert_info = $conn->prepare($sql_info);
    $insert_info->bind_param('ssssssssss', $refnum, $cname, $phone, $unitnum, $stnum, $stname, $origin, $destination, $date, $time);

    $refnum = $arr['customerref'];
    $cname = $arr['cname'];
    $phone = $arr['phone'];
    $unitnum = $arr['unumber'];
    $stnum = $arr['snumber'];
    $stname = $arr['stname'];
    $origin = $arr['sbname'];
    $destination = $arr['dsname'];
    $date = $arr['date'];
    $time = $arr['time'];

    if ($insert_info->execute()) {
        $insert_info->store_result();

        return true;
    }

    return false;
}

function insertBooking($arr)
{
    global $conn;
    $sql_booking = 'INSERT INTO `bookings`(`BOOKING_REF`) VALUES(?)';
    $insert_booking = $conn->prepare($sql_booking);
    $insert_booking->bind_param('s', $refnum);

    $refnum = $arr['customerref'];

    if ($insert_booking->execute()) {
        $insert_booking->store_result();

        return true;
    }

    return false;
}

// executes all the queries
function submit($arr)
{
    if (insertInfo($arr) && insertBooking($arr)) {
        return true;
    }

    return false;
}

// creates a reference number for user
function createRef()
{
    $time = time();
    $bytes = random_bytes(8);
    $hex = bin2hex($bytes);
    $ref = strtoupper(substr($time, -4).$hex);

    return implode('-', str_split($ref, 4));
}

$response = ['status' => 0, 'details' => '', 'data' => '', 'confirmation' => ''];

$search_query = $conn->prepare('SELECT `SUBURB_NAME` FROM `suburbs` WHERE  `SUBURB_NAME` LIKE ? ');
$search_query->bind_param('s', $searchvar);

$suburb_arr = [];

$form_arr = [];

if (isset($_GET['sbname'])) {
    $searchvar = '%'.$_GET['sbname'].'%';

    $search_query->execute();

    $search_query->store_result();
    $search_query->bind_result($suburb);

    if ($search_query->num_rows() > 0) {
        while ($search_query->fetch()) {
            array_push($suburb_arr, $suburb);
        }
    }
    $response['data'] = $suburb_arr;
} elseif (isset($_GET['dsname'])) {
    $searchvar = '%'.$_GET['dsname'].'%';

    $search_query->execute();

    $search_query->store_result();
    $search_query->bind_result($suburb);

    if ($search_query->num_rows() > 0) {
        while ($search_query->fetch()) {
            array_push($suburb_arr, $suburb);
        }
    }
    $response['data'] = $suburb_arr;
} elseif (isset($_GET['validateSub'])) {
    if (validateSuburb($_GET['validateSub'])) {
        $response['status'] = 1;
    }
}

if (isset($_POST['action'])) {
    parse_str($_POST['formdata'], $form_arr);

    $dt = Datetime::createFromFormat('d/m/Y', $form_arr['date'])->format('Y-m-d');
    $form_arr['date'] = $dt;
    $form_arr['customerref'] = createRef();

    if (submit($form_arr)) {
        $response['status'] = 1;
        $response['confirmation'] = $form_arr;
    } else {
        $response['details'] = 'Something went wrong';
    }
}

echo json_encode($response);
