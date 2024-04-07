<?php
// Array of passwords to hash
$passwords = ['adminadmin2', 'grace247', 'iamapassword3', 'iamapassword4', 'iamapassword5'];

foreach ($passwords as $password) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    echo $hashed . "\n";
}
?>
