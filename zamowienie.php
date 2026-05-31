<?php
// Uruchomienie lub wznowienie sesji – kluczowe dla identyfikacji kupującego
session_start();

// Kontrola dostępu: Jeśli użytkownik nie jest zalogowany, blokujemy akcję i przekierowujemy do logowania
if(!isset($_SESSION['user_id'])){
    header("Location: logowanie.php");
    exit;
}

// Pobranie identyfikatora zalogowanego użytkownika z sesji
$status = $_SESSION['user_id'];

// Generowanie unikalnego identyfikatora (tokenu) zamówienia na podstawie bieżącego czasu w mikrosekundach.
// Dzięki temu cała partia produktów zakupionych w tym samym momencie ma jeden, wspólny numer zamówienia.
$indeks = uniqid();

// Nawiązanie połączenia z bazą danych
$db = mysqli_connect('localhost', 'root', '', 'zegowskaszama');

// Weryfikacja połączenia z bazą danych
if (!$db) {
    die("Błąd połączenia z bazą: " . mysqli_connect_error());
}
// Ustawienie kodowania znaków na utf8mb4 dla prawidłowej obsługi polskich znaków
mysqli_set_charset($db, "utf8mb4");

// Przygotowanie zapytania pobierającego wszystkie produkty, które aktualnie znajdują się w koszyku danego użytkownika
$sql1 = "SELECT id_produktu FROM koszyk WHERE koszyk.id_uzytkownika = '" . mysqli_real_escape_string($db, $status) . "'";

// Pobranie bieżącej daty w formacie RRRR-MM-DD
$data = date("Y-m-d");

// Wykonanie zapytania pobierającego zawartość koszyka
$wynik1 = mysqli_query($db, $sql1);

    // Pętla przepisująca produkty z koszyka do tabeli zamówień
    while($d = mysqli_fetch_array($wynik1)){
        $id_prod = $d['id_produktu'];
        
        // Przygotowanie zapytania INSERT. Struktura tabeli zamówienia zakłada:
        // NULL (dla auto-increment ID zamówienia), id_użytkownika, id_produktu, datę, unikalny indeks transakcji oraz status (0 - np. "złożone/nieopłacone")
        $sql = "INSERT INTO zamówienia VALUES (NULL, '" . mysqli_real_escape_string($db, $status) . "', '" . mysqli_real_escape_string($db, $id_prod) . "', 
                        '$data', 
                        '$indeks' , 0)";
                        
        // Wykonanie zapytania dodającego pojedynczy produkt do historii zamówień
        mysqli_query($db, $sql);
    }

    // Po pomyślnym skopiowaniu wszystkich produktów do tabeli zamówień, 
    // koszyk użytkownika zostaje całkowicie opróżniony
    mysqli_query($db, "DELETE FROM koszyk WHERE koszyk.id_uzytkownika = '" . mysqli_real_escape_string($db, $status) . "'");
    

// Zamknięcie bezpiecznego połączenia z bazą danych
mysqli_close($db);
?>