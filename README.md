<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

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

## Installation

Follow these steps to install and set up the project:
1. Install dependencies
```bash
composer install
```

2. Copy .env
```bash
cp .env.example .env
```

3. Generate key
```bash
php artisan key:generate
```

4. Create database 'ssyars_intel' in MySQL

5. Run migrations
```bash
php artisan migrate
```

6. Create storage link
```bash
php artisan storage:link
```

7. Start Laravel
```bash
php artisan serve
```

8. Start Flask API [Camera API](https://github.com/Nfx1z/Py_CamLapor) (in separate terminal)
```bash
python yolo_rtsp_api.py
```

<html>
<h2>Login Default Account</h2>
URL &emsp; &emsp; &emsp; : http://localhost:8000 <br>
Email &emsp; &emsp; &nbsp; : admin@yolo.local <br>
Password &emsp; : admin123
</html>



## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
