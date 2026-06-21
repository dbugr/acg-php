# adventureclub.info — localhost dev environment

Runs the Adventure Club of Gainesville PHP/MySQL website locally via Docker.

## Requirements

- Docker + Docker Compose

## Quick start

```bash
docker compose up -d
```

Site is available at http://localhost:8080

## Stop / restart

```bash
docker compose down        # stop (database preserved)
docker compose down -v     # stop and wipe database (reimports on next start)
docker compose up -d       # start again
docker compose logs -f     # tail logs
```

## Stack

| Component | Details |
|---|---|
| Web | PHP 7.4 + Apache 2.4 |
| Database | MySQL 8.0 |
| Port | 8080 → 80 |

## Project layout

```
acg-prj/
├── Dockerfile               # PHP 7.4-apache image with extensions
├── docker-compose.yml       # web + db services
├── config/
│   ├── acg.ini              # PHP settings (app_env, db hostname, paths)
│   └── vhost.conf           # Apache virtual host
├── site/                    # website files (extracted from backup archive)
│   ├── lib/                 # PHP libraries and Composer dependencies
│   ├── public_html/         # Apache document root
│   └── var/                 # logs, sessions, photos (writable)
└── mysql-init/
    └── advclub.sql          # database dump — imported on first container start
```

## Source of truth

Files in `site/` were extracted from the backup archives in
`/home/chucky/prj/acg-archive/bak/`. The most recent backups used:

- `bak/files/acg_club_files_advclub_2026-05-10_07.20.01.tar.gz`
- `bak/sql/acg_club_dump_advclub_2026-05-10_07.15.01.sql.tar.gz`

## Email

`app_env = "dev"` is set in `config/acg.ini`. In dev mode the site logs
outbound emails to `site/var/log/advclub.log` instead of sending them via
Mailgun.

## Source changes from the original backup

Two lines were modified in `site/lib/always.include.php` to work inside
Docker:

1. Composer autoloader — changed from a hardcoded host path to a path
   relative to the `lib/` directory.
2. Database hostname — made overridable via `php.ini` so `config/acg.ini`
   can point it at the `mysql` Docker service instead of `localhost`.

`site/lib/composer.json` had `mailgun/mailgun-php` added (it was installed
globally on the production server, not in the local vendor directory).
