<?php

namespace Nonetallt\Jinitialize;

use Composer\Script\Event;

class ComposerScripts
{
    public static function postAutoloadDump(Event $event)
    {
        /*TODO  PLACEHOLDER */
        /* var_dump($event->getComposer()->getPackage()->getExtra()); */

        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        require_once $vendorDir . '/autoload.php';

        $installedPackages = [];

        if(file_exists($path = $vendorDir . '/composer/installed.json')) {
            $installedPackages = json_decode(file_get_contents($path), true);
        }

        $discoverPackages = [];

        foreach($installedPackages as $package) {
            if(!empty($package['extra']['jinitialize-plugin'])) {
                $packageInfo = $package['extra']['jinitialize-plugin'];

                $discoverPackages[$package['name']] = [];

                /* TODO */
                if(!empty($packageInfo['plugins'])) {
                    $discoverPackages[$package['name']['plugins']] = $packageInfo['plugins'];
                }
            }
        }
        $content = '<?php return ' . var_export($discoverPackages, true) . ';';
        file_put_contents(__DIR__ . '/../bootstrap/cache/plugins.php', $content);
    }
}
