<?php
/**
* @copyright (C) 2013 iJoomla, Inc. - All rights reserved.
* @license GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html)
* @author iJoomla.com <webmaster@ijoomla.com>
* @url https://www.jomsocial.com/license-agreement
* The PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
* More info at https://www.jomsocial.com/license-agreement
*/
defined('_JEXEC') or die( 'Restricted Access' );

?>

<div class="joms-subnav">
    <ul>
    <?php
    foreach($submenu as $menu)
    {
        // set default task value if empty
        if (empty($menu->task)) {
            $menu->task = 'display';
        }

        if (empty($task)) {
            $task = 'display';
        }

        /* extra class */
        $menuClass= $menu->class . ' ';
        if( isset($menu->action) && ($menu->action) )
        {
            $menuClass .= 'action ';
        }
        if( isset($menu->childItem) && $menu->childItem )
        {
            $menuClass .= 'hasChildItem ';
        }

        $link=''; $linkClass=''; $onclick='';
        if( isset($menu->onclick) && !empty($menu->onclick) )
        {
            $link    = 'javascript: void(0);';
            $onclick =  $menu->onclick;
        } else {
            $link    = CRoute::_($menu->link);

            if( strtolower( $menu->view ) == strtolower($view) &&
                strtolower( $menu->task ) == strtolower($task) &&
                ! $noActive)
            {
                $linkClass .= 'class="'.'active'.'"';
            }
        }
    ?>
        <li<?php if ($menuClass) { ?> class="<?php echo $menuClass ?>"<?php } ?>>
            <a href="<?php echo $link ?>"
               <?php echo $linkClass ?>
               onclick="<?php echo $onclick ?>"><?php echo $menu->title ?></a>
            <?php echo $menu->childItem ?>
        </li>
    <?php
    }
    ?>
    </ul>
</div>

<div class="joms-subnav__menu">
    <?php
    $dropdownlist = '';
    foreach($submenu as $menu)
    {
        // set default task value if empty
        if (empty($menu->task)) {
            $menu->task = 'display';
        }
        
        if (empty($task)) {
            $task = 'display';
        }

        /* extra class */
        $menuClass= $menu->class . ' ';
        if( isset($menu->action) && ($menu->action) )
        {
            $menuClass .= 'action ';
        }
        if( isset($menu->childItem) && $menu->childItem )
        {
            $menuClass .= 'hasChildItem ';
        }

        $link=''; $linkClass=''; $onclick='';
        if( isset($menu->onclick) && !empty($menu->onclick) )
        {
            $link    = 'javascript: void(0);';
            $onclick =  $menu->onclick;
        } else {
            $link    = CRoute::_($menu->link);

            if( CStringHelper::strtolower( $menu->view ) == CStringHelper::strtolower($view) &&
                CStringHelper::strtolower( $menu->task ) == CStringHelper::strtolower($task) &&
                ! $noActive)
            {
                $linkClass .= ' class="'.'active'.'"';
                $activeTitle = $menu->title;
            }
        }

        $dropdownlist .= '<li ' . ($menuClass ? 'class="' . $menuClass . '"' : '') . '>';
        $dropdownlist .= '<a href="' . $link. '"' . $linkClass . ' onclick="' . $onclick .'">' . $menu->title . '</a>';
        $dropdownlist .= $menu->childItem;
        $dropdownlist .= '</li>';

    }
    ?>
    <a href="<?php echo (count($submenu) == 1) ? $link : 'javascript:'; ?>" class="joms-button--neutral joms-button--full" <?php echo (count($submenu) > 1) ? 'data-ui-object="joms-dropdown-button"' : ''; ?>>
        <?php 
            if (isset($activeTitle)) echo $activeTitle;
            else if (isset($submenu[0]->title)) echo $submenu[0]->title;
        ?>
        <?php if (count($submenu) > 1) { ?>
            <svg viewBox="0 0 16 16" class="joms-icon">
                <use xlink:href="<?php echo CRoute::getURI(); ?>#joms-icon-arrow-down"></use>
            </svg>
        <?php } ?>
    </a>
    <ul class="joms-dropdown">
        <?php echo $dropdownlist; ?>
    </ul>
    <div class="joms-gap"></div>
</div>

<div class="joms-subnav--desktop">
    <ul>
    <?php
    foreach($submenu as $menu)
    {
        // set default task value if empty
        if (empty($menu->task)) {
            $menu->task = 'display';
        }
        
        if (empty($task)) {
            $task = 'display';
        }

        /* extra class */
        $menuClass= $menu->class . ' ';
        if( isset($menu->action) && ($menu->action) )
        {
            $menuClass .= 'action ';
        }
        if( isset($menu->childItem) && $menu->childItem )
        {
            $menuClass .= 'hasChildItem ';
        }

        $link=''; $linkClass=''; $onclick='';
        if( isset($menu->onclick) && !empty($menu->onclick) )
        {
            $link    = 'javascript: void(0);';
            $onclick =  $menu->onclick;
        } else {
            $link    = CRoute::_($menu->link);

            if( CStringHelper::strtolower( $menu->view ) == CStringHelper::strtolower($view) &&
                CStringHelper::strtolower( $menu->task ) == CStringHelper::strtolower($task) &&
                ! $noActive)
            {
                $linkClass .= 'class="'.'active'.'"';
            }
        }
    ?>
        <li<?php if ($menuClass) { ?> class="<?php echo $menuClass ?>"<?php } ?>>
            <a href="<?php echo $link ?>"
               <?php echo $linkClass ?>
               onclick="<?php echo $onclick ?>"><?php echo $menu->title ?></a>
            <?php echo $menu->childItem ?>
        </li>
    <?php
    }
    ?>
    </ul>
</div>
