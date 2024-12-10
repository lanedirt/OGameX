<?php

namespace OGame\Actions\Fortify;

use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use OGame\Factories\PlanetServiceFactory;
use OGame\Factories\PlayerServiceFactory;
use OGame\Models\User;
use OGame\Models\UserTech;
use OGame\Services\MessageService;
use OGame\Services\SettingsService;
use RuntimeException;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * @var PlayerServiceFactory
     */
    private PlayerServiceFactory $playerServiceFactory;

    /**
     * @var PlanetServiceFactory
     */
    private PlanetServiceFactory $planetServiceFactory;

    /**
     * @var SettingsService
     */
    private SettingsService $settings;

    /**
     * Create a new controller instance.
     *
     * @param PlayerServiceFactory $playerServiceFactory
     * @param PlanetServiceFactory $planetServiceFactory
     * @param SettingsService $settings
     */
    public function __construct(PlayerServiceFactory $playerServiceFactory, PlanetServiceFactory $planetServiceFactory, SettingsService $settings)
    {
        $this->playerServiceFactory = $playerServiceFactory;
        $this->planetServiceFactory = $planetServiceFactory;
        $this->settings = $settings;
    }

    /**
     * The first names to use when generating a unique username.
     *
     * @var array<string>
     */
    private array $firstNames = [
        "President", "Constable", "Commander", "Engineer", "Commodore",
        "Captain", "Czar", "Gamma", "Jarhead", "Technocrat",
        "Viceregent", "Admiral", "Emperor", "Tempus", "Geologist",
        "Chief", "Navigator", "Mariner", "Astro", "Pioneer",
        "Sentinel", "Vanguard", "Starlord", "Quasar", "Zenith",
        "Eclipse", "Nova", "Comet", "Pulsar", "Meteor",
        "Titan", "Orion", "Celestial", "Lunar", "Solar",
        "Photon", "Cosmic", "Nebula", "Stellar", "Voidwalker",
        "Galaxy", "Astrophysicist", "Quantum", "Sovereign", "Majesty",
        "Sentry", "Guardian", "Vortex", "Spectre", "Legend",
        "Chrono", "Mystic", "Paladin", "Arcane", "Sage",
        "Virtuoso", "Maverick", "Prophet", "Strategist", "Tactician",
        "Expeditionary", "Pioneer", "Visionary", "Vanguard", "Crusader",
        "Centurion", "Cosmonaut", "Astronaut", "Navigator", "Pathfinder",
        "Voyager", "Explorer", "Adventurer", "Ranger", "Wanderer",
        "Nomad", "Pilgrim", "Seeker", "Discoverer", "Scout",
        "Frontiersman", "Trailblazer", "Pioneer", "Innovator", "Inventor"
    ];

    /**
     * The last names to use when generating a unique username.
     *
     * @var array|string[]
     */
    private array $lastNames = [
        "Orbit", "Nova", "Quark", "Vega", "Rigel",
        "Io", "Europ", "Titan", "Ganym", "Calyp",
        "Atlas", "Deimos", "Phobos", "Janus", "Ariel",
        "Oberon", "Mir", "Leda", "Helio", "Sol",
        "Luna", "Mars", "Venus", "Earth", "Mer",
        "Jup", "Sat", "Uran", "Nept", "Pluto",
        "Eris", "Makem", "Haume", "Sedna", "Varun",
        "Quaoar", "Ixion", "Orcus", "Triton", "Nix",
        "Hydra", "Charon", "Styx", "Kerber", "Logos",
        "Comet", "Dactyl", "Ida", "Gaspra", "Mathi",
        "Bennu", "Borrel", "Ymir", "Paalia", "Namaka",
        "Haiku", "Fornj", "Surtur", "Thrymr", "Skathi",
        "Tayget", "Elara", "Janus", "Mimas", "Encel",
        "Thalas", "Arche", "Iapet", "Dione", "Rhea",
        "Kiviuq", "Ijira", "Tarqeq",
        "Varda", "Quirin", "Sila", "Numis", "Orcus",
        "Rhode", "Helik", "Kalyp", "Kore",
        "Phorc", "Deuc", "Neso", "Bergel", "Sirona",
        "Galax", "Cosmo", "Astro", "Stela", "Nebul",
        "Siriu", "Vega", "Altai", "Denib", "Fomal",
        "Orion", "Lyra", "Cygns", "Aquil", "Draco",
        "Solis", "Lunae", "Terra", "Aeria", "Caeli",
        "Zephy", "Borel", "Eurus", "Notus", "Austro"
    ];

    /**
     * Generate a random name based on the first and last name arrays.
     *
     * @return string
     */
    private function generateName(): string
    {
        $firstName = $this->firstNames[array_rand($this->firstNames)];
        $lastName = $this->lastNames[array_rand($this->lastNames)];
        return $firstName . ' ' . $lastName;
    }

    /**
     * Generate a unique username suffix based on the attempt number.
     *
     * @param int $attempt
     * @return int
     */
    private function getUniqueSuffix(int $attempt): int
    {
        return rand(100 * $attempt, 999 * $attempt);
    }

    /**
     * Generate a unique username that is not already in use.
     *
     * @return string
     */
    public function generateUniqueName(): string
    {
        $attempt = 0;
        do {
            // Generate a more unique name by adding digits or initials.
            $username = $this->generateName();
            if ($attempt >= 5) {
                // After 5 attempts, start adding numeric values to ensure uniqueness.
                $username .= $this->getUniqueSuffix($attempt);
            }
            $attempt++;
        } while (User::where('username', $username)->exists() && $attempt < 10);

        if ($attempt >= 10) {
            // As a last resort, append a large random number or a timestamp.
            $username .= '_' . time();
        }

        return $username;
    }

    /**
     * Validate and create a newly registered user.
     *
     * @param array<string, string> $input
     * @throws ValidationException
     * @throws Exception
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        // Add try/catch to retry creating user 5 times because exception could be triggered
        // if the username is already taken.
        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                $user = User::create([
                    'lang' => 'en',
                    'username' => $this->generateUniqueName(),
                    'email' => $input['email'],
                    'password' => Hash::make($input['password']),
                ]);

                // Check if the user is the first registered user
                if (User::count() === 1) {
                    $user->assignRole('admin');
                    $user->username = 'Admin';
                    $user->save();
                }

                $this->createInitialGameDataForUser($user);

                return $user;
            } catch (Exception $e) {
                if ($e->getCode() === 23000) {
                    // If the username is already taken, retry creating the user.
                    continue;
                }
                throw $e;
            }
        }

        throw new RuntimeException('Failed to create a unique username after 5 attempts.');
    }

    /**
     * Create initial data for the player such as planets and tech records.
     *
     * @param User $user
     * @throws Exception
     */
    private function createInitialGameDataForUser($user): void
    {
        // Create initial player tech record.
        $tech = new UserTech();
        $tech->user_id = $user->id;
        $tech->save();

        // Create initial planet(s) for the player.
        $playerService = $this->playerServiceFactory->make($user->id);
        $planetNames = ['Homeworld', 'Colony'];
        // The amount of planets to create is defined in the settings and defaults to 1.
        for ($i = 0; $i < $this->settings->registrationPlanetAmount(); $i++) {
            $this->planetServiceFactory->createInitialPlanetForPlayer($playerService, $planetNames[$i === 0 ? 0 : 1]);
        }

        // Send welcome message to player
        $message = new MessageService($playerService);
        $message->sendWelcomeMessage();
    }
}
