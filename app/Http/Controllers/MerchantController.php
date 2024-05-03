<?php

namespace OGame\Http\Controllers;

use Illuminate\View\View;

class MerchantController extends OGameController
{
    /**
     * Shows the merchant index page
     *
     * @return View
     */
    public function index(): View
    {
        $this->setBodyId('traderOverview');

        return view('ingame.merchant.index');
    }
}
