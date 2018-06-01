<?php

namespace Nonetallt\Jinitialize;

use Composer\Script\Event;

class ComposerScripts
{
    public static function postAutoloadDump(Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        /* require_once $vendorDir . '/autoload.php'; */

        $packages = [];

        if(file_exists($path = $vendorDir . '/composer/installed.json')) {
            $packages = json_decode(file_get_contents($path), true);
        }

        /* The path where the plugin manifest will be created */
        $output = __DIR__ . '/../bootstrap/cache/plugins.php';

        self::generatePluginsManifest($packages, $output, $vendorDir);    
    }

    /**
     * Separate mehtod for testing puroposes
     */
    public static function generatePluginsManifest(array $packages, string $outputPath, string $vendorDir = null)
    {
        $plugins = [];

        foreach($packages as $package) {

            /* Skip packages that do not define plugin in extra */
            if(empty($package['extra']['jinitialize-plugin'])) continue;

            /* Append plugin directory to info */
            if(! is_null($vendorDir)) {
                /* The directory where the plugin will be installed */
                $path = $vendorDir .'/'. $package['name'];
                $package['extra']['jinitialize-plugin']['path'] = $path;
            }

            $pluginInfo = $package['extra']['jinitialize-plugin'];
            $plugins[] = $pluginInfo;
        }

        $content = '<?php return ' . var_export($plugins, true) . ';';
        file_put_contents($outputPath, $content);
    }

    /**
     *
     * @param string $path path to the manifest file
     * @return array $plugins
     *
     */
    public static function loadPluginsManifest(string $path)
    {
        if(! file_exists($path)) return [];

        /* Return the var_export */
        return include $path;
    }
}
