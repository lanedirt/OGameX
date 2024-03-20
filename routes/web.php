<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::redirect('/', '/overview', 301);

// Group: all logged in pages:
Route::middleware(['auth', 'globalgame'])->group(function () {
    Route::namespace('OGame\Http\Controllers')->group(function () {
        Route::get('/overview', 'OverviewController@index')->name('overview.index');

        // Resources
        Route::get('/resources', 'ResourcesController@index')->name('resources.index');
        Route::get('/resources/settings', 'ResourcesController@settings')->name('resources.settings');
        Route::post('/resources/settings', 'ResourcesController@settingsUpdate')->name('resources.settingsUpdate');
        Route::get('/ajax/resources', 'ResourcesController@ajax')->name('resources.ajax');
        Route::post('/resources/add-buildrequest', 'ResourcesController@addBuildRequest')->name('resources.addbuildrequest');
        Route::post('/resources/cancel-buildrequest', 'ResourcesController@cancelBuildRequest')->name('resources.cancelbuildrequest');

        // Facilities
        Route::get('/facilities', 'FacilitiesController@index')->name('facilities.index');
        Route::get('/ajax/facilities', 'FacilitiesController@ajax')->name('facilities.ajax');
        Route::post('/facilities/add-buildrequest', 'FacilitiesController@addBuildRequest')->name('facilities.addbuildrequest');
        Route::post('/facilities/cancel-buildrequest', 'FacilitiesController@cancelBuildRequest')->name('facilities.cancelbuildrequest');

        // Research
        Route::get('/research', 'ResearchController@index')->name('research.index');
        Route::get('/ajax/research', 'ResearchController@ajax')->name('research.ajax');
        Route::post('/research/add-buildrequest', 'ResearchController@addBuildRequest')->name('research.addbuildrequest');
        Route::post('/research/cancel-buildrequest', 'ResearchController@cancelBuildRequest')->name('research.cancelbuildrequest');

        // Shipyard
        Route::get('/shipyard', 'ShipyardController@index')->name('shipyard.index');
        Route::get('/ajax/shipyard', 'ShipyardController@ajax')->name('shipyard.ajax');
        Route::post('/shipyard/add-buildrequest', 'ShipyardController@addBuildRequest')->name('shipyard.addbuildrequest');

        // Defense
        Route::get('/defense', 'DefenseController@index')->name('defense.index');
        Route::get('/ajax/defense', 'DefenseController@ajax')->name('defense.ajax');
        Route::post('/defense/add-buildrequest', 'DefenseController@addBuildRequest')->name('defense.addbuildrequest');

        // Techtree
        Route::get('/ajax/techtree', 'TechtreeController@ajax')->name('techtree.ajax');

        // Fleet
        Route::get('/fleet', 'FleetController@index')->name('fleet.index');
        Route::get('/fleet/movement', 'FleetController@movement')->name('fleet.movement');

        // Galaxy
        Route::get('/galaxy', 'GalaxyController@index')->name('galaxy.index');
        Route::post('/ajax/galaxy', 'GalaxyController@ajax')->name('galaxy.ajax');

        // Misc
        Route::get('/merchant', 'MerchantController@index')->name('merchant.index');


        Route::get('/alliance', 'AllianceController@index')->name('alliance.index');
        Route::get('/premium', 'PremiumController@index')->name('premium.index');
        Route::get('/shop', 'ShopController@index')->name('shop.index');

        Route::get('/options', 'OptionsController@index')->name('options.index');
        Route::post('/options', 'OptionsController@save')->name('options.save');

        Route::get('/highscore', 'HighscoreController@index')->name('highscore.index');
        Route::get('/buddies', 'BuddiesController@index')->name('buddies.index');
        Route::get('/messages', 'MessagesController@index')->name('messages.index');
        Route::get('/rewards', 'RewardsController@index')->name('rewards.index');
        Route::get('/planet-move', 'PlanetMoveController@index')->name('planetMove.index');

        Route::get('/overlay/search', 'SearchController@overlay')->name('search.overlay');
        Route::get('/overlay/notes', 'NotesController@overlay')->name('notes.overlay');
        Route::get('/overlay/changenick', 'ChangeNickController@overlay')->name('changenick.overlay');
        Route::get('/overlay/payment', 'PaymentController@overlay')->name('payment.overlay');
        Route::get('/overlay/payment/iframe', 'PaymentController@iframe')->name('payment.iframesrc');

    });
});