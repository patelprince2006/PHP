<?php
require_once "Product.php";

class DigitalProduct extends Product {
    private $fileSize; 

    public function __construct($name, $price, $taxRate, $discount, $fileSize) {
        parent::__construct($name, $price, $taxRate, $discount);
        $this->fileSize = $fileSize;
    }

    public function getFileSize() {
        return $this->fileSize;
    }

    public function setFileSize($fileSize) {
        $this->fileSize = $fileSize;
    }

    public function getFinalPrice() {
        $priceAfterDiscount = $this->getPrice() - ($this->getPrice() * $this->getDiscount() / 100);
        return round($priceAfterDiscount, 2);
    }

    public function displayInfo() {
        echo "💾 Digital Product: {$this->getName()}<br>";
        echo "File Size: {$this->fileSize} MB<br>";
        echo "Base Price: ₹{$this->getPrice()}<br>";
        echo "Discount: {$this->getDiscount()}%<br>";
        echo "Final Price (No Tax): ₹" . $this->getFinalPrice() . "<br><br>";
    }
}
?>
