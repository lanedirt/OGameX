<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;

class BuddiesController extends Controller
{
  use IngameTrait;

  /**
   * Shows the buddies index page
   *
   * @param  int  $id
   * @return Response
   */
  public function index(Request $request)
  {
      $this->body_id = 'buddies';
      // TODO: create generic return view method which can be used for all pages to return body_id?
      return view('ingame.buddies.index')->with([
          'body_id' => 'buddies', // Sets <body> tag ID property.
      ]);
  }
}
