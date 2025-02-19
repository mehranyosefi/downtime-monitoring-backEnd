<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return match (true) {
                //TODO change url on product mode
                $user instanceof User => 'http://localhost:3000/reset-password' . '?token=' . $token . '&email=' . urlencode($user->email),
                    // other user types
                default => throw new \Exception("Invalid user type"),
            };
        });
    }
}
