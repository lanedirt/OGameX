<?php

namespace OGame\Services;

use Illuminate\Support\Facades\DB;
use OGame\Http\Middleware\Locale;
use OGame\Models\Planet;

/**
 * Centralizes the logic that retranslates a user's default-named planets / moons
 * when their UI language changes. Shared by LanguageController (pre-login dropdown
 * at /lang/{lang}) and OptionsController (in-game Preferences form).
 *
 * Only planets whose current `name` matches a "default" name in one of the
 * supported locales (Homeworld / Colony / Moon, translated) are touched. Custom
 * names chosen by the player do not match any entry in the lookup table and
 * therefore remain unchanged.
 */
class PlanetNameLocalizationService
{
    /**
     * Translation keys under t_ingame.overview that represent default planet names.
     * The array value is the short "kind" identifier used internally when picking
     * the new-locale translation after a match.
     *
     * @var array<string, string>
     */
    private const DEFAULT_NAME_KEYS = [
        't_ingame.overview.homeworld' => 'homeworld',
        't_ingame.overview.colony' => 'colony',
        't_ingame.overview.moon' => 'moon',
    ];

    /**
     * Hardcoded fallbacks used if a translation is missing for some locale.
     *
     * @var array<string, string>
     */
    private const FALLBACKS = [
        'homeworld' => 'Homeworld',
        'colony' => 'Colony',
        'moon' => 'Moon',
    ];

    /**
     * Retranslate the user's default-named planets/moons into the new locale.
     *
     * @param int $userId
     * @param string $newLocale
     * @return int Number of planets actually renamed.
     */
    public function retranslateDefaultNamesForUser(int $userId, string $newLocale): int
    {
        // Build a lookup: every "default" name in every supported locale → kind.
        // Example: ['Homeworld' => 'homeworld', 'Heimatplanet' => 'homeworld',
        //           'Pianeta Madre' => 'homeworld', 'Colony' => 'colony',
        //           'Moon' => 'moon', 'Mond' => 'moon', 'Luna' => 'moon', ...].
        $defaultNamesToKind = [];
        foreach (Locale::SUPPORTED_LOCALES as $loc) {
            foreach (self::DEFAULT_NAME_KEYS as $transKey => $kind) {
                $translated = trans($transKey, [], $loc);
                if (is_string($translated) && $translated !== '' && $translated !== $transKey) {
                    $defaultNamesToKind[$translated] = $kind;
                }
            }
        }

        if (empty($defaultNamesToKind)) {
            return 0;
        }

        // Resolve the new-locale translations once, with fallbacks.
        $newNames = [];
        foreach (self::DEFAULT_NAME_KEYS as $transKey => $kind) {
            $val = (string) trans($transKey, [], $newLocale);
            if ($val === $transKey || $val === '') {
                $val = self::FALLBACKS[$kind];
            }
            $newNames[$kind] = $val;
        }

        // Fetch only the user's planets/moons whose current name is in the default set.
        // Direct DB updates bypass model events because a name change does not
        // require resource recalculation.
        $renamed = 0;
        Planet::query()
            ->where('user_id', $userId)
            ->whereIn('name', array_keys($defaultNamesToKind))
            ->select('id', 'name')
            ->chunkById(200, function ($planets) use ($defaultNamesToKind, $newNames, &$renamed) {
                foreach ($planets as $planet) {
                    $kind = $defaultNamesToKind[$planet->name] ?? null;
                    if ($kind === null) {
                        continue;
                    }

                    $newName = $newNames[$kind] ?? null;
                    if ($newName === null || $newName === '' || $newName === $planet->name) {
                        continue;
                    }

                    DB::table('planets')->where('id', $planet->id)->update(['name' => $newName]);
                    $renamed++;
                }
            });

        return $renamed;
    }
}
