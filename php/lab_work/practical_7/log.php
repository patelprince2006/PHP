<?php
$localhost="localhost";
$sname="root";
$pass="";
$db="pratice";

session_start();

$conn = new mysqli($localhost,$sname,$pass,$db);

if($conn->connect_error){
    echo "error:".$conn->$connect_error;
}
echo "success";

if($_SERVER['REQUEST_METHOD']=="POST" &&isset($_POST['register'])){
$name=$_POST['name'];
$email=$_POST['email'];
$pass=$_POST['password'];

$stmt= $conn->prepar("INSERT INTO log(name,email,password) VALUE ($name,$email,$pass)");
 if($stmt->execute()){
    echo "successfully inserted";
 }
}


// if($_SERVER['REQUEST_METHOD']=="POST" &&$_POST['login']){
// $name=$_POST['name'];
// $email=$_POST['email'];
// $pass=$_POST['password'];
// $stmt= $conn->prepar("UPDATE INTO log(name,password) WHERE $email=?");
// $stmt=$conn->bind_param("s",$email);

// if($pass==hash()){
   
// }
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>php trayl</title>
</head>
<body>
   <form method="POST">
      Name: <input type="text" name="name" required>
      email: <input type="text" name="email" required>
      passsword: <input type="text" name="paassword"required>
      <button id="button" name="register">register</button>
   </form>

   <!-- <form method="POST">
      Name: <input type="text" name="name" required>
      email: <input type="text" name="email" required>
      passsword: <input type="text" name="paassword"required>
      <button id="button" name="login">login</button>
   </form> -->
</body>
