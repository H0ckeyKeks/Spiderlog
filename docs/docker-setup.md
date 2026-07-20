# Spiderlog – Docker- & Symfony-Setup

Nachschlage-Dokumentation für den Aufbau der lokalen Entwicklungsumgebung: Schritte, Befehle und die Fehler, die dabei aufgetreten sind (inkl. Fixes).

## Stack

- Symfony 7.4 (PHP 8.3, da die neueste Skeleton-Version PHP 8.4 voraussetzt)
- Twig (Templates), Doctrine ORM (DB), Form + Validator (Formulare)
- MySQL 8.0
- Nginx als Webserver
- Adminer als DB-Weboberfläche
- Docker Desktop + Docker Compose

## 1. PHP-Dockerfile

`docker/php/Dockerfile`:

```dockerfile
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo pdo_mysql intl zip opcache gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
```

PHP 8.3-FPM als Basis. Die installierten Extensions: `pdo_mysql` für die DB-Verbindung, `intl` für Symfony-Lokalisierung, `zip`/`gd` u. a. fürs Foto-Feature. Composer wird direkt aus dessen offiziellem Image kopiert.

## 2. Nginx-Konfiguration

`docker/nginx/default.conf`:

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass php:9000;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $document_root;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/spiderlog_error.log;
    access_log /var/log/nginx/spiderlog_access.log;
}
```

`root` zeigt auf `public/`, dem Symfony-Front-Controller-Ordner. Anfragen ohne echte Datei-Treffer werden an `index.php` weitergereicht. PHP-Anfragen gehen über FastCGI an den `php`-Container (Servicename aus der `docker-compose.yml`).

## 3. docker-compose.yml

```yaml
services:
  php:
    build:
      context: ./docker/php
    volumes:
      - ./:/var/www/html
    depends_on:
      - database
    networks:
      - spiderlog

  nginx:
    image: nginx:1.27-alpine
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - php
    networks:
      - spiderlog

  database:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: spiderlog
      MYSQL_USER: spiderlog
      MYSQL_PASSWORD: spiderlog
      MYSQL_ROOT_PASSWORD: root
    volumes:
      - db_data:/var/lib/mysql
    ports:
      - "3306:3306"
    networks:
      - spiderlog

  adminer:
    image: adminer:latest
    ports:
      - "8081:8080"
    depends_on:
      - database
    networks:
      - spiderlog

volumes:
  db_data:

networks:
  spiderlog:
```

Vier Services: `php` (eigenes Image, kompletter Projektordner gemountet), `nginx` (Port 8080), `database` (MySQL 8, Zugangsdaten `spiderlog`/`spiderlog`, Port 3306), `adminer` (Port 8081, DB-Weboberfläche). `db_data` als benanntes Volume, damit die Datenbank Container-Neustarts übersteht.

## 4. Container starten

```
docker compose up -d --build
docker compose ps
```

**Fehler:** `unable to get image 'mysql:8.0': failed to connect to the docker API at npipe:////./pipe/dockerDesktopLinuxEngine ...`
**Ursache:** Docker Desktop war nicht geöffnet.
**Fix:** Docker Desktop starten, warten bis die Engine läuft (Tray-Icon grün), Befehl erneut ausführen.

Check: `http://localhost:8081` (Adminer, Server `database`, User/Passwort `spiderlog`/`spiderlog`) sollte laden, sobald die Container stehen.

## 5. Symfony-Projekt initialisieren

Direkter Versuch im `php`-Container:

```
docker compose exec php composer create-project symfony/skeleton . --no-interaction
```

**Fehler:** `Project directory "/var/www/html/." is not empty.`
**Ursache:** `.git`, `.gitignore`, `README.md`, `docker/`, `docker-compose.yml` lagen schon im Ordner.
**Fix:** Skeleton in einen temporären Ordner installieren und reinkopieren, eigene `.gitignore` danach wiederherstellen:

```
docker compose exec php composer create-project symfony/skeleton /tmp/skeleton --no-interaction
docker compose exec php sh -c "cp -a /tmp/skeleton/. /var/www/html/ && rm -rf /tmp/skeleton"
git checkout -- .gitignore
```

### Doctrine ORM installieren

```
docker compose exec php composer require symfony/orm-pack --no-interaction
```

**Fehler beim ersten DB-Test:** `docker compose exec php bin/console doctrine:query:sql "SELECT 1"` → `failed to parse docker-compose.yml: yaml: construct errors: line 48: mapping key "database" already defined`
**Ursache:** Die Doctrine-Flex-Recipe hat automatisch einen eigenen Postgres-`database`-Service in `docker-compose.yml` angehängt, obwohl schon ein MySQL-`database`-Service existierte → doppelter Schlüssel, ungültiges YAML.
**Fix:** Den automatisch angehängten Block (zwischen `###> doctrine/doctrine-bundle ###` und `###< doctrine/doctrine-bundle ###` unter `services` sowie den zugehörigen Eintrag `database_data:` unter `volumes`) wieder entfernt, ursprüngliche MySQL-Version behalten.

**Lehre:** Nach jedem `composer require`, das eine Symfony-Recipe mit `.docker`-Bezug installiert, kurz `git diff` prüfen, bevor man weitermacht – Recipes schreiben teils ungefragt in bestehende Dateien.

### Datenbankverbindung konfigurieren

`.env.local` (nicht versioniert):

```
DATABASE_URL="mysql://spiderlog:spiderlog@database:3306/spiderlog?serverVersion=8.0&charset=utf8mb4"
```

Wichtig: Host ist der Docker-Servicename `database`, nicht `localhost`.

Test:

```
docker compose exec php bin/console doctrine:query:sql "SELECT 1"
```

Ergebnis: Verbindung erfolgreich.

## 6. Twig, Form, Validator installieren

```
docker compose exec php composer require twig form validator --no-interaction
```

Legt u. a. `config/packages/twig.yaml`, `config/packages/validator.yaml`, `config/packages/csrf.yaml`, `config/packages/property_info.yaml` und `templates/base.html.twig` an.

## 7. PhpStorm-Konfiguration

Nach der Installation zeigte PhpStorms Commit-Analyse Fehler wie `Return type declaration is only allowed since PHP 7.0` in automatisch generierten Dateien (`config/reference.php`). Ursache: PHP Language Level in PhpStorm stand auf 5.6 statt auf der tatsächlich genutzten Version.

**Fix:** `Settings → PHP` (eigener Top-Level-Eintrag, nicht unter „Languages & Frameworks") → „PHP language level" auf **8.3** stellen (passend zum `php:8.3-fpm`-Image, nicht die neueste verfügbare Version wie 8.5). Änderung wurde erst nach Neustart von PhpStorm vollständig wirksam (Cache-Problem).

Weitere Warnungen, alles unkritisch:
- `base.html.twig`: fehlendes `lang`-Attribut am `<html>`-Tag → `<html lang="de">` ergänzen (kosmetisch/SEO).
- „Missed locally stored library for HTTP link" → reiner IDE-Komfort-Hinweis, keine Funktionsauswirkung.
- `composer.json`: „Path 'tests' not found" → verschwindet, sobald ein `tests/`-Ordner existiert.

## Git-Commit-Konventionen

Nach [Conventional Commits](https://www.conventionalcommits.org/), Format `<type>(<scope>): <description>`:

```
git commit -m "chore(docker): add PHP Dockerfile for app container"
git commit -m "chore(docker): add Nginx configuration for Symfony front controller"
git commit -m "chore(docker): add docker-compose setup for PHP, Nginx, MySQL and Adminer"
git commit -m "feat: bootstrap Symfony 7.4 skeleton with Doctrine ORM"
git commit -m "fix(docker): remove duplicate database service added by Doctrine recipe"
git commit -m "feat: install Twig, Form and Validator components"
```

`chore`/`build` für Tooling & Infrastruktur, `feat` für neue Funktionalität, `fix` für Fehlerbehebungen.

## Aktueller Stand

- [x] Docker-Umgebung (PHP, Nginx, MySQL, Adminer)
- [x] Symfony-Projekt initialisiert (v7.4)
- [x] Doctrine ORM, Twig, Form, Validator installiert
- [x] DB-Verbindung getestet
- [ ] Doctrine-Entities (`Species`, `Spider`, `Feeding`, `Molt`) aus dem DB-Modell erstellen
- [ ] Controller & Twig-Templates für die Mockup-Seiten (Hauptseite, Neue Art, Neue Spinne, Fütterung/Häutung hinzufügen, Archiv)
- [ ] Ampel-Logik für fällige Fütterungen
- [ ] Archivieren-Flow (`archived`, `archivierungsdatum`, `todestag`)
- [ ] Styling (AssetMapper + Symfony UX/Stimulus)
