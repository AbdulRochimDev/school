# Laravel Providers Registration

Ensure these providers are registered in `config/app.php` if not auto-discovered:

App\Providers\AuthServiceProvider::class,
App\Providers\EventServiceProvider::class,
App\Providers\ActivityLogServiceProvider::class,

Also ensure `App\Models\User` exists and authentication (Sanctum) is configured if you use gated API routes.

