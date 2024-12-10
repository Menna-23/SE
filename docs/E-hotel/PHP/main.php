<?php

session_start();
include("connection.php");
include("function.php");

if (isset($_POST['Check_Availability'])) {
    header("location: ../HTML/avrooms.html");
    die;
}
