<?php
echo "Hello word";

echo"<br>today date is : 29/8/2025";
echo"<br>today time is : 12:55 <br>";

$a=1;
$b=5;
$c=$a+$b;
$d=$a.$b;
$e=$a-$b;
$f=$a/$b;

echo "<br>sum: $c";
echo"<br> mul:$d";
echo"<br> div:$f";
echo"<br> sub: $e<br>";

for($i=1; $i<=100;$i++){
    echo "<br>$i";
}
$f=92;
if($f%2==0){
   echo "<br><br>$f is even";
}
else{
      echo "<br>$f is odd";
}

$name= readline(prompt:"enter name:");
echo "<br><br>my name is:$name";
?>