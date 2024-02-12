<?php

namespace App\Traits;

trait AddonHelper
{
    private string $moduleDirectory = '';
    private array $rootDirectories = [];

    public function __construct()
    {
        $this->moduleDirectory = base_path('Modules/');
        $this->rootDirectories = $this->getDirectories($this->moduleDirectory);
    }

    public function get_addons(): array
    {
        $addons = [];
        foreach ($this->rootDirectories as $directory) {
            $sub_dirs = self::getDirectories($this->moduleDirectory . $directory);
            if (in_array('Addon', $sub_dirs)) {
                $addons[] = $this->moduleDirectory . $directory;
            }
        }

        $array = [];
        foreach ($addons as $item) {
            $full_data = include($item . '/Addon/info.php');
            $array[] = [
                'addon_name' => $full_data['name'],
                'software_id' => $full_data['software_id'],
                'is_published' => $full_data['is_published'],
            ];
        }

        return $array;
    }

    public function get_addon_admin_routes(): array
    {
        $addons = [];
        foreach ($this->rootDirectories as $directory) {
            $sub_dirs = self::getDirectories($this->moduleDirectory . $directory);
            if (in_array('Addon', $sub_dirs)) {
                $addons[] = $this->moduleDirectory . $directory;
            }
        }

        $full_data = [];
        foreach ($addons as $item) {
            $info = include($item . '/Addon/info.php');
            if ($info['is_published']){
                $full_data[] = include($item . '/Addon/admin_routes.php');
            }
        }

        return $full_data;
    }

    public function get_payment_publish_status(): array
    {
        $addons = [];
        foreach ($this->rootDirectories as $directory) {
            $sub_dirs = self::getDirectories($this->moduleDirectory . $directory); // Use $dir instead of 'Modules/'
            if($directory == 'Gateways'){
                if (in_array('Addon', $sub_dirs)) {
                    $addons[] = $this->moduleDirectory . $directory; // Use $dir instead of 'Modules/'
                }
            }
        }

        $array = [];
        foreach ($addons as $item) {
            $full_data = include($item . '/Addon/info.php');
            $array[] = [
                'is_published' => $full_data['is_published'],
            ];
        }

        return $array;
    }


    function getDirectories(string $path): array
    {
        $directories = [];
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item == '..' || $item == '.')
                continue;
            if (is_dir($path . '/' . $item))
                $directories[] = $item;
        }
        return $directories;
    }
}
