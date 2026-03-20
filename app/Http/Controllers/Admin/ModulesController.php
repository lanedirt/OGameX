<?php

namespace OGame\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use OGame\Http\Controllers\OGameController;
use OGame\Modules\ModuleServiceProvider;

class ModulesController extends OGameController
{
    private string $disabledFile;

    public function __construct()
    {
        $this->disabledFile = storage_path('app/modules-disabled.json');
    }

    /**
     * List all discovered modules (from config/modules.php and Composer auto-discovery)
     * with their enabled/disabled state.
     */
    public function index(): View
    {
        $disabled = $this->loadDisabled();
        $modules = [];

        foreach (ModuleServiceProvider::allDiscovered() as $providerClass) {
            /** @var ModuleServiceProvider $provider */
            $provider = new $providerClass(app());
            $manifest = $provider->getModuleManifest();

            $modules[] = [
                'provider'    => $providerClass,
                'id'          => $provider->moduleId(),
                'name'        => $manifest['name'] ?? $provider->moduleId(),
                'description' => $manifest['description'] ?? '',
                'version'     => $manifest['version'] ?? '?',
                'enabled'     => !in_array($providerClass, $disabled),
                'missing'     => false,
            ];
        }

        return view('ingame.admin.modules', ['modules' => $modules]);
    }

    /**
     * Toggle a module on or off.
     * Changes take effect on the next request (no cache:clear needed).
     */
    public function toggle(Request $request): RedirectResponse
    {
        $providerClass = $request->input('provider');

        if (!is_string($providerClass) || !class_exists($providerClass)) {
            return redirect()->back()->with('error', 'Invalid module provider class.');
        }

        $disabled = $this->loadDisabled();

        if (in_array($providerClass, $disabled)) {
            $disabled = array_values(array_filter($disabled, fn ($p) => $p !== $providerClass));
            $message = 'Module enabled. Reload the page to apply.';
        } else {
            $disabled[] = $providerClass;
            $message = 'Module disabled. Reload the page to apply.';
        }

        $this->saveDisabled($disabled);

        return redirect()->route('admin.modules.index')->with('success', $message);
    }

    private function loadDisabled(): array
    {
        if (!file_exists($this->disabledFile)) {
            return [];
        }
        return json_decode((string) file_get_contents($this->disabledFile), true) ?? [];
    }

    private function saveDisabled(array $disabled): void
    {
        if (!is_dir(dirname($this->disabledFile))) {
            mkdir(dirname($this->disabledFile), 0755, true);
        }
        file_put_contents($this->disabledFile, json_encode(array_values($disabled), JSON_PRETTY_PRINT));
    }
}
