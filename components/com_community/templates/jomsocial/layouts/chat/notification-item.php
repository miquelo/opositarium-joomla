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
<li class="joms-popover--toolbar-chat-item {{= +data.seen ? '' : 'unread' }} joms-js-chat-notif joms-js-chat-notif-{{= data.chat_id }}"
        data-chat-id="{{= data.chat_id }}" style="cursor:pointer">
    <div class="joms-popover__avatar">
        <div class="joms-avatar">
            <img src="{{= data.thumb }}" alt="avatar">
        </div>
    </div>
    <div class="joms-popover__content">
        <a href="javascript:">{{= data.name }}</a>
        <small data-timestamp="{{= data.timestamp }}" data-elapsed>{{= data.time }}</small>
    </div>
</li>
