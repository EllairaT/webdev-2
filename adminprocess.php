<?php

session_start();
header('Content-Type: application/json');

include 'dbcon.php';
$conn = Connect();

function search()
{
}
function searchTwoHours()
{
}

function updateStatus()
{
}

$response = ['status' => 0, 'details' => '', 'data' => ''];

if (isset($_GET['bsearch'])) {
    $search = $_GET['bsearch'];
    if ('' == $search) {
        searchTwoHours();
    } else {
        search();
    }
}
