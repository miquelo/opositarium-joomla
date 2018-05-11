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
$my = CFactory::getUser();
?>
<?php if($sortItems): ?>
    <script type="text/javascript">
        function joms_change_filterdropdown(value) {
            var url = value;
            url = url.replace('__sort__', value);

            window.location = url;
        }
    </script>
    <select class="joms-select" onchange="joms_change_filterdropdown(this.value);">
        <!-- <li class="filter-label"><?php echo JText::_('COM_COMMUNITY_SORT_BY'); ?>:</li> -->
        <?php
        $categoryid = $jinput->get->get('categoryid','');
        $catId      = $jinput->get->get('catid','');
        $groupid    = $jinput->get->get('groupid','');
        $eventid    = $jinput->get->get('eventid','');

        foreach( $sortItems as $key => $option )
        {
            $categoryLink   = ($categoryid) ? '&categoryid='.$categoryid : '';
            $catLink        = ($catId) ? '&catid='.$catId : '';
            $groupLink      = ($groupid) ? '&groupid='.$groupid : '';
            $eventLink      = ($eventid) ? '&eventid='.$eventid : '';

            $queries['sort'] = $key;
            if (isset($queries['filter'])) $queries['filter'] = $queries['filter'];
            if (isset($queries['radius'])) $queries['radius'] = $queries['radius'];
            if (isset($queries['profiletype'])) $queries['profiletype'] = $queries['profiletype'];
            
            $link = 'index.php?'. $uri->buildQuery($queries).$categoryLink.$catLink.$groupLink.$eventLink;
            $link = CRoute::_($link);
        ?>
            <option <?php if($key==CStringHelper::trim($selectedSort)) { ?>selected<?php }?> value="<?php echo $link; ?>"><?php echo $option; ?></option>
        <?php
        }
        ?>
    </select>
<?php
    $queries['sort'] = $selectedSort;
    
endif;
?>

<?php if($filterItems): ?>
    <select class="joms-select" onchange="window.location=this.value;">
        <?php foreach($filterItems as $key => $option): ?>
            <?php
            if (isset($queries['sort'])) $queries['sort'] = $queries['sort'];
            if (isset($queries['radius'])) $queries['radius'] = $queries['radius'];
            if (isset($queries['profiletype'])) $queries['profiletype'] = $queries['profiletype'];
            $queries['filter'] = $key;

            // We need to reset the pagination limitstart so the pagination will not affect the filter
            unset($queries['limitstart']);
            $link = 'index.php?'. $uri->buildQuery($queries);

            $link = CRoute::_($link);
            ?>

            <option value="<?php echo $link; ?>" <?php if ($selectedFilter == $key) echo 'selected="selected"'; ?>><?php echo $option; ?></option>
        <?php endforeach; ?>
    </select> 
<?php
    $queries['filter'] = $selectedFilter;
endif;
?>

<?php if($radiusItems && $my->id != 0): ?>
    <select class="joms-select" onchange="window.location=this.value;">
        <?php
        unset($queries['limitstart']);
        unset($queries['radius']);
        if (isset($queries['sort'])) $queries['sort'] = $queries['sort'];
        if (isset($queries['filter'])) $queries['filter'] = $queries['filter'];
        if (isset($queries['profiletype'])) $queries['profiletype'] = $queries['profiletype'];

        $link = 'index.php?'. $uri->buildQuery($queries);
        ?>
        <option value="<?php echo $link; ?>"><?php echo JText::_('COM_COMMUNITY_SORT_BY_DISTANCE_FROM_ME'); ?></option>
        <?php foreach($radiusItems as $key => $option): ?>
            <?php
            $queries['radius'] = $option;

            // We need to reset the pagination limitstart so the pagination will not affect the filter
            $link = 'index.php?'. $uri->buildQuery($queries);

            $link = CRoute::_($link);
            ?>

            <option value="<?php echo $link; ?>" <?php if ($selectedRadius == $option) echo 'selected="selected"'; ?>><?php echo $option.' '.(($config->get('advanced_search_units') == 'metric') ? JText::_('COM_COMMUNITY_SORT_BY_DISTANCE_METRIC') : JText::_('COM_COMMUNITY_SORT_BY_DISTANCE_IMPERIAL')); ?></option>
        <?php endforeach; ?>
    </select> 
<?php
    $queries['radius'] = $selectedRadius;
endif;
?>