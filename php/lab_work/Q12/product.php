<?php
class Product {
    private $name;
    private $price;
    private $taxRate;
    private $discount;

    public function __construct($name, $price, $taxRate = 0, $discount = 0) {
        $this->name = $name;
        $this->price = $price;
        $this->taxRate = $taxRate;
        $this->discount = $discount;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getTaxRate() {
        return $this->taxRate;
    }

    public function setTaxRate($taxRate) {
        $this->taxRate = $taxRate;
    }

    public function getDiscount() {
        return $this->discount;
    }

    public function setDiscount($discount) {
        $this->discount = $discount;
    }

    public function getFinalPrice() {
        $priceWithTax = $this->price + ($this->price * $this->taxRate / 100);
        $finalPrice = $priceWithTax - ($priceWithTax * $this->discount / 100);
        return round($finalPrice, 2);
    }

    public function displayInfo() {
        echo "🛒 Product: {$this->name}<br>";
        echo "Base Price: ₹{$this->price}<br>";
        echo "Tax Rate: {$this->taxRate}%<br>";
        echo "Discount: {$this->discount}%<br>";
        echo "Final Price: ₹" . $this->getFinalPrice() . "<br><br>";
    }
}
?>
