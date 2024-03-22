<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;
use OGame\Services\PlayerService;
use Auth;

class OptionsController extends Controller
{
  use IngameTrait;

  /**
   * Shows the overview index page
   *
   * @param  int  $id
   * @return Response
   */
  public function index(Request $request, PlayerService $player)
  {
    return view('ingame.options.index')->with([
      'username' => $player->getUsername(),
      'current_email' => $player->getEmail(),
      'body_id' => 'preferences', // Set <body> tag ID attribute.
    ]);
  }

  /**
   * Save handler for index() form.
   *
   * @param \Illuminate\Http\Request $request
   */
  public function save(Request $request, PlayerService $player) {
    // Define change handlers.
    $change_handlers = [
      'processChangeUsername'
    ];

    // Loop through change handlers, execute them and if it triggers
    // return its message.
    foreach ($change_handlers as $method) {
      $change_handler = $this->{$method}($request, $player);
      if ($change_handler) {
        if (!empty($change_handler['success_logout'])) {
          return redirect()->route('options.index')->with('success_logout', $change_handler['success_logout']);
        }
        elseif (!empty($change_handler['success'])) {
          return redirect()->route('options.index')->with('success', $change_handler['success']);
        }
        elseif (!empty($change_handler['error'])) {
          return redirect()->route('options.index')->with('error', $change_handler['error']);
        }
      }
    }

    // No actual change has been detected, return to index page.
    return redirect()->route('options.index');
  }

  /**
   * Process change username submit request.
   *
   * @param \Illuminate\Http\Request $request
   * @param \OGame\Services\PlayerService $player
   *
   * @return \Illuminate\Http\RedirectResponse
   */
  public function processChangeUsername(Request $request, PlayerService $player) {
    $name = $request->input('new_username_username');
    $password = $request->input('new_username_password');
    if (!empty($name)) {
      // Check if password matches.
      if (!$player->validatePassword($password)) {
        return array('error' => 'Wrong password!');
      }

      // Check if username validates.
      if (!$player->validateUsername($name)) {
        return array('error' => 'Illegal characters in username!');
      }

      // Update username
      $player->setUsername($name);
      $player->save();

      return array('success_logout' => 'Settings saved');
    }

    return false;
  }


}
