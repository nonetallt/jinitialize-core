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

        var_dump($packages);

        self::generatePluginsManifest($packages, __DIR__ . '/../bootstrap/cache/plugins.php');    
    }

    /**
     * Separate mehtod for testing puroposes
     */
    public static function generatePluginsManifest(array $packages, string $outputPath)
    {
        $plugins = [];

        foreach($packages as $package) {

            /* Skip packages that do not define plugin in extra */
            if(empty($package['extra']['jinitialize-plugin'])) continue;


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
        if(! file_exists($packagesFile)) return [];

        /* Return the var_export */
        return include $path;
    }
}
