<?php

namespace OGame\Http\Controllers;

use OGame\Http\Traits\IngameTrait;

class OGameController extends Controller
{
    use IngameTrait;

    /**
     * Set body_id attribute for the view which is used to set the <body> tag ID attribute
     * by the view composer.
     *
     * @see \OGame\Http\ViewComposers\IngameMainComposer
     *
     * @param string $bodyId
     * @return void
     */
    protected function setBodyId(string $bodyId): void
    {
        request()->attributes->set('body_id', $bodyId);
    }
}
