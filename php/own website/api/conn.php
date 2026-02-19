<?php
// conn.php — simple mysqli connection wrapper
$cfg = require __DIR__ . '/db_config.php';

$mysqli = @new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['db']);
if ($mysqli->connect_errno) {
    // connection failed — set to null, endpoints should handle fallback
    $mysqli = null;
}

/**
 * Return mysqli instance or null on failure.
 */
function get_db()
{
    global $mysqli;
    return $mysqli;
}

?>
