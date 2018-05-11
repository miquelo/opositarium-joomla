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

$license_details = $this->getLicenseDetail($this->config->get('registerlicense'));
?>

<div class="widget-box">
	<div class="widget-header widget-header-flat">
		<h5><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTER_LICENSE_TITLE' ); ?></h5>
		<div class="widget-toolbar no-border">
			<!-- <a href="http://tiny.cc/dailylimits" target="_blank">
				<i class="js-icon-info-sign"></i> <?php echo JText::_('COM_COMMUNITY_DOC'); ?>
			</a> -->
		</div>
	</div>
	<div class="widget-body">
		<div class="widget-main">
			<p><?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTER_LICENSE_TEXT'); ?></p>
			<table>
				<tbody>
					<tr>
						<td width="140" class="key">
							<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTER_LICENSE_TIPS'); ?>">
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTER_LICENSE' ); ?>
							</span>
						</td>
						<td>
							&nbsp;<input type="text" name="registerlicense" value="<?php echo $this->config->get('registerlicense');?>" class="input-big" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<span>
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTER_LICENSE_DOMAIN' ); ?>
							</span>
						</td>
						<td>
							:&nbsp;<?php if (isset($license_details["domain"])) echo $license_details["domain"]; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<span>
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTER_LICENSE_EXPIRE' ); ?>
							</span>
						</td>
						<td>
							:&nbsp;<?php if (isset($license_details["date"])) echo $license_details["date"]; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<span>
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTER_LICENSE_STATUS' ); ?>
							</span>
						</td>
						<td>
							:&nbsp;<?php if (isset($license_details["status"]))  echo $license_details["status"]; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>