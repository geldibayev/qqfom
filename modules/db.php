<?php
    $servername = 'MySQL-8.4';
    $username = 'root';
    $password = '';
    $dbname = 'qqfom';

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die('Ma\'lumotlar bazasiga ulanishda xatolik: ' . mysqli_connect_error());
    }

    if (!$conn->set_charset('utf8')) {
        die('Kodlashni o\'rnatishda xatolik: ' . $conn->error);
    }
?>
