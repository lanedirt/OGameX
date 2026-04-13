<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use OGame\Mail\ResetPasswordMail;
use OGame\Models\User;
use Tests\AccountTestCase;

/**
 * Test the forgot-password (reset link) and reset-password flows.
 */
class ForgotPasswordTest extends AccountTestCase
{
    /**
     * Test that the forgot-password form renders for guests.
     */
    public function testForgotPasswordPageLoads(): void
    {
        $this->post('/logout');

        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    /**
     * Test that requesting a reset link for an existing email sends a mail.
     */
    public function testResetLinkSentForExistingEmail(): void
    {
        $this->post('/logout');
        Mail::fake();

        $user = User::find($this->currentUserId);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertRedirect();

        Mail::assertSent(ResetPasswordMail::class, function (ResetPasswordMail $mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->username === $user->username;
        });
    }

    /**
     * Test that requesting a reset link for a non-existent email does not send a mail
     * but still redirects (no user enumeration).
     */
    public function testResetLinkNotSentForUnknownEmail(): void
    {
        $this->post('/logout');
        Mail::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent-user-' . uniqid() . '@example.com',
        ]);

        $response->assertRedirect();

        Mail::assertNotSent(ResetPasswordMail::class);
    }

    /**
     * Test that the reset-password form renders with a valid token.
     */
    public function testResetPasswordPageLoadsWithToken(): void
    {
        $this->post('/logout');

        $user = User::find($this->currentUserId);
        $token = Password::createToken($user);

        $response = $this->get('/reset-password/' . $token . '?email=' . urlencode($user->email));
        $response->assertStatus(200);
    }

    /**
     * Test that submitting a valid reset completes successfully.
     */
    public function testPasswordCanBeResetWithValidToken(): void
    {
        $this->post('/logout');

        $user = User::find($this->currentUserId);
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertRedirect('/login');
    }

    /**
     * Test that an invalid token is rejected.
     */
    public function testPasswordResetFailsWithInvalidToken(): void
    {
        $this->post('/logout');

        $user = User::find($this->currentUserId);

        $response = $this->post('/reset-password', [
            'token' => 'invalid-token-value',
            'email' => $user->email,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        // Should redirect back with errors (invalid token).
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }
}
