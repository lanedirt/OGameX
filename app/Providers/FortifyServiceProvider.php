<?php

namespace OGame\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use OGame\Actions\Fortify\CreateNewUser;
use OGame\Actions\Fortify\ResetUserPassword;
use OGame\Actions\Fortify\UpdateUserPassword;
use OGame\Actions\Fortify\UpdateUserProfileInformation;
use OGame\Models\User;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(20)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(20)->by($request->session()->get('login.id'));
        });

        Fortify::loginView(function () {
            return view('outgame.login');
        });

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return;
            }

            if ($user->isBanned()) {
                $ban   = $user->currentBan();
                $until = $ban?->banned_until
                    ? $ban->banned_until->format('Y-m-d H:i') . ' UTC'
                    : 'permanently';

                throw ValidationException::withMessages([
                    'email' => ["Your account has been banned: {$ban?->reason}. Expires: {$until}."],
                ]);
            }

            return $user;
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('outgame.forgot-password');
        });

        Fortify::resetPasswordView(function ($request) {
            return view('outgame.reset-password', ['request' => $request]);
        });
    }
}
