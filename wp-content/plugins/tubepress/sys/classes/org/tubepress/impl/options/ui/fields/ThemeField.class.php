<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class_exists('org_tubepress_impl_classloader_ClassLoader') || require(dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php');
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_impl_options_ui_fields_DropdownField',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_api_filesystem_Explorer',
));

/**
 * Displays a drop-down input for the TubePress theme.
 */
class org_tubepress_impl_options_ui_fields_ThemeField extends org_tubepress_impl_options_ui_fields_DropdownField
{
    const __ = 'org_tubepress_impl_options_ui_fields_ThemeField';
    
    public function getDescription()
    {
        $ioc               = org_tubepress_impl_ioc_IocContainer::getInstance();
        $themeHandler      = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $filesystem        = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $defaultThemesPath = $filesystem->getTubePressBaseInstallationPath() . '/sys/ui/themes';
        $userThemesPath    = $themeHandler->getUserContentDirectory() . '/themes';

        $originalDescription = parent::getDescription();

        return sprintf($originalDescription, $userThemesPath, $defaultThemesPath);
    }
}