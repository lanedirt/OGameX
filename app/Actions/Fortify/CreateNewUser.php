<?php

namespace OGame\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use OGame\User;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    private $firstNames = [
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

    private $lastNames = [
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
        "Kiviuq", "Ijira", "Tarqeq", "S/2004", "S/2006",
        "Varda", "Quirin", "Sila", "Numis", "Orcus",
        "S/2015", "Rhode", "Helik", "Kalyp", "Kore",
        "Phorc", "Deuc", "Neso", "Bergel", "Sirona",
        "Galax", "Cosmo", "Astro", "Stela", "Nebul",
        "Siriu", "Vega", "Altai", "Denib", "Fomal",
        "Orion", "Lyra", "Cygns", "Aquil", "Draco",
        "Solis", "Lunae", "Terra", "Aeria", "Caeli",
        "Zephy", "Borel", "Eurus", "Notus", "Austro"
    ];


    private function generateName(): string
    {
        $firstName = $this->firstNames[array_rand($this->firstNames)];
        $lastName = $this->lastNames[array_rand($this->lastNames)];
        return $firstName . ' ' . $lastName;
    }

    private function getUniqueSuffix($attempt): int
    {
        // Generate a unique suffix based on the attempt number.
        // This could be a simple numeric increment or a more complex hash.
        return rand(100 * $attempt, 999 * $attempt);
    }

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

        return User::create([
            'lang' => 'en',
            'username' => $this->generateUniqueName(),
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
