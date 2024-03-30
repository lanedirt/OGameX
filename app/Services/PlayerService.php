<?php

namespace OGame\Services;

use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Carbon;
use OGame\User;
use OGame\UserTech;

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
    protected User $user;
    /**
     * The user tech object from the model of this player.
     *
     * @var UserTech
     */
    protected UserTech $user_tech;
    /**
     * @var ObjectService
     */
    protected ObjectService $objects;

    /**
     * Player constructor.
     */
    public function __construct($player_id)
    {
        // Load the player object if a positive player ID is given.
        // If no player ID is given then player context will not be available, but this can be fine for unittests.
        if ($player_id != 0) {
            $this->load($player_id);
        }

        $this->objects = resolve('OGame\Services\ObjectService');
    }

    /**
     * Load player object by user ID.
     */
    public function load($id): void
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
            $tech = new UserTech;
            $tech->user_id = $this->getId();
            $tech->save();
        }
        $this->setUserTech($tech);

        // Fetch all planets of user
        $planet_list_service = app()->make(PlanetListService::class, ['player' => $this]);
        $this->planets = $planet_list_service;
    }

    public function setUserTech($userTech): void
    {
        $this->user_tech = $userTech;
    }

    /**
     * Get current player ID.
     */
    public function getId()
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
     * Set username property.
     *
     * @param $username
     */
    public function setUsername($username): void
    {
        if ($this->validateUsername($username)) {
            $this->user->username = $username;
        } else {
            throw new Exception('Illegal characters in username.');
        }
    }

    /**
     * Validates a username.
     */
    public function validateUsername($username): false|int
    {
        return preg_match('/^[A-Za-z][A-Za-z0-9\s]*(?:_[A-Za-z0-9\s]+)*$/', $username);
    }

    /**
     * Get the users username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->user->username;
    }

    /**
     * Set email address.
     */
    public function setEmail($email): void
    {
        $this->user->email = $email;
    }

    /**
     * Validates whether input matches current users password.
     */
    public function validatePassword($password): bool
    {
        if (Auth::Attempt((['email' => $this->getEmail(), 'password' => $password]))) {
            return true;
        }

        return false;
    }

    /**
     * Get email address.
     */
    public function getEmail()
    {
        return $this->user->email;
    }

    /**
     * Gets the level of a research technology for this player.
     */
    public function getResearchLevel($object_id)
    {
        $research = $this->objects->getResearchObjects($object_id);
        $research_level = $this->user_tech->{$research['machine_name']};

        if ($research_level) {
            return $research_level;
        } else {
            return 0;
        }
    }

    /**
     * Set the level of a research technology for this player.
     *
     * @param $object_id
     * @param $level
     * @param $save_to_db
     * @return void
     */
    public function setResearchLevel($object_id, $level, $save_to_db = true): void
    {
        $research = $this->objects->getResearchObjects($object_id);

        // Sanity check: if building does not exist yet then return 0.
        $this->user_tech->{$research['machine_name']} = $level;
        if ($save_to_db) {
            $this->user_tech->save();
        }
    }

    /**
     * Get planet ID that player has currently selected / is looking at.
     */
    public function getCurrentPlanetId()
    {
        return $this->user->planet_current;
    }

    /**
     * Set current planet ID (update).
     */
    public function setCurrentPlanetId($planet_id)
    {
        // Check if user owns this planet ID
        if ($this->planets->planetExistsAndOwnedByPlayer($planet_id)) {
            $this->user->planet_current = $planet_id;
            $this->user->save();
        }
    }

    /**
     * Update the player entity.
     */
    public function update()
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
            $building = $planet->objects->getResearchObjects($item->object_id);

            // Update build queue record
            $item->processed = 1;
            $item->save();

            // Update planet and update level of the building that has been processed.
            $this->setResearchLevel($item->object_id, $item->object_level_target, true);

            // Build the next item in queue (if there is any)
            $queue->start($this, $item->time_end);
        }

        // ------
        // 2. Update last_ip and time properties.
        // ------
        $this->user->time = Carbon::now()->timestamp;
        $this->user->last_ip = request()->ip();
        $this->user->save();
    }

    /**
     * Calculate and return planet score based on levels of buildings and amount of units.
     */
    public function getResearchScore() {
        // For every research in the game, calculate the score based on how much resources it costs to build it.
        // For research it is the sum of resources needed for all levels up to the current level.
        // The score is the sum of all these values.
        $resources_spent = 0;

        // Create object array
        $research_objects = $this->objects->getResearchObjects();
        foreach ($research_objects as $object) {
            for ($i = 1; $i <= $this->getResearchLevel($object['id']); $i++) {
                // Concatenate price which is array of metal, crystal and deuterium.
                $raw_price = $this->objects->getObjectRawPrice($object['id'], $i);
                $resources_spent += $raw_price['metal'] + $raw_price['crystal'] + $raw_price['deuterium'];
            }
        }

        // Divide the score by 1000 to get the amount of points. Floor the result.
        $score = floor($resources_spent / 1000);

        return $score;
    }
}
