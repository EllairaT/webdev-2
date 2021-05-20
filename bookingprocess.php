<?php
session_start();
header('Content-Type: application/json');

include 'dbcon.php';
$conn = Connect();

function validateSuburb($param)
{
    global $conn;

    $search_query = $conn->prepare("SELECT `SUBURB_NAME` FROM `suburbs` WHERE  `SUBURB_NAME` = ?");
    $search_query->bind_param("s", $searchvar);

    $searchvar = $param;

    $search_query->execute();
    $search_query->store_result();

    //check if db returns a result
    if ($search_query->num_rows() > 0) {
        return true;
    } else {
        return false;
    }
}

$response = array('status' => 0, 'details' => '');

$search_query =  $conn->prepare("SELECT `SUBURB_NAME` FROM `suburbs` WHERE  `SUBURB_NAME` LIKE ? ");
$search_query->bind_param("s", $searchvar);

$searchvar;
$suburb_arr = array();

if (isset($_GET['sbname'])) {
    $searchvar = '%' . $_GET['sbname'] . '%';

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
    $searchvar = '%' . $_GET['dsname'] . '%';

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
}

echo json_encode($response);
