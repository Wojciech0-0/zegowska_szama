
let register_button = document.getElementById('register_button')

if(register_button){
register_button.addEventListener("click", (e) => {
    e.preventDefault();

    let blad = document.getElementById('blad');

    // reset komunikatu
    blad.textContent = "";
    blad.classList.add("d-none"); // ukryj na start

    let imie = document.getElementById('imie');
    let nazwisko = document.getElementById('nazwisko');
    let email = document.getElementById('email');
    let haslo = document.getElementById('haslo');
    let haslo2 = document.getElementById('haslo2');

    // reset borderów
    imie.style.border = "";
    nazwisko.style.border = "";
    email.style.border = "";
    haslo.style.border = "";
    haslo2.style.border = "";

    if (imie.value.trim() === "") {
        imie.style.border = "2px solid red";
        blad.textContent = "Podaj imię!";
        blad.classList.remove("d-none");
    }

        else if (nazwisko.value.trim() === "") {
            nazwisko.style.border = "2px solid red";
            blad.textContent = "Podaj nazwisko!";
            blad.classList.remove("d-none");
        }

        else if (email.value.trim() === "") {
            email.style.border = "2px solid red";
            blad.textContent = "Podaj email!";
            blad.classList.remove("d-none");
        }

        else if (haslo.value.trim() === "") {
            haslo.style.border = "2px solid red";
            blad.textContent = "Podaj hasło!";
            blad.classList.remove("d-none");
        }

        else if (haslo2.value.trim() === "") {
            haslo2.style.border = "2px solid red";
            blad.textContent = "Potwierdź hasło!";
            blad.classList.remove("d-none");
        }

        else if (haslo.value.trim() !== haslo2.value.trim()) {
            haslo.style.border = "2px solid red";
            haslo2.style.border = "2px solid red";
            blad.textContent = "Podane hasła się różnią!";
            blad.classList.remove("d-none");
        }

      else {
        let user = {
            email: email.value.trim(),
            password: haslo.value.trim()
        };

        localStorage.setItem("user", JSON.stringify(user));

        window.location.href = "logowanie.html";
    }
});}


let login_button = document.getElementById('login_button')

if (login_button){
login_button.addEventListener("click", (e) => {
    e.preventDefault();

    let email_logowanie = document.getElementById('email_logowanie');
    let haslo_logowanie = document.getElementById('haslo_logowanie');
    let blad_logowanie = document.getElementById('blad_logowanie');

    blad_logowanie.textContent = "";
    blad_logowanie.classList.add("d-none");

    email_logowanie.style.border = "";
    haslo_logowanie.style.border = "";

    let savedUser = JSON.parse(localStorage.getItem("user"));

    if (!savedUser) {
        blad_logowanie.textContent = "Brak konta! Zarejestruj się.";
        blad_logowanie.classList.remove("d-none");
        return;
    }

    if (email_logowanie.value.trim() !== savedUser.email) {
        email_logowanie.style.border = "2px solid red";
        blad_logowanie.textContent = "Nieprawidłowy email!";
        blad_logowanie.classList.remove("d-none");
        return;
    }

    if (haslo_logowanie.value.trim() !== savedUser.password) {
        haslo_logowanie.style.border = "2px solid red";
        blad_logowanie.textContent = "Nieprawidłowe hasło!";
        blad_logowanie.classList.remove("d-none");
        return;
    }

    window.location.href = "sklep.html";
});}