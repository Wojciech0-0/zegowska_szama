<?php
// Uruchomienie lub wznowienie istniejącej sesji, aby serwer wiedział, którą sesję ma zamknąć
session_start();

// Czyszczenie tablicy $_SESSION – usuwa wszystkie zapisane dane (np. user_id, user_imie) w bieżącym skrypcie
session_unset();

// Całkowite zniszczenie sesji na serwerze (usuwa plik sesyjny przypisany do danego użytkownika)
session_destroy();
?>