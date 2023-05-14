<?php
    /*
     *      KONFIGURACJA BLOGA
     */

    // Czy blog przeszedł już pierwsze uruchomienie
    const BLOG_RAN = true;

    // Tytuł bloga
    const BLOG_TITLE = "Kwiatowy Blog";

    // Opis bloga
    const BLOG_DESC = "ef";

    // Autor bloga/Nazwa wyświetlana dla głównego administratora bloga
    const BLOG_AUTHOR = "Krzysiek Smaga";

    // Tagi bloga
    const BLOG_TAGS = "kwiaty, rośliny";

    // Domena bloga
    const BLOG_DOMAIN = "http://localhost";

    // Ikona bloga (format .PNG)
    const BLOG_ICON = "";

    /*
     *      KONFIGURACJA BAZY DANYCH
     */

    // Adres IP do serwera bazy danych MySQL/MariaDB
    const DB_HOST = "127.0.0.1:3306";

    // Nazwa użytkownika do serwera bazy danych MySQL/MariaDB
    const DB_USER = "root";

    // Hasło do użytkownika serwera bazy danych MySQL/MariaDB
    const DB_PASS = "";

    // Baza danych dla bloga
    const DB_NAME = "blog";

    /*
     *      KONFIGURACJA NARZĘDZIA SERWEROWEGO
     */

    // Port, który ma używać PHP dla serwera publicznego
    const PUB_PORT = "8085";

    // Port, który ma używać PHP dla serwera deweloperskiego
    const DEV_PORT = "8086";

    /*
     *      KONFIGURACJA KONTA ADMINISTRATORA
     */

    // Nazwa użytkownika dla głównego administratora bloga
    const ADMIN_USER = "admin";

    // Adres mailowy kontaktowy do głównego administratora bloga
    const ADMIN_EMAIL = "krzysiek-smaga@outlook.com";

    // Hasło dla głównego administratora bloga
    const ADMIN_PASS = "root";


    /*
     *      KONFIGURACJA POCZTY, DLA NEWSLETTERÓW
     */

    // Adres e-mail, z którego będą wysyłane wiadomości mailowe
    const MAIL_NAME = "";

    // Hasło do poczty
    const MAIL_PASS = "";

    // Serwer wychodzący poczty e-mail
    const MAIL_SRV = "";

    // Port do serwera wychodzącego poczty e-mail
    const MAIL_PORT = "";

    // Wymaga SSL'a
    const MAIL_SSL = 1;