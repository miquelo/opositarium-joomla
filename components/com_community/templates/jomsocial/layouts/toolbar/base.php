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
$jinput = JFactory::getApplication()->input;
$viewName = $jinput->get('view');
$taskName = $jinput->get('task');
require_once( JPATH_ROOT .'/components/com_community/libraries/core.php' );
$svgPath = CFactory::getPath('template://assets/icon/joms-icon.svg');
include_once $svgPath;
// call the auto refresh on specific page
?>
<?php if ($menuParams != '' && $menuParams->get('show_page_heading') != 0) : ?>
<div class="page-header">
    <h3><?php echo $this->escape($menuParams->get('page_title')); ?></h3>
</div>
<?php endif; ?>
<?php if ($showToolbar) : ?>

<div class="joms-toolbar">
    <ul>
        <li>
            <span class="joms-trigger__menu--main">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-menu"></use>
                </svg>
            </span>
        </li>
        <li>
            <a class="joms-js--notification-general" data-ui-object="joms-dropdown-button" href="<?php echo CRoute::_('index.php?option=com_community&view=profile&task=notifications'); ?>">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-earth"></use>
                </svg>
                <span class="joms-notifications__label joms-js--notiflabel-general"><?php echo $newEventInviteCount > 0 ? $newEventInviteCount : ''; ?></span>
            </a>
            <ul class="joms-popover joms-popover--toolbar-general">
                <li class="joms-js--loading" style="display:block">
                    <img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader">
                </li>
            </ul>
        </li>
        <li>
            <a class="joms-js--notification-friendrequest" data-ui-object="joms-dropdown-button" href="<?php echo CRoute::_('index.php?option=com_community&view=friends&task=pending'); ?>">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-users"></use>
                </svg>
                <span class="joms-notifications__label joms-js--notiflabel-frequest"><?php echo $newFriendInviteCount > 0 ? $newFriendInviteCount : ''; ?></span>
            </a>
            <ul class="joms-popover joms-popover--toolbar-friendrequest">
                <li class="joms-js--loading" style="display:block">
                    <img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader">
                </li>
            </ul>
        </li>

        <?php if ($isMessageEnable) { ?>
        <li class="joms-js--notification-chat-list-mobile">
            <a class="joms-js--notification-chat" data-ui-object="joms-dropdown-button" href="<?php echo CRoute::_('index.php?option=com_community&view=chat'); ?>">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-envelope"></use>
                </svg>
                <span class="joms-notifications__label joms-js--notiflabel-chat"><?php echo $newChatCount > 0 ? $newChatCount : '' ?></span>
            </a>
            <ul class="joms-popover joms-arrow--top joms-popover--toolbar-chat" data-propagate="1">
                <li class="joms-js--empty">
                    <span><?php echo JText::_('COM_COMMUNITY_CHAT_NOTIF_NO_NEW_MESSAGE') ?></span>
                </li>
                <div>
                    <a href="<?php echo CRoute::_('index.php?option=com_community&view=chat'); ?>" class="joms-button--neutral joms-button--full"><?php echo JText::_('COM_COMMUNITY_CHAT_NOTIF_SHOW_ALL') ?></a>
                </div>
            </ul>
        </li>
        <?php } ?>

        <li>
            <a class="joms-js--notification-search" href="javascript:" data-ui-object="joms-dropdown-button">
                <svg viewBox="0 0 16 16" class="joms-icon joms-trigger__menu--search">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-search"></use>
                </svg>
            </a>
            <ul class="joms-popover joms-popover--toolbar-search">
                <li class="joms-js--noremove joms-js--field">
                    <input type="text" class="joms-input" placeholder="<?php echo JText::_('COM_COMMUNITY_SEARCH'); ?>"
                        oninput="joms.view.toolbar.search(this, event);">
                </li>
                <li class="joms-js--noremove joms-js--loading">
                    <img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader">
                </li>
                <li class="joms-js--noremove joms-js--viewall">
                    <div>
                        <a href="javascript:" class="joms-button--neutral joms-button--full"
                            data-lang="<?php echo JText::_('COM_COMMUNITY_VIEW_ALL_N_RESULTS'); ?>"></a>
                        <form method="post" action="<?php echo CRoute::_('index.php?option=com_community&view=search&task=advancesearch'); ?>">
                            <input type="hidden" name="q" value="">
                        </form>
                    </div>
                </li>
            </ul>
        </li>
        <li>
            <span class="joms-trigger__menu--user">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-user"></use>
                </svg>
            </span>
        </li>
    </ul>

</div>

<div class="joms-toolbar--desktop">
    <ul>
        <li>
            <a href="<?php echo CRoute::_('index.php?option=com_community&view=frontpage') ?>">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-home"></use>
                </svg>
            </a>
        </li>
        <?php
            foreach ($menus as $menu) {
                $hasDropdown = !empty($menu->childs);
                $attr = $hasDropdown ? ' class="joms-js--has-dropdown" data-ui-object="joms-dropdown-button"' : '';
                $title = (isset($menu->item->anchor_title)) ? 'title="' . $menu->item->anchor_title . '"' : '';

                if ($menu->item->browserNav == 1) {
                    $target = 'target="_blank"';
                } else if ($menu->item->browserNav == 2) {
                    $target = 'onclick="window.open(this.href, \'targetWindow\', \'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes\'); return false;"';
                } else {
                    $target = '';
                }
        ?>
        <li class="<?php echo $active === $menu->item->id ? 'active ' : '' ?><?php echo (isset($menu->item->css)) ? $menu->item->css : '' ?>">
            <a href="<?php echo CRoute::_($menu->item->link) ?>"<?php echo $attr . $title . $target; ?>><?php echo JText::_( $menu->item->name ); ?></a>
            <?php if ($hasDropdown) { ?>
            <ul class="joms-dropdown joms-arrow--top-left">
                <?php foreach( $menu->childs as $child ) { ?>
                    <li>
                        <?php if( $child->script ){ ?>
                            <a href="javascript:void(0);" onclick="<?php echo $child->link;?>">
                        <?php } else { ?>
                            <a href="<?php echo CRoute::_( $child->link );?>">
                        <?php } ?>
                        <?php echo JText::_( $child->name );?></a>
                    </li>
                <?php } ?>
            </ul>
            <?php } ?>
        </li>
        <?php } ?>
        <li>
            <a class="joms-js--notification-general" data-ui-object="joms-dropdown-button" href="<?php echo CRoute::_('index.php?option=com_community&view=profile&task=notifications'); ?>">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-earth"></use>
                </svg>
                <span class="joms-notifications__label joms-js--notiflabel-general"><?php echo $newEventInviteCount > 0 ? $newEventInviteCount : ''; ?></span>
            </a>
            <ul class="joms-popover joms-arrow--top joms-popover--toolbar-general">
                <li class="joms-js--loading" style="display:block">
                    <img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader">
                </li>
            </ul>
        </li>
        <li>
            <a class="joms-js--notification-friendrequest" data-ui-object="joms-dropdown-button" href="<?php echo CRoute::_('index.php?option=com_community&view=friends&task=pending'); ?>">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-users"></use>
                </svg>
                <span class="joms-notifications__label joms-js--notiflabel-frequest"><?php echo $newFriendInviteCount > 0 ? $newFriendInviteCount : ''; ?></span>
            </a>
            <ul class="joms-popover joms-arrow--top joms-popover--toolbar-friendrequest">
                <li class="joms-js--loading" style="display:block">
                    <img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader">
                </li>
            </ul>
        </li>

        <?php if ($isMessageEnable) { ?>
        <li class="joms-js--notification-chat-list">
            <a class="joms-js--notification-chat" data-ui-object="joms-dropdown-button" href="<?php echo CRoute::_('index.php?option=com_community&view=chat'); ?>">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-envelope"></use>
                </svg>
                <span class="joms-notifications__label joms-js--notiflabel-chat"><?php echo $newChatCount > 0 ? $newChatCount : '' ?></span>
            </a>
            <ul class="joms-popover joms-arrow--top joms-popover--toolbar-chat" data-propagate="1">
                <li class="joms-js--empty">
                    <span><?php echo JText::_('COM_COMMUNITY_CHAT_NOTIF_NO_NEW_MESSAGE') ?></span>
                </li>
                <div>
                    <a href="<?php echo CRoute::_('index.php?option=com_community&view=chat'); ?>" class="joms-button--neutral joms-button--full"><?php echo JText::_('COM_COMMUNITY_CHAT_NOTIF_SHOW_ALL') ?></a>
                </div>
            </ul>
        </li>
        <?php } ?>

        <li>
            <a class="joms-js--notification-search" href="javascript:" data-ui-object="joms-dropdown-button">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-search"></use>
                </svg>
            </a>
            <ul class="joms-popover joms-popover--toolbar-search">
                <li class="joms-js--noremove joms-js--field">
                    <input type="text" class="joms-input" placeholder="<?php echo JText::_('COM_COMMUNITY_SEARCH'); ?>"
                        oninput="joms.view.toolbar.search(this, event);">
                </li>
                <li class="joms-js--noremove joms-js--loading">
                    <img src="<?php echo JURI::root(true); ?>/components/com_community/assets/ajax-loader.gif" alt="loader">
                </li>
                <li class="joms-js--noremove joms-js--viewall">
                    <div>
                        <a href="javascript:" class="joms-button--neutral joms-button--full"
                            data-lang="<?php echo JText::_('COM_COMMUNITY_VIEW_ALL_N_RESULTS'); ?>"></a>
                        <form method="post" action="<?php echo CRoute::_('index.php?option=com_community&view=search&task=advancesearch'); ?>">
                            <input type="hidden" name="q" value="">
                        </form>
                    </div>
                </li>
            </ul>
        </li>
        <li class="joms-right">
            <a class="joms-js--logout" href="javascript:void(0);" onclick="document.getElementById('jomsocial-logout-form').submit();">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-switch"></use>
                </svg>
            </a>
        </li>

    </ul>
</div>

<div class="joms-menu">
    <ul>
        <li><a href="<?php echo CRoute::_('index.php?option=com_community&view=frontpage'); ?>"><?php echo JText::_('COM_COMMUNITY_HOME'); ?></a></li>
        <?php foreach ($menus as $menu) { ?>
        <li>
            <a href="<?php echo CRoute::_($menu->item->link); ?>"><?php echo JText::_($menu->item->name); ?></a>
            <?php if ( !empty($menu->childs) ) { ?>
            <span class="joms-menu__toggle">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-arrow-down"></use>
                </svg>
            </span>
            <ul>
                <?php foreach ($menu->childs as $child) { ?>
                <li>
                    <?php if ($child->script) { ?>
                        <a href="javascript:" onclick="<?php echo $child->link; ?>">
                    <?php } else { ?>
                        <a href="<?php echo CRoute::_($child->link); ?>">
                    <?php } ?>
                    <?php echo JText::_($child->name); ?></a>
                </li>
                <?php } ?>
            </ul>
            <?php } ?>
        </li>
        <?php } ?>
    </ul>
</div>

<div class="joms-menu--user">
    <ul>
        <li><a href="<?php echo CRoute::_('index.php?option=com_community&view=profile'); ?>"><?php echo JText::_('COM_COMMUNITY_MY_PROFILE'); ?></a></li>
        <li><a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&task=editPage'); ?>"><?php echo JText::_('COM_COMMUNITY_PROFILE_CUSTOMIZE_PAGE'); ?></a></li>
        <li><a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&task=preferences'); ?>"><?php echo JText::_('COM_COMMUNITY_EDIT_PREFERENCES'); ?></a></li>
        <li><a href="javascript:void(0);" onclick="document.getElementById('jomsocial-logout-form').submit();"><?php echo JText::_('COM_COMMUNITY_LOGOUT'); ?></a></li>
    </ul>
</div>

<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="jomsocial-logout-form" style="display: none;">
    <input type="hidden" name="option" value="com_users" />
    <input type="hidden" name="task" value="user.logout" />
    <input type="hidden" name="return" value="<?php echo $logoutLink; ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>

<?php endif; ?>

<?php if (isset($miniheader) && $miniheader) { ?>
    <?php echo $miniheader; ?>
<?php } ?>

<?php if ( !empty( $groupMiniHeader ) ) { ?>
    <?php echo $groupMiniHeader; ?>
<?php }; ?>

<?php
    if(isset($eventMiniHeader) && $eventMiniHeader){
        echo $eventMiniHeader;
    }
?>

