<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;

class ChangeNickController extends Controller
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
    //var_dump($request->getClientIps());
    return view('ingame.changenick.overlay');
  }
}
