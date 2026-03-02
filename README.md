<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# ScholarsToday — IELTS Reading REST API

Backend untuk study case latihan soal IELTS Reading (Test → Passage → Question → Option) + submission jawaban user (Attempt → Answer) dan perhitungan skor.

## Tech Stack
- Laravel (REST API)
- Laravel Sanctum (token-based authentication)
- Spatie Laravel Permission (RBAC: admin/user)

## Quick Start (Local)
1. Install dependency: `composer install`
2. Setup env: copy `.env.example` → `.env`
3. Generate key: `php artisan key:generate`
4. Migrate + seed: `php artisan migrate:fresh --seed`
5. Run server: `php artisan serve`

Seeded accounts (password default dari `UserFactory`: `password`):
- User: `test@example.com`
- Admin: `admin@example.com`

## Base URL
- `http://127.0.0.1:8000`
- Semua endpoint API berada di prefix: `/api`

## API Endpoints (Ringkas)
Auth:
- `POST /api/register`
- `POST /api/login`
- `POST /api/logout` (auth)

Tests:
- `GET /api/tests` (auth)
- `GET /api/tests/{{test_id}}` (auth)
- `POST /api/tests` (admin)
- `PUT/PATCH /api/tests/{{test_id}}` (admin)
- `DELETE /api/tests/{{test_id}}` (admin)

Passages (nested + shallow):
- `GET /api/tests/{{test_id}}/passages` (auth)
- `POST /api/tests/{{test_id}}/passages` (admin)
- `GET /api/passages/{{passage_id}}` (auth)
- `PUT/PATCH /api/passages/{{passage_id}}` (admin)
- `DELETE /api/passages/{{passage_id}}` (admin)

Questions (nested + shallow):
- `GET /api/passages/{{passage_id}}/questions` (auth)
- `POST /api/passages/{{passage_id}}/questions` (admin)
- `GET /api/questions/{{question_id}}` (auth)
- `PUT/PATCH /api/questions/{{question_id}}` (admin)
- `DELETE /api/questions/{{question_id}}` (admin)

Options (nested + shallow):
- `GET /api/questions/{{question_id}}/options` (auth)
- `POST /api/questions/{{question_id}}/options` (admin)
- `GET /api/options/{{option_id}}` (auth)
- `PUT/PATCH /api/options/{{option_id}}` (admin)
- `DELETE /api/options/{{option_id}}` (admin)

Attempts:
- `GET /api/attempts` (auth; user hanya milik sendiri, admin lihat semua)
- `POST /api/attempts` (auth)
- `GET /api/attempts/{{attempt_id}}` (auth; ownership)
- `PUT/PATCH /api/attempts/{{attempt_id}}` (auth; ownership)
- `DELETE /api/attempts/{{attempt_id}}` (auth; ownership)

Answers:
- `GET /api/attempts/{{attempt_id}}/answers` (auth; ownership)
- `POST /api/attempts/{{attempt_id}}/answers` (auth; ownership)
- `GET /api/answers/{{answer_id}}` (auth; ownership)
- `PUT/PATCH /api/answers/{{answer_id}}` (auth; ownership)
- `DELETE /api/answers/{{answer_id}}` (auth; ownership)

Submit (scoring):
- `POST /api/tests/{{test_id}}/submit` (auth) → simpan `user_attempts` + `user_answers` dan return `total_score`

## Postman Notes
- Karena routing pakai `shallow()`, update/delete passage **bukan** `PUT /api/tests/{{test_id}}/passages/{{passage_id}}`, tapi `PUT /api/passages/{{passage_id}}`.

dokumentasi dari seluruh endpoint ada di tools posman link di bawah ini : 
https://www.postman.com/maintenance-cosmologist-75316120/workspace/intern


## Penggunaan AI & Prompt Log 
Dalam pengerjaan project ini saya menggunakan bantuan AI (mis. ChatGPT). Sesuai requirement assessment, **seluruh prompt yang digunakan wajib dicantumkan lengkap** dalam dokumentasi.

- Prompt log disimpan di: `docs/AI_PROMPTS.md`


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
