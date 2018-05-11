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
<div class="joms-chat__item joms-js--chat-item-##chat_id## ##unread##" data-chat-type="##type##" data-chat-id="##chat_id##">
    <div class="joms-avatar">
        <img src="##thumb##" alt="">
    </div>
    <div class="joms-chat__item-body">
        <a href="#">##name##</a>
        <span class="joms-js--chat-item-msg"></span>
    </div>
    ##mute##
</div>
