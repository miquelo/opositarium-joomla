<?php
/**
* @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<div class="widget-box">
	<div class="widget-header widget-header-flat">
		<h5><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FRONTPAGE' ); ?></h5>
	</div>
	<div class="widget-body">
		<div class="widget-main">
			<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
						<td width="250" class="key">
							<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_FRONTPAGE_SITE_TITLE_DESC'); ?>">
							<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FRONTPAGE_SITE_TITLE' ); ?>
							</span>
						</td>
						<td valign="top">
							<input type="text" name="sitename" value="<?php echo $this->config->get('sitename');?>" size="40" />
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>