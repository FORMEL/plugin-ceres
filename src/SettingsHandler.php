<?php

namespace Ceres;

use Plenty\Modules\Plugin\Models\Plugin;
use Plenty\Modules\Plugin\Models\Configuration;
use Plenty\Modules\Plugin\PluginSet\Contracts\PluginSetEntryRepositoryContract;
use Plenty\Modules\Plugin\PluginSet\Models\PluginSetEntry;
use Plenty\Modules\Plugin\PluginSet\Repositories\PluginSetEntryRepository;

class SettingsHandler
{
    public function read($pluginSetId = null, $pluginName = null)
    {
        $plugin = Plugin::where('name', '=', $pluginName)->first();
        $pluginId = $plugin->id;

        $pluginSetEntry = PluginSetEntry::where('pluginId', '=', $pluginId)
            ->where('pluginSetId', '=', $pluginSetId)
            ->first();
        $pluginSetEntryId = $pluginSetEntry->id;

        $configs = Configuration::where('key', 'like', 'globalSetting:%')
            ->where('plugin_id', '=', $pluginId)
            ->where('pluginSetEntryId', '=', $pluginSetEntryId)
            ->get();

        foreach ($configs as $config)
        {
            $config->key = str_replace('globalSetting:', '', $config->key);
        }

        return $configs;
    }

    public function write($pluginSetId, $pluginName, $values)
    {
        $config = new Configuration();

        $plugin = Plugin::where('name', '=', $pluginName)->first();
        $pluginId = $plugin->id;

        /** @var PluginSetEntryRepository $pluginSetEntryRepository */
        $pluginSetEntryRepository = pluginApp(PluginSetEntryRepositoryContract::class);

        $pluginSetEntries = $pluginSetEntryRepository->list();

        $pluginSetEntryId = null;

        foreach($pluginSetEntries as $pluginSetEntry)
        {
            if((int)$pluginSetEntry->pluginId === (int)$pluginId && (int)$pluginSetEntry->pluginSetId === (int)$pluginSetId)
            {
                $pluginSetEntryId = $pluginSetEntry->id;
            }
        }

        $config->key = 'globalSetting:' . $values[0]['key'];
        $config->value = $values[0]['value'];
        $config->plugin_id = $pluginId;
        $config->pluginSetEntryId = $pluginSetEntryId;

        $config->save();
    }
}
