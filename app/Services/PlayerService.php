<?php

namespace OGame\Services;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use OGame\Models\Resources;
use OGame\Models\User;
use OGame\Models\UserTech;

/**
 * Class PlayerService.
 *
 * Player object.
 *
 * @package OGame\Services
 */
class PlayerService
{
    /**
     * The planetlist object for this player.
     *
     * @var PlanetListService
     */
    public PlanetListService $planets;

    /**
     * The user object from the model of this player.
     *
     * @var User
     */
    private User $user;

    /**
     * The user tech object from the model of this player.
     *
     * @var UserTech
     */
    private UserTech $user_tech;

    /**
     * @var ObjectService
     */
    private ObjectService $objects;

    /**
     * Player constructor.
     *
     * @param int $player_id
     * @param ObjectService $objectService
     */
    public function __construct(int $player_id, ObjectService $objectService)
    {
        // Load the player object if a positive player ID is given.
        // If no player ID is given then player context will not be available, but this can be fine for unittests.
        if ($player_id !== 0) {
            $this->load($player_id);
        }

        $this->objects = $objectService;
    }

    /**
     * Checks if this object is equal to another object.
     *
     * @param PlayerService|null $other
     * @return bool
     */
    public function equals(PlayerService|null $other): bool
    {
        return $other !== null && $this->getId() === $other->getId();
    }

    /**
     * Load player object by user ID.
     *
     * @param int $id
     */
    public function load(int $id): void
    {
        // Fetch user from model
        $user = User::where('id', $id)->first();
        $this->user = $user;

        // Fetch user tech from model
        $tech = $this->user->tech()->first();
        if (!$tech) {
            // User has no tech record, so create one.
            // @TODO: move this logic as well as the planet creation
            // to the user register logic action.
            $tech = new UserTech();
            $tech->user_id = $this->getId();
            $tech->save();
        }
        $this->setUserTech($tech);

        // Fetch all planets of user
        try {
            $planet_list_service = app()->make(PlanetListService::class, ['player' => $this]);
            $this->planets = $planet_list_service;
        } catch (BindingResolutionException $e) {
            throw new \RuntimeException('Class not found: ' . PlanetListService::class);
        }
    }

    /**
     * Checks is the supplied password is valid for this user. This method is used as
     * a security measure for critical operations like abandoning a planet.
     *
     * @param string $password
     * @return bool
     */
    public function isPasswordValid(string $password): bool
    {
        return Auth::attempt(['email' => $this->getEmail(), 'password' => $password]);
    }

    /**
     * Set user tech object.
     *
     * @param UserTech $userTech
     * @return void
     */
    public function setUserTech(UserTech $userTech): void
    {
        $this->user_tech = $userTech;
    }

    /**
     * Get current player ID.
     */
    public function getId(): int
    {
        return $this->user->id;
    }

    /**
     * Saves current player object to DB.
     */
    public function save(): void
    {
        $this->user->save();
    }

    /**
     * Checks if the player is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->user->hasRole('admin');
    }

    /**
     * Set username property.
     *
     * @param string $username
     * @throws Exception
     */
    public function setUsername(string $username): void
    {
        if ($this->validateUsername($username)) {
            $this->user->username = $username;
        } else {
            throw new Exception('Illegal characters in username.');
        }
    }

    /**
     * Validates a username.
     *
     * @param string $username
     * @return false|int
     */
    public function validateUsername(string $username): false|int
    {
        return preg_match('/^[A-Za-z][A-Za-z0-9\s]*(?:_[A-Za-z0-9\s]+)*$/', $username);
    }

    /**
     * Get the user's username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        if ($this->isAdmin()) {
            return '<span class="status_abbr_admin">' . $this->user->username . '</span>';
        }
        return $this->user->username;
    }

    /**
     * Set email address.
     *
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->user->email = $email;
    }

    /**
     * Validates whether input matches current users password.
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        if (Auth::Attempt((['email' => $this->getEmail(), 'password' => $password]))) {
            return true;
        }

        return false;
    }

    /**
     * Get email address.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->user->email;
    }

    /**
     * Gets the level of a research technology for this player.
     *
     * @param string $machine_name
     * @return int
     */
    public function getResearchLevel(string $machine_name): int
    {
        $research = $this->objects->getResearchObjectByMachineName($machine_name);
        $research_level = $this->user_tech->{$research->machine_name};

        if ($research_level) {
            return $research_level;
        } else {
            return 0;
        }
    }

    /**
     * Set the level of a research technology for this player.
     *
     * @param string $machine_name
     * @param int $level
     * @param bool $save_to_db
     * @return void
     */
    public function setResearchLevel(string $machine_name, int $level, bool $save_to_db = true): void
    {
        $research = $this->objects->getResearchObjectByMachineName($machine_name);

        // Sanity check: if building does not exist yet then return 0.
        $this->user_tech->{$research->machine_name} = $level;
        if ($save_to_db) {
            $this->user_tech->save();
        }
    }

    /**
     * Get planet ID that player has currently selected / is looking at.
     *
     * @return int
     */
    public function getCurrentPlanetId(): int
    {
        if (!$this->user->planet_current) {
            // If no current planet is set, return the first planet of the player.
            return $this->planets->first()->getPlanetId();
        }

        return $this->user->planet_current;
    }

    /**
     * Set current planet ID (update).
     *
     * @param int $planet_id
     */
    public function setCurrentPlanetId(int $planet_id): void
    {
        // Check if user owns this planet ID.
        // Planet ID 0 is always valid as that will be updated to the first planet of the player.
        if ($planet_id == 0) {
            $this->user->planet_current = null;
            $this->user->save();
            return;
        } elseif ($this->planets->planetExistsAndOwnedByPlayer($planet_id)) {
            $this->user->planet_current = $planet_id;
            $this->user->save();
        }
    }

    /**
     * Update the player entity.
     *
     * This method is called every time the player logs in.
     * It updates the player's last IP and time properties.
     * It also updates the research queue.
     *
     * @return void
     * @throws Exception
     */
    public function update(): void
    {
        // ------
        // 1. Update research queue
        // ------
        $queue = resolve('OGame\Services\ResearchQueueService');
        $research_queue = $queue->retrieveFinishedForUser($this);

        // @TODO: add DB transaction wrapper
        foreach ($research_queue as $item) {
            $planet = $this->planets->childPlanetById($item->planet_id);

            // Get object information of building.
            $object = $this->objects->getResearchObjectById($item->object_id);

            // Update planet and update level of the building that has been processed.
            $this->setResearchLevel($object->machine_name, $item->object_level_target, true);

            // Update build queue record
            $item->processed = 1;
            $item->save();

            // Build the next item in queue (if there is any)
            $queue->start($this, $item->time_end);
        }

        // ------
        // 2. Update last_ip and time properties.
        // ------
        $this->user->time = (string)Carbon::now()->timestamp;
        $this->user->last_ip = request()->ip();
        $this->user->save();
    }

    /**
     * Calculate and return planet score based on levels of buildings and amount of units.
     *
     * @return int
     */
    public function getResearchScore(): int
    {
        // For every research in the game, calculate the score based on how much resources it costs to build it.
        // For research it is the sum of resources needed for all levels up to the current level.
        // The score is the sum of all these values.
        $resources_spent = new Resources(0, 0, 0, 0);

        // Create object array
        $research_objects = $this->objects->getResearchObjects();
        foreach ($research_objects as $object) {
            for ($i = 1; $i <= $this->getResearchLevel($object->machine_name); $i++) {
                // Concatenate price which is array of metal, crystal and deuterium.
                $raw_price = $this->objects->getObjectRawPrice($object->machine_name, $i);
                $resources_spent->add($raw_price);
            }
        }

        // Divide the score by 1000 to get the amount of points. Floor the result.
        $resources_sum = $resources_spent->metal->get() + $resources_spent->crystal->get() + $resources_spent->deuterium->get();
        return (int)floor($resources_sum / 1000);
    }
}
