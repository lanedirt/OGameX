<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use OGame\Mail\RetrieveEmailMail;
use OGame\Models\User;

class ForgotEmailController extends Controller
{
    public function show(): View
    {
        return view('outgame.forgot-email');
    }

    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
        ]);

        // Always show a success message regardless of whether the username exists,
        // to avoid leaking which usernames are registered.
        $user = User::where('username', $request->username)->first();

        if ($user !== null) {
            $maskedEmail = $this->maskEmail($user->email);
            Mail::to($user->email)->send(new RetrieveEmailMail(
                maskedEmail: $maskedEmail,
                username: $user->username,
                loginUrl: route('login'),
            ));
        }

        return redirect()->route('password.email-lookup')
            ->with('status', __('t_external.forgot_email.sent'));
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $visibleLocal = mb_substr($local, 0, min(2, mb_strlen($local)));
        $masked = $visibleLocal . str_repeat('*', max(0, mb_strlen($local) - 2));

        return $masked . '@' . $domain;
    }
}
