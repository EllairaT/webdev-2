<?php

function Connect()
{
    require_once('../../conf/sqlinfo.inc.php');

    date_default_timezone_set('Pacific/Auckland');

    //creating connection 
    $conn = new mysqli($sql_host, $sql_user, $sql_pass, $sql_db);

    //checking connection   
    if ($conn->connect_errno) {
        die("Something went wrong, failed to connect to MySQL: " . $conn->connect_error);
    }
    return $conn;
}
