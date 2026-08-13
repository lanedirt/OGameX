<?php

namespace Modules\HelloWorld\Http\Controllers;

use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;
use OGame\Services\SettingsService;

class HelloWorldController extends OGameController
{
    public function index(SettingsService $settings): View
    {
        $this->setBodyId('overview');

        return view('helloworld::index', [
            'greeting' => $settings->module('helloworld')->string('greeting', 'Hello from the OGameX HelloWorld module!'),
            'moduleName' => config('helloworld.name', 'HelloWorld'),
        ]);
    }
}
