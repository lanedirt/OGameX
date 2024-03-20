<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;

class PaymentController extends Controller
{
  use IngameTrait;

  /**
   * Shows the payment popup page
   *
   * @param  int  $id
   * @return Response
   */
  public function overlay(Request $request)
  {
    //var_dump($request->getClientIps());
    return view('ingame.payment.overlay');
  }

    /**
     * Shows the payment popup iframe (placeholder).
     *
     * @param  int  $id
     * @return Response
     */
    public function iframe(Request $request)
    {
        // NOTE: this is a placeholder iframe src which is empty for now.
        return view('ingame.payment.iframe');
    }
}
