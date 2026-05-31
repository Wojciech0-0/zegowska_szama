<?php
// Uruchomienie lub wznowienie sesji – niezbędne do pobrania identyfikatora zalogowanego użytkownika
session_start();

// --- KONFIGURACJA RAPORTOWANIA BŁĘDÓW ---
// Włączenie wyświetlania błędów bezpośrednio w odpowiedzi skryptu (pomocne przy debugowaniu)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kontrola dostępu: Jeśli brak sesji użytkownika, przerywamy skrypt i zwracamy komunikat tekstowy dla JavaScript
if (!isset($_SESSION['user_id'])) {
    echo 'Błąd autoryzacji. Zaloguj się ponownie.';
    exit;
}

// Nawiązanie połączenia z bazą danych 'zegowskaszama'
$db = mysqli_connect('localhost', 'root', '', 'zegowskaszama');

// Weryfikacja, czy połączenie z serwerem MySQL się powiodło
if (!$db) {
    echo 'Błąd połączenia z bazą danych.';
    exit;
}
// Wymuszenie kodowania UTF-8 dla poprawnej pracy z polskimi znakami
mysqli_set_charset($db, "utf8mb4");

// Przypisanie ID zalogowanego użytkownika do zmiennej $status
$status = $_SESSION['user_id'];

// Pobranie danych przesłanych asynchronicznie (metodą POST) z formularza przez obiekt Fetch w JS
// Użycie skróconej instrukcji warunkowej (operator ternarny) zabezpiecza przed błędami braku indeksów
$stareHaslo = isset($_POST['stareHaslo']) ? $_POST['stareHaslo'] : '';
$noweHaslo1 = isset($_POST['noweHaslo1']) ? $_POST['noweHaslo1'] : '';
$noweHaslo2 = isset($_POST['noweHaslo2']) ? $_POST['noweHaslo2'] : '';

// Pobranie aktualnego, zahashowanego hasła użytkownika z bazy danych (zabezpieczenie ID przed SQL Injection)
$sql = "SELECT Haslo FROM użytkownicy WHERE id = '" . mysqli_real_escape_string($db, $status) . "'";
$wynik = mysqli_query($db, $sql);

// Sprawdzenie, czy zapytanie się powiodło i czy w bazie istnieje dokładnie jeden taki użytkownik
if (!$wynik || mysqli_num_rows($wynik) !== 1) {
    echo 'Nie znaleziono użytkownika w bazie.';
    exit;
}

// Wyciągnięcie danych użytkownika (tablicy asocjacyjnej) z wyniku zapytania
$hasloUser = mysqli_fetch_assoc($wynik);

// --- LOGIKA WERYFIKACJI I WALIDACJI HASEŁ (ZWRACANIE KOMUNIKATÓW DLA ALERTU JS) ---

// KROK 1: Sprawdzenie, czy pole nie jest puste oraz czy stare hasło pasuje do hasha z bazy (password_verify)
if (empty($stareHaslo) || !password_verify($stareHaslo, $hasloUser['Haslo'])) {
    echo 'Nieprawidłowe aktualne hasło!';
} else {
    // KROK 2: Walidacja złożoności – sprawdzenie, czy nowe hasło ma minimum 8 znaków długości
    if (strlen($noweHaslo1) < 8) {
        echo 'Nowe hasło jest za krótkie! (musi mieć przynajmniej 8 znaków)';
    } else {
        // KROK 3: Sprawdzenie, czy nowe hasło oraz jego powtórzenie są identyczne
        if ($noweHaslo1 === $noweHaslo2) {
            
            // KROK 4: Bezpieczne zahashowanie nowego hasła algorytmem bcrypt (PASSWORD_DEFAULT)
            $noweHasloZhashowane = password_hash($noweHaslo1, PASSWORD_DEFAULT);
            // Oczyszczenie ciągu znaków przed umieszczeniem go bezpośrednio w zapytaniu SQL
            $noweHasloZhashowaneEscaped = mysqli_real_escape_string($db, $noweHasloZhashowane);

            // Przygotowanie zapytania aktualizującego hasło użytkownika w bazie
            $update_sql = "UPDATE użytkownicy SET Haslo = '$noweHasloZhashowaneEscaped' WHERE id = '" . mysqli_real_escape_string($db, $status) . "'";
            
            // Wykonanie aktualizacji i weryfikacja jej powodzenia
            if (mysqli_query($db, $update_sql)) {
                // Skrypt JS w pliku widoku oczekuje tekstu zaczynającego się od słowa "Hasło", 
                // aby poprawnie przełączyć styl wizualny powiadomienia na zielony alert sukcesu.
                echo 'Hasło zmieniono pomyślnie!';
            } else {
                echo 'Błąd podczas zapisu nowego hasła w bazie danych.';
            }
        } else {
            echo 'Potwierdzone hasło nie jest identyczne z nowym hasłem!';
        }
    }
}
?>