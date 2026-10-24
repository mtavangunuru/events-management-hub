<?php
/**
 * Nuustar Events - Database Connection
 * Connects PHP to MySQL on local XAMPP
 */

$host = 'localhost';
$db_name = 'nuustar_events_db';
$username = 'root';
$password = ''; // Default XAMPP password is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => true,
    ]);
} catch (PDOException $e) {
    // If the database does not exist yet, show a helpful message
    die("<div style='font-family: sans-serif; padding: 30px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; max-width: 650px; margin: 40px auto; color: #9f1239;'>
        <h3 style='margin-top: 0;'>Database Connection Notice</h3>
        <p>Could not connect to database <strong>$db_name</strong>.</p>
        <p>Please open phpMyAdmin at <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a>, create database <strong>$db_name</strong>, and import <code>database.sql</code>.</p>
        <small>Error detail: " . htmlspecialchars($e->getMessage()) . "</small>
    </div>");
}
