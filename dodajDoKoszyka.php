<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: logowanie.php");
    exit;
}

$status = $_SESSION['user_id'];

$id_produktu = $_GET['id'] ?? 0;
// polaczenie z baza
$db = mysqli_connect('localhost','root','','zegowskaszama');


// kwerernda
mysqli_query($db,"INSERT INTO koszyk VALUES (NULL, $id_produktu, $status)");

header("Location: sklep.php");
?>