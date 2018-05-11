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
?>
<script type="text/javascript" language="javascript">
/**
 * This function needs to be here because, Joomla toolbar calls it
 **/
 Joomla.submitbutton = function(action){
 	submitbutton( action );
 }

function submitbutton( action )
{
	submitform( action );
}

jQuery(function( $ ) {
    var cssField = '.joms-js-field',
        cssHandle = '.joms-js-field-handle',
        startIndex, $container;

    $container = $('.joms-js-fields-container').sortable({
        items: cssField,
        handle: cssHandle,
        start: function( e, ui ) {
            var $tr = ui.item;
            startIndex = $tr.prevAll( cssField ).length;
        },
        stop: function( e, ui ) {
        },
        update: function( e, ui ) {
            var $tr = ui.item,
                id = $tr.data('id'),
                endIndex = $tr.prevAll( cssField ).length,
                sortVal = endIndex - startIndex;
            if ( sortVal !== 0 ) {
                $container.sortable('disable');
                jax.call('community', 'admin,multiprofile,ajaxSortField', id, sortVal );
                jax.doneLoadingFunction = function() {
                    $container.sortable('enable');
                };
            }
        }
    });
});
</script>
<style>
.table .ui-sortable-helper {
    display: table;
}
</style>
<div class="well">
	<p><?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_HEADER_MESSAGE')?></p>
	<a class="btn btn-mini btn-info" href="http://tiny.cc/jsmultiprofile" target="_blank"><i class="js-icon-info-sign"></i> <?php echo JText::_('COM_COMMUNITY_DOC'); ?></a>
</div>

<form action="index.php?option=com_community" method="post" name="adminForm" id="adminForm">

<table class="table table-bordered table-hover">
	<thead>
		<tr class="title">
            <th width="10">&nbsp;</th>
			<th width="10">#</th>
			<th width="10">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
				<span class="lbl"></span>
			</th>
			<th>
				<?php echo JText::_('COM_COMMUNITY_NAME');?>
			</th>
			<th>
				<?php echo JText::_('COM_COMMUNITY_DESCRIPTION');?>
			</th>
			<th>
				<?php echo JText::_('COM_COMMUNITY_TOTAL_USERS');?>
			</th>
			<th>
				<?php echo JText::_('COM_COMMUNITY_PUBLISHED');?>
			</th>
			<th>
				<?php echo JText::_( 'COM_COMMUNITY_USERGOUP' );?>
			</th>
		</tr>
	</thead>
    <tbody class="joms-js-fields-container">
	<?php $i = 0; ?>
	<?php
		if( empty( $this->profiles ) )
		{
	?>
	<tr>
		<td colspan="8" align="center"><?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_NO_PROFILE_CREATED_YET');?></td>
	</tr>
	<?php
		}
		else
		{
			$n=count( $this->profiles );
			for( $i=0; $i < $n; $i++ )
			{
				$row	= $this->profiles[ $i ];
	?>
		<tr class="joms-js-field" data-id="<?php echo $row->id; ?>">
            <td class="joms-js-field-handle" style="background-color: #EEEEEE; cursor: move;"><span class="icon-move"></span></td>
			<td align="center">
				<?php echo ( $i + 1 ); ?>
			</td>
			<td>
				<?php echo JHTML::_('grid.id', $i , $row->id); ?>
				<span class="lbl"></span>
			</td>
			<td>
				<a href="<?php echo JRoute::_('index.php?option=com_community&view=multiprofile&layout=edit&id=' . $row->id ); ?>"><?php echo $row->name; ?></a>
			</td>
			<td><?php echo $row->description; ?></td>
			<td align="center"><?php echo $this->getTotalUsers( $row->id );?></td>
			<td id="published<?php echo $row->id;?>" align="center" class='center'><?php echo $this->getPublish( $row , 'published' , 'multiprofile,ajaxTogglePublish' );?></td>
			<td>
				<?php
                $params = new CParameter('');
                $params->loadString($row->params);
                $userGroupSelected = $params->get('userGroup', array($this->defaultUserGroup));

                $groups = array();
                foreach (JHelperUsergroups::getInstance()->getAll() as $group) {
                    foreach ($userGroupSelected as $selected) {
                        if ($group->id == $selected) $groups[] = $group->title;
                    }
                }

                echo implode(', ', $groups);
                ?>
			</td>
		</tr>
	<?php
			}
	?>
	<?php } ?>
    </tbody>
</table>

<div class="pull-left">
<?php echo $this->pagination->getListFooter(); ?>
</div>

<div class="pull-right">
<?php echo $this->pagination->getLimitBox(); ?>
</div>

<input type="hidden" name="view" value="multiprofile" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="task" value="multiprofile" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' );?>
</form>
