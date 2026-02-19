<?php

 include "fetch.php";

 $sql="SELECT * FROM event";
$result=$conn->query($sql);

if($result->num_rows>0){
    while($row=$result->fetch_assoc()){
         echo "<br>no".$row['no']."<br>name".$row['name']."<br>date".$row['date'];
    }
    echo "successsfully feched";
}
else{
    echo "error".$conn->connect_error;
}
$conn->close();
?>