# DM Pilot

> Instagram automation SaaS platform — bulk direct messaging, autopilot campaigns, scheduled posts, chat bot, RSS autoposting, and more.

**Version:** 5.0.3 &nbsp;|&nbsp; **Framework:** Laravel 7.x &nbsp;|&nbsp; **PHP:** 7.2.5+

---

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Cron Jobs](#cron-jobs)
- [Subscription Packages](#subscription-packages)
- [Payment Gateways](#payment-gateways)
- [Multi-language Support](#multi-language-support)
- [Updating](#updating)
- [Tech Stack](#tech-stack)

---

## Features

| Feature | Description |
|---|---|
| **Bulk Direct Messaging** | Send DMs to your followers, following, or any custom user list |
| **Autopilot** | Trigger automated DM campaigns when users follow/unfollow your account |
| **Direct Messenger** | Real-time inbox to read and reply to Instagram DMs |
| **Scheduled Posts** | Schedule and auto-publish posts, albums, and stories |
| **Chat Bot** | Rule-based Q&A bot that auto-replies to incoming DMs |
| **RSS Autoposter** | Automatically post new content to Instagram from RSS feeds |
| **Media Manager** | Upload and organise media files with per-user storage quotas |
| **User & Message Lists** | Build reusable lists of usernames and message templates |
| **Messages Log** | Full delivery-status log for every outgoing message |
| **Statistics** | Track follower, following, and media counts over time |
| **Proxy Support** | Assign per-account or system-wide SOCKS/HTTP proxies |
| **Admin Panel** | Manage users, packages, payments, proxies, pages, and settings |
| **Social Login** | Allow users to register/log in with social accounts |
| **Localization** | Fully translatable UI |

---

## Requirements

### Server

| Requirement | Minimum |
|---|---|
| PHP | 7.2.5 |
| MySQL | 5.7 / MariaDB 10.2 |
| Web server | Apache or Nginx |

### PHP Extensions

`openssl`, `pdo`, `mbstring`, `xml`, `ctype`, `gd`, `tokenizer`, `json`, `bcmath`, `exif`, `curl`, `fileinfo`, `zip`, `proc_open`

### Directory Permissions

The following directories must be writable (`0777`):

```
storage/
storage/app/
storage/framework/
storage/logs/
bootstrap/cache/
```

---

## Installation

### 1. Upload files

Upload the project files to your web server and point the document root to the `public/` directory.

### 2. Copy the environment file

```bash
cp .env.example .env
```

### 3. Check minimum requirements

Open `https://your-domain.com/min-requirements.php` in a browser to verify PHP version and required extensions before proceeding.

### 4. Run the web installer

Navigate to `https://your-domain.com/install` and follow the steps:

1. **Requirements check** – confirms PHP version, extensions, and directory permissions.
2. **Database setup** – enter your MySQL credentials and application URL.
3. **Administrator setup** – enter your license key and create the admin account.

The installer will automatically:
- Write database credentials to `.env`
- Generate an application key
- Run all database migrations
- Create default subscription packages
- Pre-load message and user list templates

### Nginx configuration

A sample Nginx virtual host is included at `nginx.example.com.conf`. Key points:

- Document root must point to `public/`
- PHP-FPM socket path should match your PHP version (e.g. `php7.2-fpm.sock`)
- Direct access to `app/`, `bootstrap/`, `config/`, `database/`, `resources/`, `routes/`, `storage/`, and `tests/` is blocked

---

## Configuration

All application settings live in `.env`. The most important variables:

```dotenv
APP_NAME='DM Pilot'
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

QUEUE_CONNECTION=database   # Required for background jobs
MAIL_MAILER=smtp            # Configure for email notifications
```

Additional application settings (logo paths, trial limits, message speeds, enabled locales) are controlled in `config/pilot.php`.

---

## Cron Jobs

DM Pilot requires the following HTTP cron endpoints to be called on a schedule. Set them up in your server's crontab or a cron service:

| URL | Recommended frequency | Purpose |
|---|---|---|
| `/cron/queue/autopilot` | Every minute | Process the autopilot message queue |
| `/cron/queue/mail` | Every minute | Process outgoing email notifications |
| `/cron/messages` | Every minute | Send queued direct messages |
| `/cron/posts` | Every minute | Publish scheduled posts |
| `/cron/followers` | Every hour | Sync follower lists |
| `/cron/following` | Every hour | Sync following lists |
| `/cron/expired` | Every day | Mark expired subscriptions |
| `/cron/retry` | Every 15 minutes | Retry failed messages |

Example crontab entry (calls every minute via `curl`):

```cron
* * * * * curl -s https://your-domain.com/cron/messages > /dev/null
```

> All cron routes are rate-limited to 10 requests per minute.

---

## Subscription Packages

Three default packages are created during installation:

| Package | Price | Accounts | Messages/day | Storage | Extra features |
|---|---|---|---|---|---|
| **Starter** | $4.99/mo | 1 | 1,000 | 64 MB | Posts, Lists, Media Manager |
| **Captain** | $14.99/mo | 5 | 10,000 | 512 MB | + Autopilot, Direct Messenger |
| **Jet Pilot** | $29.99/mo | 20 | Unlimited | 1 GB | + Chat Bot, RSS Autoposter |

A **3-day free trial** is available (1 account, 100 messages). All packages and pricing are fully configurable from the admin panel.

---

## Payment Gateways

The following payment gateways are supported out of the box:

- **Stripe**
- **PayPal**
- **Paystack**
- **Instamojo**
- **Yandex Kassa**

Configure gateway credentials in the admin panel under **Settings → Integrations**.

---

## Multi-language Support

The UI ships with translations for:

- 🇬🇧 English (`en`)
- 🇷🇺 Russian (`ru`)
- 🇵🇹 Portuguese (`pt`)
- 🇹🇷 Turkish (`tr`)
- 🇺🇦 Ukrainian (`ua`)

Users can switch language at any time via `/lang/{locale}`. Additional locales can be added in `resources/lang/` and enabled in `config/pilot.php`.

---

## Updating

1. Upload the new application files to your server (do not overwrite `.env` or `storage/`).
2. Navigate to `https://your-domain.com/update`.
3. The update page will check for pending database migrations and apply them automatically.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 7.2.5+, Laravel 7.x |
| Database | MySQL / MariaDB |
| Queue | Laravel Database Queue |
| Async / Realtime | ReactPHP, MQTT (valga/fbns-react) |
| Media processing | FFmpeg (php-ffmpeg) |
| Frontend | jQuery, Bootstrap / Tabler UI, Laravel Mix (webpack) |
| Payments | Omnipay, Stripe SDK, Paystack SDK |
| Social auth | Laravel Socialite |
