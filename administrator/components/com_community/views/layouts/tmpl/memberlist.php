<?php
/**
 * @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
 * @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author iJoomla.com <webmaster@ijoomla.com>
 * @url https://www.jomsocial.com/license-agreement
 * The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at https://www.jomsocial.com/license-agreement
 */

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php?option=com_community" method="post" name="adminForm" id="adminForm">
<table>
    <tr>
        <td class="key" width="280">
                <span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_LAYOUTS_SHOW_DISTANCE_CONFIG_TIPS'); ?>">
                    <?php echo JText::_( 'COM_COMMUNITY_LAYOUTS_SHOW_DISTANCE_CONFIG_TIPS' ); ?>
                </span>
        </td>
        <td>
            <?php
                // check if google maps integration properly setup
                if (CMapsHelper::googleMapSetup()) {
                    echo CHTMLInput::checkbox('config[memberlist_show_distance]' ,'ace-switch ace-switch-5', null , CFactory::getConfig()->get('memberlist_show_distance') );
                } else {
                    echo CHTMLInput::checkbox('config[memberlist_show_distance]' ,'ace-switch ace-switch-5', 'disabled' , CFactory::getConfig()->get('memberlist_show_distance') );
                    echo '<div class="alert alert-notice">';
                    echo JText::sprintf('COM_COMMUNITY_CONFIGURATION_GOOGLEMAPS_DISABLED_JOOMLA_ERROR', CRoute::_('index.php?option=com_community&view=configuration&cfgSection=integrations'));
                    echo '</div>';
                }
            ?>
        </td>
    </tr>
    <tr>
        <td class="key">
                <span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_LAYOUTS_FRIENDS_BUTTON_CONFIG_TIPS'); ?>">
                    <?php echo JText::_( 'COM_COMMUNITY_LAYOUTS_FRIENDS_BUTTON_CONFIG' ); ?>
                </span>
        </td>
        <td>
            <?php echo CHTMLInput::checkbox('config[memberlist_show_friends_button]' ,'ace-switch ace-switch-5', null , CFactory::getConfig()->get('memberlist_show_friends_button') ); ?>
        </td>
    </tr>
    <tr>
        <td class="key">
                <span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_LAYOUTS_FRIENDS_PROFILE_INFO_CONFIG_TIPS'); ?>">
                    <?php echo JText::_( 'COM_COMMUNITY_LAYOUTS_FRIENDS_PROFILE_INFO_CONFIG' ); ?>
                </span>
        </td>
        <td>
            <?php echo CHTMLInput::checkbox('config[memberlist_show_profile_info]' ,'ace-switch ace-switch-5', null , CFactory::getConfig()->get('memberlist_show_profile_info') ); ?>
        </td>
    </tr>
    <tr>
        <td class="key">
                <span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_LAYOUTS_FRIENDS_COUNT_CONFIG_TIPS'); ?>">
                    <?php echo JText::_( 'COM_COMMUNITY_LAYOUTS_FRIENDS_COUNT_CONFIG' ); ?>
                </span>
        </td>
        <td>
            <?php echo CHTMLInput::checkbox('config[memberlist_show_friends_count]' ,'ace-switch ace-switch-5', null , CFactory::getConfig()->get('memberlist_show_friends_count') ); ?>
        </td>
    </tr>
    <tr>
        <td class="key">
                <span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_LAYOUTS_MATCHMAKING_MODE_CONFIG_TIPS'); ?>">
                    <?php echo JText::_( 'COM_COMMUNITY_LAYOUTS_MATCHMAKING_MODE_CONFIG' ); ?>
                </span>
        </td>
        <td>
            <?php echo CHTMLInput::checkbox('config[memberlist_matchmaking_mode]' ,'ace-switch ace-switch-5', null , CFactory::getConfig()->get('memberlist_matchmaking_mode') ); ?>
        </td>
    </tr>

    <tr>
        <td class="key">
                <span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_LAYOUTS_MATCHMAKING_GENDER_CONFIG_TIPS'); ?>">
                    <?php echo JText::_( 'COM_COMMUNITY_LAYOUTS_MATCHMAKING_GENDER_CONFIG' ); ?>
                </span>
        </td>
        <td>
            <?php echo $this->getGenderFieldCodes('config[memberlist_matchmaking_fieldcodegender]' , CFactory::getConfig()->get('fieldcodegender')); ?>
        </td>
    </tr>
</table>
    <input type="hidden" name="view" value="layouts" />
    <input type="hidden" name="option" value="com_community" />
    <input type="hidden" name="task" value="memberlist" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>