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

function insertCustomer($arr)
{
    global $conn;
    $sql_customer = 'INSERT INTO `customers`(`CUSTOMER_NAME`, `CUSTOMER_PH`) VALUES (?,?)';
    $insert_customer = $conn->prepare($sql_customer);
    $insert_customer->bind_param('ss', $cname, $cphone);

    $cname = $arr['cname'];
    $cphone = $arr['phone'];

    if ($insert_customer->execute()) {
        $insert_customer->store_result();

        return true;
    }

    return false;
}

function insertSuburb($type, $suburb, $ref)
{
    global $conn;
    $column = 'origin' == $type ? 'ORIGIN_SUBURB_ID' : 'DESTINATION_SUBURB_ID';

    $query = 'INSERT INTO `booking_information`( ? )
                SELECT `SUBURB_ID`
                FROM `suburbs`
                WHERE `REF_ID` = ? 
                AND `suburbs`.`SUBURB_NAME` = ?';

    $insert = $conn->prepare($query);
    $insert->bind_param('sss', $column, $ref, $suburb);

    if ($insert->execute()) {
        $insert->store_result();

        return true;
    }

    return false;
}

function insertInfo($arr)
{
    global $conn;
    $param_arr = [];

    $sql_info = 'INSERT INTO `booking_information`(`REF_NUM`, `UNIT_NUMBER`, `STREET_NUMBER`, `STREET_NAME`, 
                               `PICKUP_DATE`, `PICKUP_TIME`) 
                 VALUES (?,?,?,?,?,?,?,?)';

    $insert_info = $conn->prepare($sql_info);
    $insert_info->bind_param('ssssss', $searchvar);

    //if isset origin and destination subs
    foreach ($arr as $a) {
    }

    if (!$insert_info->execute()) {
        return false;
    }
    $insert_info->store_result();
}

function insertBooking($arr)
{
    global $conn;
    //two statements, but one transaction.
    $sql_booking = 'INSERT INTO `bookings`(`CUSTOMER_ID`)
    SELECT CUSTOMER_ID 
    FROM customers
    WHERE customers.CUSTOMER_NAME = ?
    AND customers.CUSTOMER_PH = ?;
    
    INSERT INTO `bookings`(`CUSTOMER_ID`, `BOOKING_REF`, `BOOKING_DATE`) 
    VALUES (?,?,?)';

    $insert_booking = $conn->prepare($sql_booking);
    $insert_booking->bind_param('ssssss', $searchvar);

    if (!$insert_booking->execute()) {
        return false;
    }
    $insert_booking->store_result()
    ;
}

// executes all the queries
function submit($arr)
{
    global $conn;
    if (insertCustomer($arr) && insertInfo($arr) && insertBooking($arr)) {
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

$response = ['status' => 0, 'details' => '', 'data' => ''];

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
        $response['status'] = 'Success';
    }
} elseif (isset($_POST['submitted'])) {
    foreach ($_POST as $k) {
        array_push($form_arr, $k);
    }
    //submit($_POST);
    $form_arr['customerref'] = createRef();
    $response = $form_arr;
}

echo json_encode($response);
//echo insertSuburb('des', '', '');
