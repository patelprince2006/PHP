<?php
  $localhost="localhost";
  $username="root";
  $password="";
  $dbname="schema";

  $conn = new mysqli($localhost,$username,$password,$dbname);

 $sql = "CREATE TABLE IF NOT EXISTS student (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    branch VARCHAR(100)
)";

  $conn->query($sql);

    if($conn->connect_error){
        die("EError : ".$conn->connect_error);
    }
    echo"connection successfully";
?>