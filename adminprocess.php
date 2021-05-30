<?php

session_start();
header('Content-Type: application/json');

include 'dbcon.php';
$conn = Connect();

function updateStatus($ref)
{
    global $conn;
    $sql = 'UPDATE `bookings` SET `BOOKING_STATUS`="assigned" WHERE `BOOKING_REF` = ?';
    $update_query = $conn->prepare($sql);
    $update_query->bind_param('s', $ref);

    $update_query->execute();
    $update_query->close();
}

$search_sql = 'SELECT
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
    WHERE booking_information.BOOKING_REF LIKE ?
    AND bookings.BOOKING_REF = booking_information.BOOKING_REF';

$search_sql_twohrs = 'SELECT
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
    booking_information.PICKUP_TIME < DATE_ADD(now(),interval 2 hour)
    AND bookings.BOOKING_REF = booking_information.BOOKING_REF';

$response = ['status' => 0, 'details' => '', 'data' => ''];

if (isset($_GET['bsearch'])) {
    if ('' == $_GET['bsearch']) {
        $search_query = $conn->prepare($search_sql_twohrs);
        $row = '';

        if ($search_query->execute()) {
            $search_query->store_result();
            $search_query->bind_result($ref, $customer, $phone, $origin, $destination, $date, $time, $status);

            if ($search_query->num_rows() > 0) {
                while ($search_query->fetch()) {
                    $row .= ' 
                <tr class="table-warning">
                <th scope="row">'.$ref.'</th>
                <td>'.$customer.'</td>
                <td>'.$phone.'</td>
                <td>'.$origin.'</td>
                <td>'.$destination.'</td>
                <td>'.$date.'</td>
                <td>'.$time.'</td>
                <td>'.$status.'</td>
                <td><button name="assign" class="btn btn-success">Assign Taxi</button></td>
                </tr>';
                }
            }
        }
        echo $row;
    } else {
        $search = '%'.$_GET['bsearch'].'%';
        $search_query = $conn->prepare($search_sql);
        $search_query->bind_param('s', $search);

        $row = '';

        if ($search_query->execute()) {
            $search_query->store_result();
            $search_query->bind_result($ref, $customer, $phone, $origin, $destination, $date, $time, $status);

            if ($search_query->num_rows() > 0) {
                while ($search_query->fetch()) {
                    $row .= ' 
                <tr class="table-warning">
                <th scope="row">'.$ref.'</th>
                <td>'.$customer.'</td>
                <td>'.$phone.'</td>
                <td>'.$origin.'</td>
                <td>'.$destination.'</td>
                <td>'.$date.'</td>
                <td>'.$time.'</td>
                <td>'.$status.'</td>
                <td><button name="assign" class="btn btn-success" onClick="assignTaxi(this)" id="'.$ref.'">Assign Taxi</button></td>
                </tr>';
                }
            }
        }
        echo $row;
    }
}

if (isset($_POST['action'])) {
    updateStatus($_POST['ref']);
}
