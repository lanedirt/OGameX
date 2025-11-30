<?php

namespace OGame\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PremiumController extends OGameController
{
    /**
     * Shows the premium/officers index page
     *
     * @return View
     */
    public function index(): View
    {
        $this->setBodyId('premium');

        // Get current user's dark matter balance
        $darkMatter = Auth::user()->dark_matter ?? 0;

        return view('ingame.premium.index', [
            'darkMatter' => $darkMatter,
        ]);
    }
}
