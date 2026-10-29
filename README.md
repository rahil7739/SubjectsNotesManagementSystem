<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Study notes app

The app provides separate `web` and `admin` session guards, private note attachments, subject/note management, admin oversight, and soft-deleted records. Public registration only creates regular users. Admin and user sessions can remain active in the same browser; signing out of either guard preserves the other.

1. Configure the database in `.env`, then run `php artisan migrate`.
2. To create an admin during seeding, set `ADMIN_NAME`, `ADMIN_EMAIL`, and `ADMIN_PASSWORD` in `.env`, then run `php artisan db:seed`. Set all three values together. If none are set, the seeder only creates the sample user.
3. Visit `/register` or `/login` for user accounts, and `/admin/login` for administrators.

Users can manage their profile, subjects, notes, and private file attachments from `/dashboard`. Admins can inspect and soft-delete users, and manage their records, from `/admin/dashboard`; deleted users, subjects, and notes can be restored or permanently removed from `/admin/trash`. Self-registered accounts remain pending until an admin approves them in User management. Registration requests notify admins by email and in the notification inbox; approval emails the user. Users created directly by an admin are approved immediately and receive an account email. When an admin assigns a subject or note to a user, the user receives an in-app notification from the header bell. Notifications can be opened or marked as read from the notification inbox.

Configure a working Laravel mail transport in `.env` (the default `MAIL_MAILER=log` writes emails to the application log rather than delivering them) to send these approval and account emails.

### API

Set a long, random `API_SHARED_KEY` in `.env` (for example, generate one with `php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"`). Do not commit it or expose it in browser-side code. After changing it, run `php artisan config:clear`. Every `/api` request requires it in the `X-API-Key` header. Authentication endpoints issue hashed, expiring bearer tokens; pass the returned token in `Authorization: Bearer <token>`. Revoke it with `POST /api/auth/logout`.

The API includes `/api/auth/register`, `/api/auth/login`, `/api/auth/profile`, `/api/subjects`, `/api/notes`, and `/api/attachments/{id}`. Admin-only endpoints are under `/api/admin` and provide statistics, user detail, deleted record restore, and permanent deletion. Web and API users can only access their own records; admins can manage any user's records. Uploads accept PDF, DOC/DOCX, TXT, JPG/JPEG, and PNG files up to 10 MB each (five per request).

Passwords are hashed by Laravel, web login attempts are rate-limited, and protected pages/API routes enforce both authentication and role authorization.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
