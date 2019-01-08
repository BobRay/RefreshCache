<?php
/**
 * en default topic lexicon file for RefreshCache extra
 *
 * Copyright 2011-2013 by Bob Ray <http://bobsguides.com>
 * Created on 07-15-2013
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
 * en default topic lexicon strings
 *
 * Variables
 * ---------
 * @var $modx modX
 * @var $scriptProperties array
 *
 * @package refreshcache
 **/

/* Used in transport.settings.php */
$_lang['setting_refreshcache_ajax_delay'] = 'RefreshCache Ajax delay';
$_lang['setting_refreshcache_ajax_delay_desc'] = 'Delay between JS calls (in milliseconds); default: 0';
$_lang['setting_refreshcache_curl_delay'] = 'RefreshCache cUrl Delay';
$_lang['setting_refreshcache_curl_delay_desc'] = 'Delay between cURL requests (in seconds); default: 0';

/* Used in refreshcache.snippet.php */
$_lang['rc_admin_only'] = 'This code can only be run by an administrator';
$_lang['rc_button_message'] = 'Refresh the Cache';
$_lang['rc_no_resources'] = 'No Cacheable Resources found';
$_lang['rc_no_curl'] = 'Failed to initialize cURL';
$_lang['rc_refreshing'] = 'Refreshing';
$_lang['rc_refresh_resource_cache'] = 'Refresh Resource Cache';
/* Used in transport.menus.php */
$_lang['rc_menu_desc'] = 'Refresh the cache for all cacheable resources';
$_lang['RefreshCache'] = 'RefreshCache';
$_lang['rc_refreshed'] = 'Refreshed';
$_lang['rc_resources'] = 'Resources';
$_lang['rc_getting_data'] = 'Getting Data';