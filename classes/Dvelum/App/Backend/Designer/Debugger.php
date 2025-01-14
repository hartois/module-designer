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

namespace Dvelum\App\Backend\Designer;

use Dvelum\View;
use Dvelum\Config;

/**
 * Class Debugger
 * @package Dvelum\App\Backend\Designer\Module
 */
class Debugger extends Module
{
    public function indexAction()
    {
        $project = $this->getProject();
        $template = View::factory();
        $template->set('project', $project);
        $template->disableCache();

        $designerConfig = Config::storage()->get('designer.php');
        // change theme
        $designerTheme = $designerConfig->get('application_theme');
        $page = \Page::getInstance();
        $page->setTemplatesPath('system/' . $designerTheme. '/');

        echo $template->render($page->getTemplatesPath().'designer/project_debug.php');
    }
}