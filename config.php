<?php
// Check if mysqli extension is installed
if (!extension_loaded('mysqli')) {
    die("Error: MySQLi extension is not installed. Please install php-mysqli extension.");
}

// Primary database configuration
define('DB_HOST', '182.61.1.142');       // Database host address (server public IP)
define('DB_PORT', '13306');              // Database port
define('DB_NAME', 'ghb7zzwh6fy5j3yl');   // Primary database name
define('DB_USER', 'root');               // Database username
define('DB_PASS', 'BtXELjPMb4dadjPy');   // Primary database password
define('DB_CHARSET', 'utf8mb4');         // Character set



// Create database connection
function getDBConnection() {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    // Check if connection is successful
    if ($mysqli->connect_error) {
        die("Database connection failed: " . $mysqli->connect_error);
    }

    // Set character set
    $mysqli->set_charset(DB_CHARSET);

    return $mysqli;
}

// Create secondary database connection (uses same password as primary)
function getECommerceDBConnection() {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB2_NAME, DB_PORT);

    if ($mysqli->connect_error) {
        die("Secondary database connection failed: " . $mysqli->connect_error);
    }

    $mysqli->set_charset(DB_CHARSET);

    return $mysqli;
}
?>
