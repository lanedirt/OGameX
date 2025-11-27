<?php

namespace OGame\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OGame\Services\JumpGateService;
use OGame\Services\ObjectService;
use OGame\Services\PlayerService;

class JumpGateController extends OGameController
{
    private JumpGateService $jumpGateService;

    public function __construct(JumpGateService $jumpGateService)
    {
        $this->jumpGateService = $jumpGateService;
    }

    /**
     * Get the Jump Gate dialog content (overlay view).
     *
     * @param Request $request
     * @param PlayerService $player
     * @return View
     * @throws Exception
     */
    public function index(Request $request, PlayerService $player): View
    {
        $current_moon = $player->planets->current();

        // Validate current planet is a moon
        if (!$current_moon->isMoon()) {
            return view('ingame.jumpgate.error', [
                'error' => __('Jump Gate can only be used from a moon.'),
            ]);
        }

        // Check if Jump Gate exists
        $jump_gate_level = $current_moon->getObjectLevel('jump_gate');
        if ($jump_gate_level < 1) {
            return view('ingame.jumpgate.error', [
                'error' => __('No Jump Gate built on this moon.'),
            ]);
        }

        // Check cooldown status
        $is_on_cooldown = $this->jumpGateService->isOnCooldown($current_moon);
        $cooldown_remaining = $this->jumpGateService->getRemainingCooldown($current_moon);
        $cooldown_formatted = $this->jumpGateService->formatCooldown($cooldown_remaining);

        // Get available target moons
        $target_moons = $this->jumpGateService->getAllTargetMoons($player, $current_moon);

        // Get eligible targets (not on cooldown)
        $eligible_targets = $this->jumpGateService->getEligibleTargets($player, $current_moon);

        // Get available ships on current moon
        $available_ships = [];
        foreach ($this->jumpGateService->getTransferableShips() as $ship_name) {
            $amount = $current_moon->getObjectAmount($ship_name);
            $ship_object = ObjectService::getObjectByMachineName($ship_name);
            $available_ships[] = [
                'machine_name' => $ship_name,
                'id' => $ship_object->id,
                'title' => $ship_object->title,
                'amount' => $amount,
            ];
        }

        // Get default target
        $default_target = $this->jumpGateService->getDefaultTarget($current_moon, $player);

        // Render the dialog view
        return view('ingame.jumpgate.dialog', [
            'current_moon' => $current_moon,
            'is_on_cooldown' => $is_on_cooldown,
            'cooldown_remaining' => $cooldown_remaining,
            'cooldown_formatted' => $cooldown_formatted,
            'target_moons' => $target_moons,
            'eligible_targets' => $eligible_targets,
            'available_ships' => $available_ships,
            'default_target' => $default_target,
            'jump_gate_level' => $jump_gate_level,
        ]);
    }

    /**
     * Execute a jump between moons.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     * @throws Exception
     */
    public function executeJump(Request $request, PlayerService $player): JsonResponse
    {
        $current_moon = $player->planets->current();

        // Validate current planet is a moon
        if (!$current_moon->isMoon()) {
            throw new Exception(__('Jump Gate can only be used from a moon.'));
        }

        // Check if Jump Gate exists
        $jump_gate_level = $current_moon->getObjectLevel('jump_gate');
        if ($jump_gate_level < 1) {
            throw new Exception(__('No Jump Gate built on this moon.'));
        }

        // Check cooldown status
        if ($this->jumpGateService->isOnCooldown($current_moon)) {
            throw new Exception(__('Jump Gate is not ready yet.'));
        }

        // Validate target moon ID
        $target_moon_id = (int)$request->input('targetMoonId');
        if ($target_moon_id <= 0) {
            throw new Exception(__('You must select a valid target.'));
        }

        // Get target moon (getById only searches player's own planets/moons)
        try {
            $target_moon = $player->planets->getById($target_moon_id);
        } catch (Exception $e) {
            throw new Exception(__('Target moon does not belong to you.'));
        }

        // Validate target is a moon
        if (!$target_moon->isMoon()) {
            throw new Exception(__('Target must be a moon.'));
        }

        // Check if target has Jump Gate
        if ($target_moon->getObjectLevel('jump_gate') < 1) {
            throw new Exception(__('Target moon does not have a Jump Gate.'));
        }

        // Check if target is on cooldown
        if ($this->jumpGateService->isOnCooldown($target_moon)) {
            throw new Exception(__('Target Jump Gate is not ready yet.'));
        }

        // Parse ships from request
        $ships = [];
        $has_ships = false;
        foreach ($this->jumpGateService->getTransferableShips() as $ship_name) {
            $ship_object = ObjectService::getObjectByMachineName($ship_name);
            $input_key = 'ship_' . $ship_object->id;
            $input_value = $request->input($input_key, '');

            // Validate input is numeric (reject values like "1x2", "9999z", etc.)
            if ($input_value !== '' && !is_numeric($input_value)) {
                throw new Exception(__('Invalid ship amount specified.'));
            }

            $amount = (int)$input_value;

            // Ensure amount doesn't exceed available ships
            if ($amount > 0) {
                $available = $current_moon->getObjectAmount($ship_name);
                if ($amount > $available) {
                    $amount = $available;
                }
                if ($amount > 0) {
                    $ships[$ship_name] = $amount;
                    $has_ships = true;
                }
            }
        }

        // Validate at least one ship is selected
        if (!$has_ships) {
            throw new Exception(__('No ships were selected!'));
        }

        // Check for unprocessed arrived fleets (race condition prevention).
        // With queue processing, a battle calculation can take minutes.
        // This check prevents ship transfer bugs during that time.
        if ($this->jumpGateService->hasUnprocessedArrivedFleet($current_moon)) {
            throw new Exception(__('Cannot transfer ships while a fleet mission is being processed.'));
        }
        if ($this->jumpGateService->hasUnprocessedArrivedFleet($target_moon)) {
            throw new Exception(__('Cannot transfer ships to a moon with pending fleet mission.'));
        }

        // Execute the transfer
        if (!$this->jumpGateService->transferShips($current_moon, $target_moon, $ships)) {
            throw new Exception(__('Failed to transfer ships. Please check your ship quantities.'));
        }

        // Set cooldown on both moons
        $this->jumpGateService->setCooldown($current_moon, $target_moon);

        return response()->json([
            'success' => true,
            'message' => __('Ships have been transferred successfully.'),
            'redirect' => route('facilities.index', ['cp' => $target_moon_id]),
        ]);
    }

    /**
     * Set the default Jump Gate target for current moon.
     *
     * @param Request $request
     * @param PlayerService $player
     * @return JsonResponse
     * @throws Exception
     */
    public function setDefaultTarget(Request $request, PlayerService $player): JsonResponse
    {
        $current_moon = $player->planets->current();

        // Validate current planet is a moon
        if (!$current_moon->isMoon()) {
            throw new Exception(__('Jump Gate can only be used from a moon.'));
        }

        // Check if Jump Gate exists
        if ($current_moon->getObjectLevel('jump_gate') < 1) {
            throw new Exception(__('No Jump Gate built on this moon.'));
        }

        // Get target moon ID
        $target_moon_id = $request->input('targetMoonId');

        // Validate target if provided
        if ($target_moon_id !== null && $target_moon_id !== '') {
            $target_moon_id = (int)$target_moon_id;

            // Get target moon (getById only searches player's own planets/moons)
            try {
                $target_moon = $player->planets->getById($target_moon_id);
            } catch (Exception $e) {
                throw new Exception(__('Target moon does not belong to you.'));
            }

            // Validate target is a moon with Jump Gate
            if (!$target_moon->isMoon() || $target_moon->getObjectLevel('jump_gate') < 1) {
                throw new Exception(__('Invalid target moon.'));
            }

            // Cannot set self as default target
            if ($target_moon->getPlanetId() === $current_moon->getPlanetId()) {
                throw new Exception(__('Cannot set current moon as default target.'));
            }
        } else {
            $target_moon_id = null;
        }

        // Set the default target
        $this->jumpGateService->setDefaultTarget($current_moon, $target_moon_id);

        return response()->json([
            'success' => true,
            'message' => __('Default target has been set.'),
        ]);
    }
}
