<?php
/**
* @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/
defined('_JEXEC') or die();
?>

<div>
	<div class="joms-map--location-map joms-js--location-map"></div>
	<div class="joms-map--location-result">
        <input type="text" class="joms-input joms-js--location-input" />
        <div class="joms-map--location-loading joms-js--location-loading">
            <img src="<?php echo JURI::root(true) ?>/components/com_community/assets/ajax-loader.gif" alt="loader" />
        </div>
		<div class="joms-map--location-selector joms-js--location-selector"></div>
	</div>
</div>
