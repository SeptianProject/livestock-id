<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["nama-lengkap"];
    $password = $_POST["kata-sandi"];

    echo "Nama Lengkap: " . $name . "<br>";
    echo "Password: " . $password;
}
