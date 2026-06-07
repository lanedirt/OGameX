<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use OGame\Http\Middleware\Locale;
use OGame\Services\PlanetNameLocalizationService;
use OGame\Services\PlayerService;

class OptionsController extends OGameController
{
    /**
     * Shows the overview index page
     *
     * @param PlayerService $player
     * @return View
     */
    public function index(PlayerService $player): View
    {
        $this->setBodyId('preferences');

        $canUpdateUsername = true;
        if ($lastChange = $player->getLastUsernameChange()) {
            $canUpdateUsername = $lastChange->addWeek()->isPast();
        }

        return view('ingame.options.index')->with([
            'username' => $player->getUsername(),
            'current_email' => $player->getEmail(),
            'canUpdateUsername' => $canUpdateUsername,
            'player' => $player,
            'espionage_probes_amount' => $player->getEspionageProbesAmount(),
            'supported_languages' => Locale::SUPPORTED_LOCALES,
            'current_language' => $player->getUser()->lang ?: app()->getLocale(),
        ]);
    }

    /**
     * Process change username submit request.
     *
     * @param Request $request
     * @param PlayerService $player
     *
     * @return array<string,string>
     * @throws Exception
     */
    public function processChangeUsername(Request $request, PlayerService $player): array|null
    {
        $name = $request->input('new_username_username');
        if (!empty($name)) {
            // Check if username validates.
            $validationResult = $player->isUsernameValid($name);
            if (!$validationResult['valid']) {
                return array('error' => $validationResult['error']);
            }

            // Update username
            $player->setUsername($name);
            $player->save();

            return array('success' => __('t_ingame.options.msg_settings_saved'));
        }

        return array();
    }

    /**
     * Process vacation mode activation/deactivation request.
     *
     * @param Request $request
     * @param PlayerService $player
     *
     * @return array<string,string>
     */
    public function processVacationMode(Request $request, PlayerService $player): array
    {
        $vacationModeChecked = $request->has('urlaubs_modus');

        // If player is currently in vacation mode
        if ($player->isInVacationMode()) {
            // Player wants to deactivate vacation mode
            if (!$vacationModeChecked) {
                if ($player->canDeactivateVacationMode()) {
                    $player->deactivateVacationMode();
                    return array('success' => __('t_ingame.options.msg_vacation_deactivated'));
                } else {
                    return array('error' => __('t_ingame.options.msg_vacation_min_duration'));
                }
            }
            // If checkbox is still checked while in vacation mode, do nothing
            return array();
        } else {
            // Player is not in vacation mode and wants to activate it
            if ($vacationModeChecked) {
                if ($player->canActivateVacationMode()) {
                    $player->activateVacationMode();
                    return array('success' => __('t_ingame.options.msg_vacation_activated'));
                } else {
                    return array('error' => __('t_ingame.options.msg_vacation_fleets_in_transit'));
                }
            }
        }

        return array();
    }

    /**
     * Process password change request.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return array<string,string>|null
     */
    public function processChangePassword(Request $request, PlayerService $player): array|null
    {
        $currentPassword = $request->input('db_password');

        // Only process if the password section was submitted
        if (empty($currentPassword)) {
            return null;
        }

        $newPassword = $request->input('newpass1');
        $confirmPassword = $request->input('newpass2');

        if (!Hash::check($currentPassword, $player->getUser()->password)) {
            return ['error' => __('t_ingame.options.msg_password_incorrect')];
        }

        if ($newPassword !== $confirmPassword) {
            return ['error' => __('t_ingame.options.msg_password_mismatch')];
        }

        $length = strlen($newPassword);
        if ($length < 4 || $length > 128) {
            return ['error' => __('t_ingame.options.msg_password_length_invalid')];
        }

        $player->getUser()->forceFill(['password' => Hash::make($newPassword)])->save();

        return ['success' => __('t_ingame.options.msg_settings_saved')];
    }

    /**
     * Process espionage probes amount save request.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return array<string,string>|null
     */
    public function processEspionageProbesAmount(Request $request, PlayerService $player): array|null
    {
        // Only process if the field is present in the request
        if (!array_key_exists('espionage_probes_amount', $request->all())) {
            return null;
        }

        $amount = $request->input('espionage_probes_amount');

        // Allow empty string to clear the setting
        if ($amount === '' || $amount === null) {
            $player->setEspionageProbesAmount(null);
            $player->save();
            return array('success' => __('t_ingame.options.msg_settings_saved'));
        }

        // Validate that it's a positive integer
        $amount = (int) $amount;
        if ($amount < 1) {
            return array('error' => __('t_ingame.options.msg_probes_min_one'));
        }

        $player->setEspionageProbesAmount($amount);
        $player->save();

        return array('success' => __('t_ingame.options.msg_settings_saved'));
    }

    /**
     * Process language change request.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return array<string,string>|null
     */
    public function processChangeLanguage(Request $request, PlayerService $player): array|null
    {
        if (!$request->has('language')) {
            return null;
        }

        $requested = (string) $request->input('language');

        // Validate against supported locales; ignore unsupported values silently.
        if (!in_array($requested, Locale::SUPPORTED_LOCALES, true)) {
            return null;
        }

        $user = $player->getUser();

        // Skip work if the value hasn't actually changed.
        if ($user->lang === $requested) {
            return null;
        }

        $user->lang = $requested;
        $user->save();

        // Apply immediately so the redirect response is rendered in the new locale.
        App::setLocale($requested);
        session()->put('locale', $requested);
        session()->save();

        // Auto-translate the user's planets/moons that still carry a "default"
        // name (Homeworld / Colony / Moon in any supported language) into the new
        // locale. Custom names chosen by the player are preserved untouched.
        /** @var PlanetNameLocalizationService $planetNameLocalization */
        $planetNameLocalization = app(PlanetNameLocalizationService::class);
        $planetNameLocalization->retranslateDefaultNamesForUser((int) $user->id, $requested);

        return array('success' => __('t_ingame.options.msg_language_changed'));
    }

    /**
     * Save handler for index() form.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return RedirectResponse
     */
    public function save(Request $request, PlayerService $player): RedirectResponse
    {
        // Define change handlers.
        // NOTE: processChangeLanguage MUST run first. processEspionageProbesAmount
        // returns success on every submit (the field is present in every form post),
        // so anything placed after it would never be reached.
        $change_handlers = [
            'processChangeLanguage',
            'processChangeUsername',
            'processChangePassword',
            'processVacationMode',
            'processEspionageProbesAmount',
        ];

        // Loop through change handlers, execute them and if it triggers
        // return its message.
        foreach ($change_handlers as $method) {
            $change_handler = $this->{$method}($request, $player);
            if ($change_handler) {
                if (!empty($change_handler['success_logout'])) {
                    return redirect()->route('options.index')->with('success_logout', $change_handler['success_logout']);
                }

                if (!empty($change_handler[ 'success'])) {
                    return redirect()->route('options.index')->with('success', $change_handler['success']);
                }

                if (!empty($change_handler[ 'error'])) {
                    return redirect()->route('options.index')->with('error', $change_handler['error']);
                }
            }
        }

        // No actual change has been detected, return to index page.
        return redirect()->route('options.index');
    }
}
