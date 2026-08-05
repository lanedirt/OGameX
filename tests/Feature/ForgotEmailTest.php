<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use OGame\Mail\RetrieveEmailMail;
use OGame\Models\User;
use Tests\AccountTestCase;

/**
 * Test the forgot-email (email retrieval by username) flow.
 */
class ForgotEmailTest extends AccountTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The 'forgot-email' throttle is active during tests. Because the test
        // cache store is shared across test methods, reset it here so every test
        // starts from a clean rate-limit counter and stays hermetic.
        Cache::flush();
    }

    /**
     * Test that the forgot-email form renders for guests.
     */
    public function testForgotEmailPageLoads(): void
    {
        $this->post('/logout');

        $response = $this->get('/forgot-email');
        $response->assertStatus(200);
    }

    /**
     * Test that submitting an existing username sends the retrieval mail.
     */
    public function testEmailSentForExistingUsername(): void
    {
        $this->post('/logout');
        Mail::fake();

        $user = User::findOrFail($this->currentUserId);

        $response = $this->post('/forgot-email', [
            'username' => $user->username,
        ]);

        $response->assertRedirect(route('password.email-lookup'));
        $response->assertSessionHas('status');

        Mail::assertSent(RetrieveEmailMail::class, function (RetrieveEmailMail $mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->username === $user->username;
        });
    }

    /**
     * Test that submitting a non-existent username does NOT send a mail
     * but still returns the same success redirect (no username enumeration).
     */
    public function testEmailNotSentForUnknownUsername(): void
    {
        $this->post('/logout');
        Mail::fake();

        $response = $this->post('/forgot-email', [
            'username' => 'nonexistent-user-' . uniqid(),
        ]);

        $response->assertRedirect(route('password.email-lookup'));
        $response->assertSessionHas('status');

        Mail::assertNotSent(RetrieveEmailMail::class);
    }

    /**
     * Test that the masked email is correct (first 2 chars visible, rest masked).
     */
    public function testMaskedEmailInMailContent(): void
    {
        $this->post('/logout');
        Mail::fake();

        $user = User::findOrFail($this->currentUserId);

        $this->post('/forgot-email', [
            'username' => $user->username,
        ]);

        Mail::assertSent(RetrieveEmailMail::class, function (RetrieveEmailMail $mail) use ($user) {
            // The masked email should start with the first 2 chars of the local part.
            [$local, $domain] = explode('@', $user->email, 2);
            $expectedPrefix = mb_substr($local, 0, min(2, mb_strlen($local)));

            return str_starts_with($mail->maskedEmail, $expectedPrefix)
                && str_contains($mail->maskedEmail, '@' . $domain)
                && str_contains($mail->maskedEmail, '***');
        });
    }

    /**
     * Test that submitting without a username returns a validation error.
     */
    public function testValidationRequiresUsername(): void
    {
        $this->post('/logout');

        $response = $this->post('/forgot-email', [
            'username' => '',
        ]);

        $response->assertSessionHasErrors('username');
    }

    /**
     * Test that the forgot-email endpoint is rate limited to 5 requests per minute.
     */
    public function testForgotEmailRequestsAreRateLimited(): void
    {
        $this->post('/logout');
        Mail::fake();

        $user = User::findOrFail($this->currentUserId);

        // The first five requests within the same minute are allowed.
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/forgot-email', [
                'username' => $user->username,
            ]);
            $response->assertRedirect();
        }

        // The sixth request is blocked by the throttle middleware.
        $response = $this->post('/forgot-email', [
            'username' => $user->username,
        ]);
        $response->assertStatus(429);
    }
}
