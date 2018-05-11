<?php
/**
 * @copyright (C) 2016 iJoomla, Inc. - All rights reserved.
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
        <h5><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_INVITE_ONLY' ); ?></h5>
    </div>
    <div class="widget-body">
        <div class="widget-main">
            
            <?php
                $comUsersParams = JComponentHelper::getParams('com_users');
                
                if($comUsersParams->get('allowUserRegistration')) {
                    echo '<div class="alert alert-notice">';
                    echo JText::sprintf('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_DISABLED_JOOMLA_ERROR', CRoute::_('index.php?option=com_config&view=component&component=com_users'));
                    echo '</div>';
                }
            ?>

            <table>
                <tbody>
                <tr>
                    <td width="250" class="key">
							<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_INVITE_ONLY_OPTIONS_TIPS'); ?>">
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_INVITE_ONLY_OPTIONS' ); ?>
							</span>
                    </td>
                    <td>
                        <?php
                            //check if com_users registration is enabled
                            if(!$comUsersParams->get('allowUserRegistration')) {
                                echo CHTMLInput::checkbox('invite_only', 'ace-switch ace-switch-5', null, $this->config->get('invite_only'));
                            }else{
                                echo CHTMLInput::checkbox('invite_only', 'ace-switch ace-switch-5', 'disabled', $this->config->get('invite_only'));
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td width="250" class="key">
                            <span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_REQUEST_INVITE_OPTIONS_TIPS'); ?>">
                                <?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_REQUEST_INVITE_OPTIONS' ); ?>
                            </span>
                    </td>
                    <td>
                        <?php
                            //check if com_users registration is enabled
                            if(!$comUsersParams->get('allowUserRegistration')) {
                                echo CHTMLInput::checkbox('invite_only_request', 'ace-switch ace-switch-5', null, $this->config->get('invite_only_request'));
                            }else{
                                echo CHTMLInput::checkbox('invite_only_request', 'ace-switch ace-switch-5', 'disabled', $this->config->get('invite_only_request'));
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="key">
							<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_LIMIT_OPTIONS_TIPS'); ?>">
							<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_LIMIT_OPTIONS' ); ?>
							</span>
                    </td>
                    <td>
                        <select name="invite_registation_limit">
                            <option value="0"<?php echo $this->config->get('invite_registation_limit') == 0 ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_CONFIGURATION_REGISTRATIONS_UNLIMITED');?></option>
                            <option value="5"<?php echo $this->config->get('invite_registation_limit') == 5 ? ' selected="selected"' : ''; ?>><?php echo 5;?></option>
                            <option value="10"<?php echo $this->config->get('invite_registation_limit') == 10 ? ' selected="selected"' : ''; ?>><?php echo 10;?></option>
                            <option value="50"<?php echo $this->config->get('invite_registation_limit') == 50 ? ' selected="selected"' : ''; ?>><?php echo 50;?></option>
                            <option value="100"<?php echo $this->config->get('invite_registation_limit') == 100 ? ' selected="selected"' : ''; ?>><?php echo 100;?></option>
                        </select>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
