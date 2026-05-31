<?php
// Uruchomienie lub wznowienie istniejącej sesji
session_start();

// Kontrola dostępu: Jeśli użytkownik nie jest zalogowany (brak zmiennej 'user_id'),
// następuje natychmiastowe przekierowanie do strony logowania i przerwanie skryptu
if(!isset($_SESSION['user_id'])){
    header("Location: logowanie.php");
    exit;
}

// Pobranie identyfikatora zalogowanego użytkownika z sesji
$status = $_SESSION['user_id'];

// Nawiązanie połączenia z bazą danych 'zegowskaszama'
$db = mysqli_connect('localhost', 'root', '', 'zegowskaszama');

// Przygotowanie zapytania SQL usuwającego rekord użytkownika o danym ID z tabeli
// UWAGA BEZPIECZEŃSTWA: Jeśli w $_SESSION['user_id'] przechowujesz liczbę (ID), warto rzutować ją na typ int: (int)$status,
// aby dodatkowo zabezpieczyć zapytanie przed potencjalnymi błędami składni SQL.
$sql = "DELETE FROM użytkownicy WHERE użytkownicy.id = $status";

// Wykonanie zapytania usuwającego konto w bazie danych
mysqli_query($db, $sql);

// Usunięcie wszystkich zmiennych zarejestrowanych w aktualnej sesji (czyszczenie tablicy $_SESSION)
session_unset();

// Całkowite zniszczenie sesji (pliku sesji na serwerze oraz identyfikatora u klienta)
session_destroy();
?>