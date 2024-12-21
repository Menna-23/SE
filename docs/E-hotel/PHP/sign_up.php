<?php
session_start();

include("connection.php");
include("function.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {

  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $password = trim($_POST['Password']);
  $age = (int)trim($_POST['age']);
  $national_id = trim($_POST['nat_id']);
  $phone = trim($_POST['pho']);
  $role = trim($_POST['role']);

  if (!empty($name) && !is_numeric($name) && !empty($email) && !empty($password) && !empty($age) && !empty($national_id) && !empty($phone) && !empty($role)) {

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);


    $stmt = $conn->prepare("INSERT INTO users (name, email, password, age, national_id, phone, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisss", $name, $email, $hashed_password, $age, $national_id, $phone, $role);

    if ($stmt->execute()) {

      header("Location: ../HTML/sign_in.html");
      die;
    } else {
      echo "Error: " . $stmt->error;
    }
  } else {
    echo "Please enter some valid information.";
  }
}
