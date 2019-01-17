<?php

namespace Ceres;

use Plenty\Modules\Plugin\Models\Plugin;
use Plenty\Modules\Plugin\Models\Configuration;

class SettingsHandler
{
    public function read($pluginSetId = null, $pluginName = null)
    {
        $plugin = Plugin::where('name', '=', $pluginName)->first();

        $pluginId = $plugin->id;

        $configs = Configuration::where('key', 'like', 'globalSetting:%')
            ->where('plugin_id', '=', $pluginId)
            ->get();

        foreach ($configs as $config)
        {
            $config->key = str_replace('globalSetting:', '', $config->key);
        }

        return $configs;
    }

    public function write($values, $pluginSetId = null)
    {
        $pluginId = 4;

        $config = new Configuration();

        $config->key = 'globalSetting:' . $values[0]['key'];
        $config->value = $values[0]['value'];
        $config->plugin_id = 4;
        $config->pluginSetEntryId = 4;

        $config->save();
    }
}
