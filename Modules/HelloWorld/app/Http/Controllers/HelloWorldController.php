<?php

namespace Modules\HelloWorld\Http\Controllers;

use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;

class HelloWorldController extends OGameController
{
    public function index(): View
    {
        $this->setBodyId('overview');

        return view('helloworld::index', [
            'greeting' => config('helloworld.greeting', 'Hello from the OGameX HelloWorld module!'),
            'moduleName' => config('helloworld.name', 'HelloWorld'),
        ]);
    }
}
