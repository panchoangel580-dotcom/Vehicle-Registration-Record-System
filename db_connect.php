<?php
/**
 * db_connect.php
 * Database connection (MySQLi). Update the credentials below.
 *
 * LOCAL (XAMPP) : host=localhost, user=root, pass='' , db=vehicle_record_db
 * INFINITYFREE  : copy hostname, username, password and database name
 *                 from Control Panel -> MySQL Databases (e.g. sql123.infinityfree.com)
 */
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'vehicle_record_db';

// Throw exceptions on errors so we can catch them (e.g. duplicate plate numbers)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $ex) {
    http_response_code(500);
    die('<h3 style="font-family:sans-serif">Database connection failed.</h3>'
      . '<p style="font-family:sans-serif">Check the credentials in <code>db_connect.php</code>.</p>');
}
