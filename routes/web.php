<?php

use Illuminate\Support\Facades\Route;
use OGame\Http\Controllers\Admin\DeveloperShortcutsController;
use OGame\Http\Controllers\Admin\ServerSettingsController as AdminServerSettingsController;
use OGame\Http\Controllers\AllianceController;
use OGame\Http\Controllers\BuddiesController;
use OGame\Http\Controllers\ChangeNickController;
use OGame\Http\Controllers\DefenseController;
use OGame\Http\Controllers\FacilitiesController;
use OGame\Http\Controllers\FleetController;
use OGame\Http\Controllers\FleetEventsController;
use OGame\Http\Controllers\GalaxyController;
use OGame\Http\Controllers\HighscoreController;
use OGame\Http\Controllers\LanguageController;
use OGame\Http\Controllers\MerchantController;
use OGame\Http\Controllers\MessagesController;
use OGame\Http\Controllers\NotesController;
use OGame\Http\Controllers\OptionsController;
use OGame\Http\Controllers\OverviewController;
use OGame\Http\Controllers\PaymentController;
use OGame\Http\Controllers\PlanetAbandonController;
use OGame\Http\Controllers\PlanetMoveController;
use OGame\Http\Controllers\PremiumController;
use OGame\Http\Controllers\ResearchController;
use OGame\Http\Controllers\ResourcesController;
use OGame\Http\Controllers\RewardsController;
use OGame\Http\Controllers\SearchController;
use OGame\Http\Controllers\ServerSettingsController;
use OGame\Http\Controllers\ShipyardController;
use OGame\Http\Controllers\ShopController;
use OGame\Http\Controllers\TechtreeController;

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
Route::middleware(['auth', 'globalgame', 'locale'])->group(function () {
    // Overview
    Route::get('/overview', [OverviewController::class, 'index'])->name('overview.index');

    // Resources
    Route::get('/resources', [ResourcesController::class, 'index'])->name('resources.index');
    Route::get('/resources/settings', [ResourcesController::class, 'settings'])->name('resources.settings');
    Route::post('/resources/settings', [ResourcesController::class, 'settingsUpdate'])->name('resources.settingsUpdate');
    Route::get('/ajax/resources', [ResourcesController::class, 'ajax'])->name('resources.ajax');
    Route::get('/resources/add-buildrequest', [ResourcesController::class, 'addBuildRequest'])->name('resources.addbuildrequest');
    Route::post('/resources/add-buildrequest', [ResourcesController::class, 'addBuildRequest'])->name('resources.addbuildrequest.post');
    Route::post('/resources/cancel-buildrequest', [ResourcesController::class, 'cancelBuildRequest'])->name('resources.cancelbuildrequest');

    // Facilities
    Route::get('/facilities', [FacilitiesController::class, 'index'])->name('facilities.index');
    Route::get('/ajax/facilities', [FacilitiesController::class, 'ajax'])->name('facilities.ajax');
    Route::get('/facilities/add-buildrequest', [FacilitiesController::class, 'addBuildRequest'])->name('facilities.addbuildrequest');
    Route::post('/facilities/add-buildrequest', [FacilitiesController::class, 'addBuildRequest'])->name('facilities.addbuildrequest.get');
    Route::post('/facilities/cancel-buildrequest', [FacilitiesController::class, 'cancelBuildRequest'])->name('facilities.cancelbuildrequest');

    // Research
    Route::get('/research', [ResearchController::class, 'index'])->name('research.index');
    Route::get('/ajax/research', [ResearchController::class, 'ajax'])->name('research.ajax');
    Route::get('/research/add-buildrequest', [ResearchController::class, 'addBuildRequest'])->name('research.addbuildrequest');
    Route::post('/research/add-buildrequest', [ResearchController::class, 'addBuildRequest'])->name('research.addbuildrequest.post');
    Route::post('/research/cancel-buildrequest', [ResearchController::class, 'cancelBuildRequest'])->name('research.cancelbuildrequest');

    // Shipyard
    Route::get('/shipyard', [ShipyardController::class, 'index'])->name('shipyard.index');
    Route::get('/ajax/shipyard', [ShipyardController::class, 'ajax'])->name('shipyard.ajax');
    Route::post('/shipyard/add-buildrequest', [ShipyardController::class, 'addBuildRequest'])->name('shipyard.addbuildrequest');

    // Defense
    Route::get('/defense', [DefenseController::class, 'index'])->name('defense.index');
    Route::get('/ajax/defense', [DefenseController::class, 'ajax'])->name('defense.ajax');
    Route::post('/defense/add-buildrequest', [DefenseController::class, 'addBuildRequest'])->name('defense.addbuildrequest');

    // Techtree
    Route::get('/ajax/techtree', [TechtreeController::class, 'ajax'])->name('techtree.ajax');

    // Fleet
    Route::get('/fleet', [FleetController::class, 'index'])->name('fleet.index');
    Route::get('/fleet/movement', [FleetController::class, 'movement'])->name('fleet.movement');

    Route::post('/ajax/fleet/dispatch/check-target', [FleetController::class, 'dispatchCheckTarget'])->name('fleet.dispatch.checktarget');
    Route::post('/ajax/fleet/dispatch/send-fleet', [FleetController::class, 'dispatchSendFleet'])->name('fleet.dispatch.sendfleet');
    Route::post('/ajax/fleet/dispatch/send-mini-fleet', [FleetController::class, 'dispatchSendMiniFleet'])->name('fleet.dispatch.sendminifleet');
    Route::post('/ajax/fleet/dispatch/recall-fleet', [FleetController::class, 'dispatchRecallFleet'])->name('fleet.dispatch.recallfleet');

    Route::get('/ajax/fleet/eventbox/fetch', [FleetEventsController::class, 'fetchEventBox'])->name('fleet.eventbox.fetch');
    Route::get('/ajax/fleet/eventlist/fetch', [FleetEventsController::class, 'fetchEventList'])->name('fleet.eventlist.fetch');

    // Galaxy
    Route::get('/galaxy', [GalaxyController::class, 'index'])->name('galaxy.index');
    Route::post('/ajax/galaxy', [GalaxyController::class, 'ajax'])->name('galaxy.ajax');

    // Messages
    Route::get('/messages', [MessagesController::class, 'index'])->name('messages.index');
    // For handling message delete requests
    Route::post('/messages', [MessagesController::class, 'post'])->name('messages.post');
    // For handling tab change AJAX requests
    Route::get('/ajax/messages', [MessagesController::class, 'ajaxGetTabContents'])->name('messages.ajax.gettabcontents');
    // For handling individual message AJAX requests by ID
    Route::get('/ajax/messages/{messageId}', [MessagesController::class, 'ajaxGetMessage'])->name('messages.ajax.getmessage');

    // Misc
    Route::get('/merchant', [MerchantController::class, 'index'])->name('merchant.index');

    Route::get('/alliance', [AllianceController::class, 'index'])->name('alliance.index');
    Route::get('/ajax/alliance/create', [AllianceController::class, 'ajaxCreate'])->name('alliance.ajax.create');

    Route::get('/premium', [PremiumController::class, 'index'])->name('premium.index');
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

    Route::get('/options', [OptionsController::class, 'index'])->name('options.index');
    Route::post('/options', [OptionsController::class, 'save'])->name('options.save');

    Route::get('/highscore', [HighscoreController::class, 'index'])->name('highscore.index');
    Route::post('/ajax/highscore', [HighscoreController::class, 'ajax'])->name('highscore.ajax');

    Route::get('/buddies', [BuddiesController::class, 'index'])->name('buddies.index');
    Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards.index');
    Route::get('/planet-move', [PlanetMoveController::class, 'index'])->name('planetMove.index');

    Route::get('/overlay/search', [SearchController::class, 'overlay'])->name('search.overlay');

    Route::match(['get', 'post'], '/overlay/notes', [NotesController::class, 'overlay'])->name('notes.overlay');
    Route::get('/overlay/notes/view', [NotesController::class, 'view'])->name('notes.view');
    Route::post('/ajax/notes/create', [NotesController::class, 'ajaxCreate'])->name('notes.ajax.create');

    Route::get('/overlay/planet-abandon', [PlanetAbandonController::class, 'overlay'])->name('planetabandon.overlay');
    Route::post('ajax/planet-abandon/rename', [PlanetAbandonController::class, 'rename'])->name('planetabandon.rename');
    Route::post('ajax/planet-abandon/abandon-confirm', [PlanetAbandonController::class, 'abandonConfirm'])->name('planetabandon.abandon.confirm');
    Route::post('ajax/planet-abandon/abandon', [PlanetAbandonController::class, 'abandon'])->name('planetabandon.abandon');

    Route::get('/overlay/changenick', [ChangeNickController::class, 'overlay'])->name('changenick.overlay');
    Route::post('ajax/changenick/rename', [ChangeNickController::class, 'rename'])->name('changenick.rename');

    Route::get('/overlay/payment', [PaymentController::class, 'overlay'])->name('payment.overlay');
    Route::get('/overlay/payment/iframe', [PaymentController::class, 'iframe'])->name('payment.iframesrc');

    Route::get('/overlay/server-settings', [ServerSettingsController::class, 'overlay'])->name('serversettings.overlay');
    Route::get('/lang/{lang}', [LanguageController::class, 'switchLang'])->name('language.switch');
});

// Group: all logged in pages:
Route::middleware(['auth', 'globalgame', 'locale', 'admin'])->group(function () {
    // Server settings
    Route::get('/admin/server-settings', [AdminServerSettingsController::class, 'index'])->name('admin.serversettings.index');
    Route::post('/admin/server-settings', [AdminServerSettingsController::class, 'update'])->name('admin.serversettings.update');

    // Developer shortcuts
    Route::get('/admin/developer-shortcuts', [DeveloperShortcutsController::class, 'index'])->name('admin.developershortcuts.index');
    Route::post('/admin/developer-shortcuts', [DeveloperShortcutsController::class, 'update'])->name('admin.developershortcuts.update');
    Route::post('/admin/developer-shortcuts/resources', [DeveloperShortcutsController::class, 'updateResources'])->name('admin.developershortcuts.update-resources');
    Route::post('/admin/developershortcuts/create-at-coords', [DeveloperShortcutsController::class, 'createAtCoords'])->name('admin.developershortcuts.create-at-coords');
    Route::post('/admin/developershortcuts/create-debris', [DeveloperShortcutsController::class, 'createDebris'])->name('admin.developershortcuts.create-debris');
});
