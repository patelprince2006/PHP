<?php
class Car {
  public $color;
  public $model;
 // you create a __construct() function, PHP will automatically call this function when you create an object from a class.
  public function __construct($color, $model) {
    $this->color = $color;
    $this->model = $model;
  }
  public function message() {
    echo "My car is a " . $this->color . " " . $this->model . "!";
  }
}

$myCar = new Car("red", "Volvo");
// $myCar.message();
var_dump($myCar);
?>