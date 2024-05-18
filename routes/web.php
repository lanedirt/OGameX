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

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/overview', 301);

// Group: all logged in pages:
Route::middleware(['auth', 'globalgame', 'locale'])->group(function () {
    Route::namespace('OGame\Http\Controllers')->group(function () {
        // Overview
        Route::get('/overview', 'OverviewController@index')->name('overview.index');

        // Resources
        Route::get('/resources', 'ResourcesController@index')->name('resources.index');
        Route::get('/resources/settings', 'ResourcesController@settings')->name('resources.settings');
        Route::post('/resources/settings', 'ResourcesController@settingsUpdate')->name('resources.settingsUpdate');
        Route::get('/ajax/resources', 'ResourcesController@ajax')->name('resources.ajax');
        Route::get('/resources/add-buildrequest', 'ResourcesController@addBuildRequest')->name('resources.addbuildrequest');
        Route::post('/resources/add-buildrequest', 'ResourcesController@addBuildRequest')->name('resources.addbuildrequest.post');
        Route::post('/resources/cancel-buildrequest', 'ResourcesController@cancelBuildRequest')->name('resources.cancelbuildrequest');

        // Facilities
        Route::get('/facilities', 'FacilitiesController@index')->name('facilities.index');
        Route::get('/ajax/facilities', 'FacilitiesController@ajax')->name('facilities.ajax');
        Route::get('/facilities/add-buildrequest', 'FacilitiesController@addBuildRequest')->name('facilities.addbuildrequest');
        Route::post('/facilities/add-buildrequest', 'FacilitiesController@addBuildRequest')->name('facilities.addbuildrequest.get');
        Route::post('/facilities/cancel-buildrequest', 'FacilitiesController@cancelBuildRequest')->name('facilities.cancelbuildrequest');

        // Research
        Route::get('/research', 'ResearchController@index')->name('research.index');
        Route::get('/ajax/research', 'ResearchController@ajax')->name('research.ajax');
        Route::get('/research/add-buildrequest', 'ResearchController@addBuildRequest')->name('research.addbuildrequest');
        Route::post('/research/add-buildrequest', 'ResearchController@addBuildRequest')->name('research.addbuildrequest.post');
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
        Route::post('/ajax/fleet/dispatch/check-target', 'FleetController@dispatchCheckTarget')->name('fleet.dispatch.checktarget');
        Route::post('/ajax/fleet/dispatch/send-fleet', 'FleetController@dispatchSendFleet')->name('fleet.dispatch.sendfleet');
        Route::post('/ajax/fleet/dispatch/recall-fleet', 'FleetController@dispatchRecallFleet')->name('fleet.dispatch.recallfleet');

        Route::get('/fleet/movement', 'FleetController@movement')->name('fleet.movement');
        Route::get('/ajax/fleet/eventbox/fetch', 'FleetEventsController@fetchEventBox')->name('fleet.eventbox.fetch');
        Route::get('/ajax/fleet/eventlist/fetch', 'FleetEventsController@fetchEventList')->name('fleet.eventlist.fetch');

        // Galaxy
        Route::get('/galaxy', 'GalaxyController@index')->name('galaxy.index');
        Route::post('/ajax/galaxy', 'GalaxyController@ajax')->name('galaxy.ajax');

        // Messages
        Route::get('/messages', 'MessagesController@index')->name('messages.index');
        // For handling message delete requests
        Route::post('/messages', 'MessagesController@post')->name('messages.post');
        // For handling tab change AJAX requests
        Route::get('/ajax/messages', 'MessagesController@ajax')->name('messages.ajax');

        // Misc
        Route::get('/merchant', 'MerchantController@index')->name('merchant.index');

        Route::get('/alliance', 'AllianceController@index')->name('alliance.index');
        Route::get('/ajax/alliance/create', 'AllianceController@ajaxCreate')->name('alliance.ajax.create');

        Route::get('/premium', 'PremiumController@index')->name('premium.index');
        Route::get('/shop', 'ShopController@index')->name('shop.index');

        Route::get('/options', 'OptionsController@index')->name('options.index');
        Route::post('/options', 'OptionsController@save')->name('options.save');

        Route::get('/highscore', 'HighscoreController@index')->name('highscore.index');
        Route::post('/ajax/highscore', 'HighscoreController@ajax')->name('highscore.ajax');

        Route::get('/buddies', 'BuddiesController@index')->name('buddies.index');
        Route::get('/rewards', 'RewardsController@index')->name('rewards.index');
        Route::get('/planet-move', 'PlanetMoveController@index')->name('planetMove.index');

        Route::get('/overlay/search', 'SearchController@overlay')->name('search.overlay');
        Route::get('/overlay/notes', 'NotesController@overlay')->name('notes.overlay');

        Route::get('/overlay/planet-abandon', 'PlanetAbandonController@overlay')->name('planetabandon.overlay');
        Route::post('ajax/planet-abandon/rename', 'PlanetAbandonController@rename')->name('planetabandon.rename');
        Route::post('ajax/planet-abandon/abandon-confirm', 'PlanetAbandonController@abandonConfirm')->name('planetabandon.abandon.confirm');
        Route::post('ajax/planet-abandon/abandon', 'PlanetAbandonController@abandon')->name('planetabandon.abandon');

        Route::get('/overlay/changenick', 'ChangeNickController@overlay')->name('changenick.overlay');
        Route::get('/overlay/payment', 'PaymentController@overlay')->name('payment.overlay');
        Route::get('/overlay/payment/iframe', 'PaymentController@iframe')->name('payment.iframesrc');

        Route::get('/lang/{lang}', 'LanguageController@switchLang')->name('language.switch');
    });
});

// Group: all logged in pages:
Route::middleware(['auth', 'globalgame', 'locale', 'admin'])->group(function () {
    Route::namespace('OGame\Http\Controllers\Admin')->group(function () {
        // Server settings
        Route::get('/admin/server-settings', 'ServerSettingsController@index')->name('admin.serversettings.index');
        Route::post('/admin/server-settings', 'ServerSettingsController@update')->name('admin.serversettings.update');

        // Developer shortcuts
        Route::get('/admin/developer-shortcuts', 'DeveloperShortcutsController@index')->name('admin.developershortcuts.index');
        Route::post('/admin/developer-shortcuts', 'DeveloperShortcutsController@update')->name('admin.developershortcuts.update');
    });
});
