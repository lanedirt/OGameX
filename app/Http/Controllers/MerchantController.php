<?php

namespace OGame\Http\Controllers;

use Illuminate\Http\Request;
use OGame\Http\Traits\IngameTrait;


class MerchantController extends Controller
{
    use IngameTrait;

    /**
     * Shows the facilities index page
     *
     * @param int $id
     * @return Response
     */
    public function index(Request $request)
    {
        $this->body_id = 'traderOverview';

        return view('ingame.merchant.index')->with([
            'body_id' => $this->body_id,
        ]);
    }
}
