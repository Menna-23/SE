<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbusers = "hotel_management";


$conn = mysqli_connect($servername, $username, $password, $dbusers);

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$name = $_POST['name'];
$email = $_POST['email'];
$password =$_POST['Password'];
$age = (int)$_POST['age']; 
$national_id = $_POST['nat_id'];
$phone = $_POST['pho'];
$role = $_POST['role'];

$sql = "INSERT INTO users (name, email, password, age, national_id, phone, role) 
        VALUES ('$name', '$email', '$password', $age, '$national_id', '$phone', '$role')";


if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

?>