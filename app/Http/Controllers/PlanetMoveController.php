<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;

class PlanetMoveController extends Controller
{
  use IngameTrait;

  /**
   * Shows the notes popup page
   *
   * @param  int  $id
   * @return Response
   */
  public function overlay(Request $request)
  {
      // TODO: add correct template for this page.
      return view('ingame.notes.overlay');
  }
}
