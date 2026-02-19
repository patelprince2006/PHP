<?php
require_once "Product.php";
require_once "DigitalProduct.php";

$physical = new Product("Laptop", 60000, 18, 10);
$digital = new DigitalProduct("E-book: Learn PHP", 500, 0, 20, 15);

echo "<h2>🧾 Product Price Calculator Demo</h2>";
$physical->displayInfo();
$digital->displayInfo();

$physical->setPrice(65000);
$physical->setDiscount(5);

echo "<h3>After Update:</h3>";
$physical->displayInfo();
?>
