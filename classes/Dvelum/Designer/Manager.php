<?php
/**
 *  DVelum project https://github.com/dvelum/dvelum
 *  Copyright (C) 2011-2019  Kirill Yegorov
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Dvelum\Designer;

use Dvelum\Config\ConfigInterface;
use Dvelum\Config;
use Dvelum\File;

/**
 * Designer Factory Wrapper
 */
class Manager
{
    /**
     * Application configuration
     * @var ConfigInterface
     */
    protected $_appConfig;
    /**
     * Designer configuration
     * @var ConfigInterface
     */
    protected $_designerConfig;

    public function __construct(ConfigInterface $appConfig)
    {
        $this->appConfig = $appConfig;
        $this->designerConfig = Config::storage()->get('designer.php');
    }

    /**
     * Render Designer project
     * @param string $projectFile - file path
     * @param string | boolean $renderTo - optional, default false (html tag id)
     * @param string | boolean $moduleId
     */
    public function renderProject(string $projectFile , $renderTo = false, $moduleId = false)
    {
        $replaces = $this->getReplaceConfig();
        Factory::runProject($projectFile , $this->designerConfig , $replaces , $renderTo, $moduleId);
    }

    /**
     * Compile designer project and return info (script paths, namespaces)
     * @param $projectFile
     * @param $renderTo
     * @param $moduleId
     * @return array
     */
    public function compileDesktopProject($projectFile , $renderTo, $moduleId)
    {
        $replaces = $this->getReplaceConfig();
        return Factory::compileDesktopProject($projectFile , $this->designerConfig , $replaces , $renderTo, $moduleId);
    }
    /**
     * Get Designer projects tree list
     */
    public function  getProjectsList($node = '')
    {
        $paths = Config::storage()->getPaths();
        $cfgPath = $this->designerConfig->get('configs');

        $list = [];
        $ret = [];

        // In accordance with configs merge priority
        rsort($paths);

        foreach($paths as $path)
        {
            $nodePath = str_replace('//', '/', $path.$cfgPath.$node);

            if(!file_exists($nodePath))
                continue;

            $items = File::scanFiles($nodePath , array('.dat'), false, File::Files_Dirs);

            if(!empty($items))
            {
                foreach ($items as $p)
                {
                    $baseName = basename($p);

                    if(!isset($list[$baseName])){
                        $obj = new \stdClass();
                        $obj->id = str_replace($path.$cfgPath, '/', $p);
                        $obj->path = str_replace($nodePath.$cfgPath, '/', $p);
                        $obj->text = $baseName;

                        if(is_dir($p))
                        {
                            $obj->expanded = false;
                            $obj->leaf = false;
                        }
                        else
                        {
                            $obj->leaf = true;
                        }
                        $list[$baseName] = $obj;
                    }
                }
            }
        }

        ksort($list);
        foreach($list as $p)
            $ret[] = $p;

        return $ret;
    }

    /**
     * Find working copy of project file
     * @param $relativeProjectPath
     * @return string | boolean
     */
    public function findWorkingCopy($relativeProjectPath)
    {
        $configPath = $this->designerConfig->get('configs');
        $paths = Config::storage()->getPaths();
        // In accordance with configs merge priority
        rsort($paths);

        foreach($paths as $path)
        {
            $nodePath = str_replace('//', '/', $path . $configPath . $relativeProjectPath);

            if(file_exists($nodePath))
                return $nodePath;
        }
        return false;
    }

    /**
     * Get configuration of code templates (for replacing)
     * @return array
     */
    public function getReplaceConfig()
    {
        $templates =  $this->designerConfig->get('templates');
        return array(
            array(
                'tpl' => $templates['wwwroot'],
                'value' => $this->appConfig->get('wwwroot')
            ),
            array(
                'tpl' => $templates['adminpath'],
                'value' => $this->appConfig->get('adminPath')
            ),
            array(
                'tpl' => $templates['urldelimiter'],
                'value' => $this->appConfig->get('urlDelimiter')
            )
        );
    }
}