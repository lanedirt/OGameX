<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\App;
use OGame\Http\Middleware\Locale;
use OGame\Models\User;
use Tests\AccountTestCase;

/**
 * Verify language switching supports French and persists the selection.
 */
class LanguageSwitchTest extends AccountTestCase
{
    /**
     * French must be registered as a supported application locale.
     */
    public function testFrenchIsSupportedLocale(): void
    {
        $this->assertContains('fr', Locale::SUPPORTED_LOCALES);
    }

    /**
     * Switching language updates session, user preference, and French translations.
     */
    public function testSwitchToFrenchPersistsPreference(): void
    {
        $response = $this->get(route('language.switch', ['lang' => 'fr']));
        $response->assertRedirect();

        $this->assertSame('fr', session('locale'));

        $user = User::findOrFail($this->currentUserId);
        $this->assertSame('fr', $user->lang);

        App::setLocale('fr');
        $this->assertSame('Se connecter', __('t_external.login.btn'));
        $this->assertSame('Vue générale', __('t_overview.overview'));
    }

    /**
     * Ingame layout exposes a French language switcher link.
     */
    public function testIngameLayoutShowsFrenchSwitcher(): void
    {
        $response = $this->get('/overview');
        $response->assertStatus(200);
        $response->assertSee(route('language.switch', ['lang' => 'fr']), false);
        $response->assertSee('>FR</a>', false);
    }
}
