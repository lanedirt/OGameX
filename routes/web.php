<?php

use Illuminate\Support\Facades\Route;
use OGame\Http\Controllers\Admin\DeveloperShortcutsController;
use OGame\Http\Controllers\Admin\RulesController as AdminRulesController;
use OGame\Http\Controllers\Admin\ServerSettingsController as AdminServerSettingsController;
use OGame\Http\Controllers\AllianceController;
use OGame\Http\Controllers\AllianceDepotController;
use OGame\Http\Controllers\BuddiesController;
use OGame\Http\Controllers\ChangeNickController;
use OGame\Http\Controllers\CharacterClassController;
use OGame\Http\Controllers\DefenseController;
use OGame\Http\Controllers\FacilitiesController;
use OGame\Http\Controllers\FleetController;
use OGame\Http\Controllers\FleetEventsController;
use OGame\Http\Controllers\GalaxyController;
use OGame\Http\Controllers\HighscoreController;
use OGame\Http\Controllers\JumpGateController;
use OGame\Http\Controllers\LanguageController;
use OGame\Http\Controllers\MerchantController;
use OGame\Http\Controllers\MessagesController;
use OGame\Http\Controllers\NotesController;
use OGame\Http\Controllers\OptionsController;
use OGame\Http\Controllers\OverviewController;
use OGame\Http\Controllers\PaymentController;
use OGame\Http\Controllers\PhalanxController;
use OGame\Http\Controllers\PlanetAbandonController;
use OGame\Http\Controllers\PlanetMoveController;
use OGame\Http\Controllers\PremiumController;
use OGame\Http\Controllers\ResearchController;
use OGame\Http\Controllers\ResourcesController;
use OGame\Http\Controllers\RewardsController;
use OGame\Http\Controllers\RulesController;
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

// Public AJAX endpoints (no auth required).
Route::get('/ajax/main/rules', [RulesController::class, 'ajaxRules'])->name('rules.ajax');
Route::get('/ajax/main/legal', [RulesController::class, 'ajaxLegal'])->name('legal.ajax');

// Group: all logged in pages:
Route::middleware(['auth', 'globalgame', 'locale', 'firstlogin'])->group(function () {
    // Overview
    Route::get('/overview', [OverviewController::class, 'index'])->name('overview.index');

    // Resources
    Route::get('/resources', [ResourcesController::class, 'index'])->name('resources.index');
    Route::get('/resources/settings', [ResourcesController::class, 'settings'])->name('resources.settings');
    Route::post('/resources/settings', [ResourcesController::class, 'settingsUpdate'])->name('resources.settingsUpdate');
    Route::get('/ajax/resources', [ResourcesController::class, 'ajax'])->name('resources.ajax');
    Route::get('/resources/add-buildrequest', [ResourcesController::class, 'addBuildRequest'])->name('resources.addbuildrequest');
    Route::post('/resources/add-buildrequest', [ResourcesController::class, 'addBuildRequest'])->name('resources.addbuildrequest.post');
    Route::post('/resources/downgrade', [ResourcesController::class, 'downgradeBuildRequest'])->name('resources.downgrade');
    Route::post('/resources/cancel-buildrequest', [ResourcesController::class, 'cancelBuildRequest'])->name('resources.cancelbuildrequest');

    // Facilities
    Route::get('/facilities', [FacilitiesController::class, 'index'])->name('facilities.index');
    Route::get('/ajax/facilities', [FacilitiesController::class, 'ajax'])->name('facilities.ajax');
    Route::get('/facilities/add-buildrequest', [FacilitiesController::class, 'addBuildRequest'])->name('facilities.addbuildrequest');
    Route::post('/facilities/add-buildrequest', [FacilitiesController::class, 'addBuildRequest'])->name('facilities.addbuildrequest.post');
    Route::post('/facilities/downgrade', [FacilitiesController::class, 'downgradeBuildRequest'])->name('facilities.downgrade');
    Route::post('/facilities/cancel-buildrequest', [FacilitiesController::class, 'cancelBuildRequest'])->name('facilities.cancelbuildrequest');
    Route::post('/ajax/facilities/halve-building', [FacilitiesController::class, 'halveBuilding'])->name('facilities.halvebuilding');
    Route::post('/ajax/facilities/start-repairs', [FacilitiesController::class, 'startRepairs'])->name('facilities.startrepairs');
    Route::post('/ajax/facilities/complete-repairs', [FacilitiesController::class, 'completeRepairs'])->name('facilities.completerepairs');
    Route::post('/ajax/facilities/burn-wreck-field', [FacilitiesController::class, 'burnWreckField'])->name('facilities.burnwreckfield');
    Route::get('/ajax/facilities/wreck-field-status', [FacilitiesController::class, 'getWreckFieldStatus'])->name('facilities.wreckfieldstatus');
    Route::get('/ajax/facilities/destroy-rockets', [FacilitiesController::class, 'destroyRocketsOverlay'])->name('facilities.destroy-rockets-overlay');
    Route::post('/ajax/facilities/destroy-rockets', [FacilitiesController::class, 'destroyRockets'])->name('facilities.destroy-rockets');

    // Research
    Route::get('/research', [ResearchController::class, 'index'])->name('research.index');
    Route::get('/ajax/research', [ResearchController::class, 'ajax'])->name('research.ajax');
    Route::get('/research/add-buildrequest', [ResearchController::class, 'addBuildRequest'])->name('research.addbuildrequest');
    Route::post('/research/add-buildrequest', [ResearchController::class, 'addBuildRequest'])->name('research.addbuildrequest.post');
    Route::post('/research/cancel-buildrequest', [ResearchController::class, 'cancelBuildRequest'])->name('research.cancelbuildrequest');
    Route::post('/ajax/research/halve-research', [ResearchController::class, 'halveResearch'])->name('research.halveresearch');

    // Shipyard
    Route::get('/shipyard', [ShipyardController::class, 'index'])->name('shipyard.index');
    Route::get('/ajax/shipyard', [ShipyardController::class, 'ajax'])->name('shipyard.ajax');
    Route::post('/shipyard/add-buildrequest', [ShipyardController::class, 'addBuildRequest'])->name('shipyard.addbuildrequest');
    Route::post('/ajax/shipyard/halve-unit', [ShipyardController::class, 'halveUnit'])->name('shipyard.halveunit');

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

    // Fleet Templates (Standard Fleets)
    Route::get('/ajax/fleet/templates', [FleetController::class, 'getTemplates'])->name('fleet.templates.index');
    Route::post('/ajax/fleet/templates', [FleetController::class, 'saveTemplate'])->name('fleet.templates.store');
    Route::delete('/ajax/fleet/templates/{id}', [FleetController::class, 'deleteTemplate'])->name('fleet.templates.delete');

    Route::get('/ajax/fleet/eventbox/fetch', [FleetEventsController::class, 'fetchEventBox'])->name('fleet.eventbox.fetch');
    Route::get('/ajax/fleet/eventlist/fetch', [FleetEventsController::class, 'fetchEventList'])->name('fleet.eventlist.fetch');
    Route::post('/ajax/fleet/eventlist/checkevents', [FleetEventsController::class, 'checkEvents'])->name('fleet.eventlist.checkevents');

    // Galaxy
    Route::get('/galaxy', [GalaxyController::class, 'index'])->name('galaxy.index');
    Route::post('/ajax/galaxy', [GalaxyController::class, 'ajax'])->name('galaxy.ajax');
    Route::get('/overlay/galaxy/missile-attack', [GalaxyController::class, 'missileAttackOverlay'])->name('galaxy.missile-attack.overlay');
    Route::post('/ajax/galaxy/missile-attack', [GalaxyController::class, 'missileAttack'])->name('galaxy.missile-attack');

    // Phalanx
    Route::post('/ajax/phalanx/scan', [PhalanxController::class, 'scan'])->name('phalanx.scan');

    // Jump Gate
    Route::get('/ajax/jumpgate', [JumpGateController::class, 'index'])->name('jumpgate.index');
    Route::post('/ajax/jumpgate/execute', [JumpGateController::class, 'executeJump'])->name('jumpgate.execute');
    Route::post('/ajax/jumpgate/set-default-target', [JumpGateController::class, 'setDefaultTarget'])->name('jumpgate.setdefaulttarget');

    // Alliance Depot
    Route::get('/ajax/alliance-depot', [AllianceDepotController::class, 'index'])->name('alliance-depot.index');
    Route::post('/ajax/alliance-depot/send-supply-rocket', [AllianceDepotController::class, 'sendSupplyRocket'])->name('alliance-depot.send-supply-rocket');

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
    Route::get('/merchant/resource-market', [MerchantController::class, 'resourceMarket'])->name('merchant.resource-market');
    Route::get('/merchant/market/{type}', [MerchantController::class, 'showMarket'])->name('merchant.market');
    Route::post('/merchant/call', [MerchantController::class, 'callMerchant'])->name('merchant.call');
    Route::post('/merchant/trade', [MerchantController::class, 'executeTrade'])->name('merchant.trade');
    Route::post('/merchant/dismiss', [MerchantController::class, 'dismissMerchant'])->name('merchant.dismiss');
    Route::get('/merchant/scrap', [MerchantController::class, 'scrap'])->name('merchant.scrap');
    Route::post('/merchant/scrap/bargain', [MerchantController::class, 'scrapBargain'])->name('merchant.scrap.bargain');
    Route::post('/merchant/scrap/execute', [MerchantController::class, 'scrapExecute'])->name('merchant.scrap.execute');

    Route::get('/alliance', [AllianceController::class, 'index'])->name('alliance.index');
    Route::get('/alliance/apply/{alliance_id}', [AllianceController::class, 'showApplicationForm'])->name('alliance.application.form');
    Route::get('/alliance/info/{alliance_id}', [AllianceController::class, 'info'])->name('alliance.info');
    Route::get('/ajax/alliance/create', [AllianceController::class, 'ajaxCreate'])->name('alliance.ajax.create');
    Route::get('/ajax/alliance/overview', [AllianceController::class, 'ajaxOverview'])->name('alliance.ajax.overview');
    Route::get('/ajax/alliance/management', [AllianceController::class, 'ajaxManagement'])->name('alliance.ajax.management');
    Route::get('/ajax/alliance/broadcast', [AllianceController::class, 'ajaxBroadcast'])->name('alliance.ajax.broadcast');
    Route::get('/ajax/alliance/applications', [AllianceController::class, 'ajaxApplications'])->name('alliance.ajax.applications');
    Route::get('/ajax/alliance/classes', [AllianceController::class, 'ajaxClasses'])->name('alliance.ajax.classes');
    Route::get('/ajax/alliance/new-application', [AllianceController::class, 'ajaxNewApplication'])->name('alliance.ajax.new-application');
    Route::get('/ajax/alliance/handle-application/{alliance_id}', [AllianceController::class, 'ajaxHandleApplication'])->name('alliance.ajax.handleapplication');
    Route::post('/alliance/store', [AllianceController::class, 'store'])->name('alliance.store');
    Route::post('/alliance/apply', [AllianceController::class, 'apply'])->name('alliance.apply');
    Route::post('/alliance/action', [AllianceController::class, 'action'])->name('alliance.action');
    Route::post('/alliance/rank/create', [AllianceController::class, 'createRank'])->name('alliance.rank.create');
    Route::post('/alliance/members/kick', [AllianceController::class, 'kickMemberAction'])->name('alliance.members.kick');
    Route::post('/alliance/members/assign-rank', [AllianceController::class, 'assignRankAction'])->name('alliance.members.assign-rank');
    Route::post('/alliance/text/update', [AllianceController::class, 'updateAllianceText'])->name('alliance.text.update');

    Route::get('/premium', [PremiumController::class, 'index'])->name('premium.index');
    Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');

    // Character Class
    Route::get('/characterclass', [CharacterClassController::class, 'index'])->name('characterclass.index');
    Route::post('/characterclass/select', [CharacterClassController::class, 'selectClass'])->name('characterclass.select');
    Route::post('/characterclass/deselect', [CharacterClassController::class, 'deselectClass'])->name('characterclass.deselect');

    Route::get('/options', [OptionsController::class, 'index'])->name('options.index');
    Route::post('/options', [OptionsController::class, 'save'])->name('options.save');

    Route::get('/highscore', [HighscoreController::class, 'index'])->name('highscore.index');
    Route::post('/ajax/highscore', [HighscoreController::class, 'ajax'])->name('highscore.ajax');

    Route::get('/buddies', [BuddiesController::class, 'index'])->name('buddies.index');
    Route::post('/buddies', [BuddiesController::class, 'post'])->name('buddies.post');
    Route::get('/buddies/request-dialog', [BuddiesController::class, 'showRequestDialog'])->name('buddies.requestdialog');
    Route::post('/buddies/send-request', [BuddiesController::class, 'sendRequest'])->name('buddies.sendrequest');
    Route::post('/buddies/ignore', [BuddiesController::class, 'ignorePlayer'])->name('buddies.ignore');
    Route::post('/buddies/unignore', [BuddiesController::class, 'unignorePlayer'])->name('buddies.unignore');
    Route::get('/buddies/online', [BuddiesController::class, 'getOnlineBuddies'])->name('buddies.online');

    Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards.index');
    Route::get('/planet-move', [PlanetMoveController::class, 'index'])->name('planetMove.index');

    Route::get('/overlay/search', [SearchController::class, 'overlay'])->name('search.overlay');
    Route::post('/ajax/search', [SearchController::class, 'search'])->name('search.ajax');

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

    // Rules
    Route::get('/admin/rules', [AdminRulesController::class, 'index'])->name('admin.rules.index');
    Route::post('/admin/rules', [AdminRulesController::class, 'update'])->name('admin.rules.update');

    // Developer shortcuts
    Route::get('/admin/developer-shortcuts', [DeveloperShortcutsController::class, 'index'])->name('admin.developershortcuts.index');
    Route::post('/admin/developer-shortcuts', [DeveloperShortcutsController::class, 'update'])->name('admin.developershortcuts.update');
    Route::post('/admin/developer-shortcuts/resources', [DeveloperShortcutsController::class, 'updateResources'])->name('admin.developershortcuts.update-resources');
    Route::post('/admin/developershortcuts/create-at-coords', [DeveloperShortcutsController::class, 'createAtCoords'])->name('admin.developershortcuts.create-at-coords');
    Route::post('/admin/developershortcuts/create-debris', [DeveloperShortcutsController::class, 'createDebris'])->name('admin.developershortcuts.create-debris');
    Route::post('/admin/developershortcuts/update-dark-matter', [DeveloperShortcutsController::class, 'updateDarkMatter'])->name('admin.developershortcuts.update-dark-matter');
});
