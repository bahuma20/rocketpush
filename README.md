# Rocketpush
Push Benachrichtigungen für rocketbeans.tv

Dieses Projekt wurde archiviert, da Push-Benachrichtigungen jetzt direkt auf rocketbeans.tv verfügbar sind.

## Features
- [x] Login mit rocketbeans.tv Account
- [x] Es werden automatisch die Abonnements der rocketbeans.tv Website übernommen
- [x] Push Benachrichtigungen 10 Minuten bevor im Livestream etwas startet
- [ ] Pro Show kann eingestellt werden ob man Live-Streams, Premieren oder Wiederholungen sehen möchte
- [ ] Man kann sich benachrichtigen lassen wenn eine neue Show im Sendeplan aufgetaucht ist

## Kleine Technik-Infos
- Nutzt Web Push und Service-Worker (Funktioniert also auch wenn der Browser nicht offen ist)
- Symfony 5 (PHP-Framework) als Basis für die WebApp
- Bootstrap 4 (Frontend-Framework)
- OPEN SOURCE: https://github.com/bahuma/rocketpush

## Developer Information
### Setup
- Create a MySQL Database
- Copy the file `.env` to `.env.local` and adjust the `APP_ENV`, `APP_SECRET` 
  and `DATABASE_URL` variables
- Create a [Rocketbeans App](https://rocketbeans.tv/accountsettings/apps) and copy the
  client id and client secret into the `.env.local` file
- Run `php bin/console webpush:generate:keys` and store the values in the `.env.local` file
- Point your webserver to the `public directory` (or run `symfony serve`)
- Enable HTTPS for the site, because on HTTP push notifications would not work
