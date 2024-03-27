<?php

namespace OGame\Services;

use Auth;
use Exception;
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
    public function __construct()
    {
        $this->objects = resolve('OGame\Services\ObjectService');
    }

    /**
     * Load player object by user ID.
     */
    public function load($id)
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
        $this->user_tech = $tech;

        // Fetch all planets of user
        $planet_list_service = resolve('OGame\Services\PlanetListService');
        $planet_list = new $planet_list_service($this);
        $planet_list->load($this->getId());
        $this->planets = $planet_list;
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
    public function save()
    {
        $this->user->save();
    }

    /**
     * Set username property.
     *
     * @param $username
     */
    public function setUsername($username)
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
    public function validateUsername($username)
    {
        return preg_match('/^[A-Za-z][A-Za-z0-9\s]*(?:_[A-Za-z0-9\s]+)*$/', $username);
    }

    /**
     * Get the users username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->user->username;
    }

    /**
     * Set email address.
     */
    public function setEmail($email)
    {
        $this->user->email = $email;
    }

    /**
     * Validates whether input matches current users password.
     */
    public function validatePassword($password)
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
     * Gets the level of a building on this planet.
     */
    public function getResearchLevel($object_id)
    {
        $research = $this->objects->getResearchObjects($object_id);

        // Sanity check: if building does not exist yet then return 0.
        // @TODO: remove when all buildings have been included.
        if (empty($research)) {
            return 0;
        }

        $research_level = $this->user_tech->{$research['machine_name']};

        if ($research_level) {
            return $research_level;
        } else {
            return 0;
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
            $this->user_tech->{$building['machine_name']} = $item->object_level_target;
            $this->user_tech->save();

            // Build the next item in queue (if there is any)
            $queue->start($this, $item->time_end);
        }

        // ------
        // 2. Update last_ip and time properties.
        // ------
        $this->user->time = time();
        $this->user->last_ip = request()->ip();
        $this->user->save();
    }
}
