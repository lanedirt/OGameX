<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;


class FleetController extends Controller
{
  use IngameTrait;

  /**
   * Shows the fleet index page
   *
   * @param  int  $id
   * @return Response
   */
  public function index(Request $request)
  {
    //var_dump($request->getClientIps());
    return view('ingame.fleet.index');
  }

    /**
     * Shows the fleet movement page
     *
     * @param  int  $id
     * @return Response
     */
    public function movement(Request $request)
    {
        //var_dump($request->getClientIps());
        return view('ingame.fleet.movement');
    }
}
