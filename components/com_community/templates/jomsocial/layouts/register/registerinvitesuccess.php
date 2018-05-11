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
<div class="joms-page" style="text-align: center;" >
    <h2><?php echo JText::_('COM_COMMUNITY_THANK_YOU_REQUESTING_INVITATION'); ?></h2>
    <p><?php echo JText::_('COM_COMMUNITY_REQUEST_INVITE_SUCCESS_MSG'); ?></p>
    <div class="joms-gap"></div>
    <svg viewBox="0 0 16 16" class="joms-icon" style="width:10%;height:10%;">
        <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-support"></use>
    </svg>
    <div class="joms-gap"></div>
    <a href="<?php echo CRoute::_('index.php?option=com_community&view=frontpage'); ?>" class="joms-button--primary joms-button--full-small">
        <?php echo JText::_('COM_COMMUNITY_BACK_HOME'); ?>
    </a>
</div>