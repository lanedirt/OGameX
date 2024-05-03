<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;

class PaymentController extends OGameController
{
    /**
     * Shows the payment popup page
     *
     * @return View
     */
    public function overlay(): View
    {
        return view('ingame.payment.overlay');
    }

    /**
     * Shows the payment popup iframe (placeholder).
     *
     * @return View
     */
    public function iframe(): View
    {
        // NOTE: this is a placeholder iframe src which is empty for now.
        return view('ingame.payment.iframe');
    }
}
