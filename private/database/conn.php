<?php
    try {
        $conn = new PDO('mysql:host=localhost;dbname=ufg100420', 'root','03312001');
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
?>