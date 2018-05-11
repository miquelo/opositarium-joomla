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
<div class="joms-chat__window">
    <div class="joms-chat__window-title">
        <span class="joms-chat__status"></span>
        ##username##
        <a href="#" class="joms-chat__window-close">
            <svg viewBox="0 0 16 16" class="joms-icon">
                <use xlink:href="#joms-icon-close"></use>
            </svg>
        </a>
    </div>
    <div class="joms-chat__window-body">
        <div class="joms-chat__message">
            <div class="joms-chat__message-avatar">
                <img src="" alt="">
            </div>
            <div class="joms-chat__message-bubble">
                <p>##message##</p>
            </div>
            <div class="joms-chat__message-media">
                <img src="" alt="">
            </div>
        </div>
    </div>
    <div class="joms-chat__input-wrapper">
        <input type="text">
        <div class="joms-chat__input-actions">
            <a href="#">
                <svg viewBox="0 0 16 16" class="joms-icon">
                    <use xlink:href="#joms-icon-camera"></use>
                </svg>
            </a>
        </div>
    </div>
</div>
