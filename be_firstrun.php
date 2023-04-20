<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8" />
        <link rel="stylesheet" href="vendor/blog-engine/blog.css" />
        <script type="module" src="vendor/blog-engine/blog.js" defer></script>
        <title>Mój pierwszy blog!</title>
    </head>
    <body class="first">
        <div class="container fr-page">
            <h1>Witaj świecie!</h1>
            <p>Jestem Twoim przyszłym blogiem! :D</p>
            <p>Ale zanim do tego dojdzie, skonfiguruj mnie. Przeprowadzę Cię przez proces konfiguracji mnie, dzięki któremu będę żył! Nie potrzeba poświęcać mi dużo czasu. Wystarczy dosłownie maks 5 minut.</p>
            <hr>
            <form method="POST" action="index.php">
                <label class="form-label" for="BLOG_AUTHOR">Jak się nazywasz mój twórco?</label>
                <input class="form-control mb-3" type="text" name="BLOG_AUTHOR" placeholder="Jan Kowalski" required/>
                <div id="config-blog">
                    <hr>
                    <h3>Konfiguracja bloga</h3>
                    <p>Nazwij mnie i opisz krótko, co takiego będzie się u mnie znajdowało? Daj też słowa kluczowe, by można było mnie znaleźć w wyszukiwarkach.</p>
                    <label class="form-label" for="BLOG_TITLE">Tytuł bloga:</label>
                    <input class="form-control mb-3" type="text" name="BLOG_TITLE" placeholder="Kwiatowy Blog" required/>
                    <label class="form-label" for="BLOG_DESC">Opis bloga:</label>
                    <textarea class="form-control mb-3" name="BLOG_DESC" placeholder="Blog o kwiatach w Polsce" required></textarea>
                    <label class="form-label" for="BLOG_TAGS">Tagi bloga:</label>
                    <input class="form-control mb-3" type="text" name="BLOG_TAGS" placeholder="kwiaty, rośliny, drzewa" required/>
                    <label class="form-label" for="BLOG_DOMAIN">Domena bloga:</label>
                    <input class="form-control mb-3" type="text" name="BLOG_DOMAIN" placeholder="https://kwiatowyblog.pl" />
                </div>
                <div id="config-db">
                    <hr>
                    <h3>Konfiguracja bazy danych</h3>
                    <p>Wskaż mi miejsce, gdzie będę zapisywał Twoje przyszłe cudowne posty! Działam jedynie na MySQL/MariaDB. </p>
                    <label class="form-label" for="DB_HOST">Adres IP i port:</label>
                    <input class="form-control mb-3" type="text" name="DB_HOST" placeholder="127.0.0.1:3306" required/>
                    <div class="row">
                        <div class="col">
                            <label class="form-label" for="DB_USER">Nazwa użytkownika:</label>
                            <input class="form-control mb-3" type="text" name="DB_USER" placeholder="root" required>
                        </div>
                        <div class="col">
                            <label class="form-label" for="DB_PASS">Hasło:</label>
                            <input class="form-control mb-3" type="password" name="DB_PASS" placeholder="hasło" />
                        </div>
                    </div>
                    <label class="form-label" for="DB_NAME">Baza danych:</label>
                    <input class="form-control mb-3" type="text" name="DB_NAME" placeholder="db_kwiatowyblog" required/>
                    <button class="btn btn-primary mb-3" type="button" id="testConn">Testuj połączenie</button>
                    <p id="connStatus"></p>
                </div>
                <div id="config-admin">
                    <hr>
                    <h3>Administrator bloga</h3>
                    <p>Nadaj sobie nazwę użytkownika oraz hasło, dzięki któremu będziesz logować się do panelu bloga!</p>
                    <label class="form-label" for="ADMIN_USER">Nazwa użytkownika:</label>
                    <input class="form-control mb-3" type="text" name="ADMIN_USER" placeholder="admin" required>
                    <label class="form-label" for="ADMIN_EMAIL">Nazwa użytkownika:</label>
                    <input class="form-control mb-3" type="email" name="ADMIN_EMAIL" placeholder="admin@blog.pl" required>
                    <div class="row">
                        <div class="col">
                            <label class="form-label" for="ADMIN_PASS">Hasło:</label>
                            <input class="form-control mb-3" type="password" name="ADMIN_PASS" placeholder="hasło" required/>
                        </div>
                        <div class="col">
                            <label class="form-label" for="ADMIN_REPASS">Powtórz hasło:</label>
                            <input class="form-control mb-3" type="password" name="ADMIN_REPASS" placeholder="hasło" required/>
                        </div>
                    </div>
                </div>
                <div id="config-email">
                    <hr>
                    <h3>Konfiguracja serwera poczty</h3>
                    <p>Powiadamiaj swoich czytelników o Twoich nowych postach lub aktualizacjach, które zostały wprowadzone na tym blogu! Nie jest wymagane, jeżeli nie chcesz korzystać z newsletterów.</p>
                    <label class="form-label" for="MAIL_NAME">Adres e-mail:</label>
                    <input class="form-control mb-3" type="email" name="MAIL_NAME" placeholder="kwiatowyblog@gmail.com">
                    <label class="form-label" for="MAIL_PASS">Hasło:</label>
                    <input class="form-control mb-3" type="password" name="MAIL_PASS" placeholder="hasło">
                    <label class="form-label" for="MAIL_SRV">Serwer:</label>
                    <input class="form-control mb-3" type="text" name="MAIL_SRV" placeholder="smtp.gmail.com">
                    <label class="form-label" for="MAIL_PORT">Port:</label>
                    <div class="row mb-3">
                        <div class="col-md-9">
                            <input class="form-control" type="number" max="65565" min="1" name="MAIL_PORT" placeholder="465">
                        </div>
                        <div class="col-md-3" style="display: flex; align-items: center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="MAIL_SSL" checked />
                                <label class="form-check-label" for="MAIL_SSL">Wymaga połączenia SSL</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="config-end">
                    <hr>
                    <h3>Koniec podstawowej konfiguracji!</h3>
                    <p>Widzisz! Nie było to aż tak trudne. :D Teraz możesz zacząć przygodę ze swoim blogowaniem, wciskając przycisk <b>Pora zacząć przygodę!</b>. Teraz pozostaje Ci tworzyć posty, które znajdą swoich ulubionych czytelników. Życzę Ci powodzenia i miłego blogowania!</p>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-success btn-lg" type="submit" name="config-me">Pora zacząć przygodę!</button>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>
