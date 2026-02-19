<?php
// Global variable declarations
$x = 5;
$y = 10;
$z = 20;

$txt1 = "Learn PHP";
$txt2 = "Charusat";

// Function using 'global' keyword
function myTest() {
  global $x, $y;
  $y = $x + $y; // $y becomes 15
}

// Function using $GLOBALS array
function test() {
  $GLOBALS['z'] = $GLOBALS['x'] + $GLOBALS['z']; // $z becomes 25
}

// Execute functions
myTest();
test();

// br used /n(in c)
echo "$y<br>";     // Outputs: 15
echo "$z<br>";     // Outputs: 25
echo '<h2>' . $txt1 . '</h2>';
echo '<p>Study PHP' . $txt2 . '</p>';

?>
