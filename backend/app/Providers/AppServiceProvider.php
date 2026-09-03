<?php

namespace App\Providers;

use App\Mail\VerifyEmailMail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;

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
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new VerifyEmailMail($url, $notifiable->name ?? null))
                ->to($notifiable->getEmailForVerification());
        });

        
        LogViewer::auth(function ($request) {            
            return $request->user()?->email === config('app.admin_email') ?? false;
        });
    }
}
