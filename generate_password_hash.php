<?php
/**
 * Script to generate password hash for admin user
 * Run this once to get the correct hash for admin123
 */

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: {$password}\n";
echo "Hash: {$hash}\n";
echo "\nCopy this hash to your SQL INSERT statement.\n";
?>
