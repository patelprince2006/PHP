<?php
$localhost="localhost";
$username="root";
$password="";
$dbname="student";

$conn=new mysqli($localhost,$username,$password,$dbname);

if($conn->connect_error){
    die("error:".$conn->connect_error);
}

echo "successfully";

// $sql="SELECT * FROM event";
// $result=$conn->query($sql);

// if (!$result) {
//     die("Query failed: " . $conn->error);
// }

// if($result->num_rows>0){
//     while($row=$result->fetch_assoc()){
//          echo "<br>no".$row['no']."<br>name".$row['name']."<br>date".$row['date'];
//     }
//     echo "successsfully feched";
// }
// else{
//     echo "error".$conn->connect_error;
// }
// $conn->close();
?>