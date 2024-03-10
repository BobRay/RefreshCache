<?php
/**
 * RefreshCache snippet for RefreshCache extra
 *
 * Copyright 2011-2024 Bob Ray <https://bobsguides.com>
 * Created on 03-09-2024
 *
 * RefreshCache is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * RefreshCache is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * RefreshCache; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA

 *
 * @package refreshcache
 */

/**
 * Description
 * -----------
 * Refreshes the site cache
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package refreshcache
 **/

if (file_exists(MODX_ASSETS_PATH . 'mycomponents/refreshcache/core/components/refreshcache/RefreshCacheCRON.php' )) {
    include MODX_ASSETS_PATH . 'mycomponents/refreshcache/core/components/refreshcache/RefreshCacheCRON.php';
} else {
    include MODX_CORE_PATH . 'components/refreshcache/RefreshCacheCRON.php';
}
