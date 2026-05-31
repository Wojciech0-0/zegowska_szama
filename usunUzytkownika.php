<?php
// Uruchomienie lub wznowienie sesji – niezbędne do sprawdzenia uprawnień administratora
session_start();

// --- KONTROLA DOSTĘPU (AUTORYZACJA) ---
// Sprawdzenie, czy użytkownik nie jest zalogowany LUB czy jego identyfikator jest inny niż 'admin'
if(!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 'admin'){
    // Osoby niebędące administratorem są natychmiast przekierowywane na stronę główną sklepu
    header("Location: sklep.php");
    // Przerwanie wykonywania skryptu, aby zablokować nieautoryzowane usunięcie danych
    exit;
}

// Połączenie z bazą danych MySQL (host, użytkownik, hasło, nazwa bazy danych)
$db = mysqli_connect('localhost','root','','zegowskaszama');

// Pobranie przesłanego identyfikatora użytkownika z tablicy POST (ID konta przeznaczonego do usunięcia)
// Użycie operatora COALESCE (??) zapobiega powstawaniu błędów, przypisując pusty ciąg, jeśli 'id' nie zostało wysłane
$id = $_POST['id'] ?? '';

// Sprawdzenie, czy zmienna $id zawiera jakąkolwiek wartość (czy formularz wysłał poprawne dane)
if($id != ''){
    // Przygotowanie zapytania SQL kasującego użytkownika o wskazanym ID
    // UWAGA BEZPIECZEŃSTWA: Warto rzutować $id na liczbę: (int)$id przed wstawieniem do zapytania,
    // aby upewnić się, że nikt nie przesyła zmanipulowanych danych (SQL Injection).
    $sql = "DELETE FROM użytkownicy WHERE id = $id";

    // Wykonanie zapytania usuwającego rekord z bazy danych
    mysqli_query($db, $sql);
}

// Po wykonaniu operacji usunięcia (lub w przypadku odebrania pustego ID),
// następuje natychmiastowe przekierowanie z powrotem do panelu zarządzania użytkownikami
header("Location: admin_uzytkownicy.php");
?>