<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Nwidart\Modules\Facades\Module;
use OGame\Http\Controllers\OGameController;

class ModulesController extends OGameController
{
    /**
     * List all installed modules with their enabled/disabled state.
     */
    public function index(): View
    {
        $modules = [];

        foreach (Module::all() as $module) {
            $providers = $module->get('providers', []);

            $modules[] = [
                'name' => $module->getName(),
                'alias' => $module->getLowerName(),
                'description' => $module->getDescription(),
                'version' => $module->getComposerAttr('version') ?: '—',
                'enabled' => $module->isEnabled(),
                'priority' => (int) $module->getPriority(),
                'providers' => is_array($providers) ? $providers : [],
                'path' => $module->getPath(),
            ];
        }

        // Enabled modules first, then highest priority, then by name.
        usort($modules, static function (array $a, array $b): int {
            if ($a['enabled'] !== $b['enabled']) {
                return $a['enabled'] ? -1 : 1;
            }

            if ($a['priority'] !== $b['priority']) {
                return $b['priority'] <=> $a['priority'];
            }

            return strcasecmp($a['name'], $b['name']);
        });

        $enabledCount = count(array_filter($modules, static fn (array $module): bool => $module['enabled']));
        $disabledCount = count($modules) - $enabledCount;

        return view('ingame.admin.modules', [
            'modules' => $modules,
            'enabledCount' => $enabledCount,
            'disabledCount' => $disabledCount,
        ]);
    }

    /**
     * Toggle a module on or off.
     */
    public function toggle(Request $request): RedirectResponse
    {
        $name = $request->input('module');

        if (!is_string($name) || !Module::has($name)) {
            return redirect()->back()->with('error', 'Invalid module name.');
        }

        $module = Module::findOrFail($name);

        if ($module->isEnabled()) {
            $module->disable();

            return redirect()->route('admin.modules.index')->with('success', 'Module disabled.');
        }

        $module->enable();

        return redirect()->route('admin.modules.index')->with('success', 'Module enabled.');
    }
}
