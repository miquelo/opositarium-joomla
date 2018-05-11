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
</script>
<form action="index.php?option=com_community" method="get" name="adminForm" id="adminForm">
<div class="alert alert-info">
	<button type="button" class="close" data-dismiss="alert">
		<i class="js-icon-remove"></i>
	</button>
	<?php echo JText::_('COM_COMMUNITY_EVENTS_CREATION_FRONT_END'); ?>
</div>

<!-- page header -->
<div class="row-fluid">
	<div class="span24">
		<input type="text" onchange="document.adminForm.submit();" class="no-margin" value="<?php echo ($this->search) ? $this->escape($this->search) : '';?>" id="search" name="search"/>
		<div class="btn btn-small btn-primary" onclick="document.adminForm.submit();">
			<i class="js-icon-search"></i>
			<?php echo JText::_('COM_COMMUNITY_SEARCH');?>
		</div>

		<div class="pull-right text-right">
			<?php echo $this->categories;?>
			<?php echo $this->_getStatusHTML();?>
		</div>

	</div>
</div>


<table class="table table-bordered table-hover">
	<thead>
		<tr class="title">
			<th width="10">#</th>
			<th width="10">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
				<span class="lbl"></span>
			</th>
			<th>
				<?php echo JText::_('COM_COMMUNITY_COVER_PHOTO')?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', JText::_('COM_COMMUNITY_NAME'), 'a.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JText::_('COM_COMMUNITY_EVENTS_DESCRIPTION'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_COMMUNITY_CATEGORY')?>
			</th>
			<th>
				<?php echo JText::_('COM_COMMUNITY_EVENTS_DATE')?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', JText::_('COM_COMMUNITY_PUBLISHED'), 'a.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', JText::_('COM_COMMUNITY_EVENTS_INVITED_GUEST'), 'a.invitedcount', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JHTML::_('grid.sort', JText::_('COM_COMMUNITY_EVENTS_CONFIRMED_GUEST'), 'a.confirmedcount', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th>
				<?php echo JText::_('COM_COMMUNITY_VIEW');?>
			</th>
		</tr>
	</thead>
	<?php $i = 0; ?>
	<?php
		if( empty( $this->events ) )
		{
	?>
	<tr>
		<td colspan="7" align="center"><?php echo (empty($this->search)) ? JText::_('COM_COMMUNITY_EVENTS_NOT_CREATED') : JText::_('No event has matched your search criteria') ;?></td>
	</tr>
	<?php
		}
	?>
	<?php foreach( $this->events as $row ): ?>
	<tr>
		<td align="center">
			<?php echo ( $i + 1 ); ?>
		</td>
		<td>
			<?php echo JHTML::_('grid.id', $i++, $row->id); ?>
			<span class="lbl"></span>
		</td>
		<td>
                <img width="90" src="<?php echo CUrlHelper::coverURI($row->cover, 'cover-event.png'); ?>" />
		</td>
		<td>
			<a href="javascript:void(0);" onclick="azcommunity.editEvent('<?php echo $row->id;?>');">
				<?php echo $row->title; ?>
			</a>
		</td>
		<td>
			<?php echo $row->description; ?>
		</td>
		<td>
			<?php echo $row->category;?>
		</td>
		<td>
			<?php echo $row->startdate;?>
		</td>
		<td id="published<?php echo $row->id;?>" align="center" class='center'>
			<?php echo $this->getPublish( $row , 'published' , 'events,ajaxTogglePublish' );?>
		</td>
		<td align="center">
			<?php echo $row->invitedcount; ?>
		</td>
		<td align="center">
			<?php echo $row->confirmedcount; ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_(JUri::root().'index.php?option=com_community&view=events&task=viewevent&eventid='.$row->id) ?>" target="_blank"><?php echo JText::_('COM_COMMUNITY_VIEW')?></a>
		</td>
	</tr>
	<?php endforeach; ?>

</table>

<div class="pull-left">
<?php echo $this->pagination->getListFooter(); ?>
</div>

<div class="pull-right">
<?php echo $this->pagination->getLimitBox(); ?>
</div>

<input type="hidden" name="view" value="events" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="task" value="events" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<?php
	$config = CFactory::getConfig();
	$lang = JFactory::getLanguage();
	$lang->load('com_community' , JPATH_ROOT);
?>
<script>

	joms_tmp_pickadateOpts = {
		min      : true,
		format   : 'yyyy-mm-dd',
		firstDay : <?php echo $config->get('event_calendar_firstday') === 'Monday' ? 1 : 0 ?>,
		today    : '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_CURRENT", true) ?>',
		'clear'  : '<?php echo JText::_("COM_COMMUNITY_DATEPICKER_CLEAR", true) ?>'
	};

	joms_tmp_pickadateOpts.weekdaysFull = [
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_1", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_2", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_3", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_4", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_5", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_6", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_DAY_7", true) ?>'
	];

	joms_tmp_pickadateOpts.weekdaysShort = [];
	for ( i = 0; i < joms_tmp_pickadateOpts.weekdaysFull.length; i++ )
		joms_tmp_pickadateOpts.weekdaysShort[i] = joms_tmp_pickadateOpts.weekdaysFull[i].substr( 0, 3 );

	joms_tmp_pickadateOpts.monthsFull = [
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_1", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_2", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_3", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_4", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_5", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_6", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_7", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_8", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_9", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_10", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_11", true) ?>',
		'<?php echo JText::_("COM_COMMUNITY_DATEPICKER_MONTH_12", true) ?>'
	];

	joms_tmp_pickadateOpts.monthsShort = [];
	for ( i = 0; i < joms_tmp_pickadateOpts.monthsFull.length; i++ )
		joms_tmp_pickadateOpts.monthsShort[i] = joms_tmp_pickadateOpts.monthsFull[i].substr( 0, 3 );

</script>
<script>
	function joms_datepicker_event_init() {
		joms_tmp_startDate = jQuery('input[name=startdate]').pickadate( jQuery.extend({}, joms_tmp_pickadateOpts, {
			klass: { frame: 'picker__frame startDate' },
			min: true,
			onSet: function( o ) {
				var min = new Date(o.select),
					date, hour, minute;

				if ( isNaN( min.getTime() ) ) {
					min = joms_tmp_pickadateOpts.min;
				}

				if ( window.joms_tmp_endDate ) {
					// Set min range.
					joms_tmp_endDate.set({ min: min }, { muted: true });

					// Set the field as well.
					min = new Date( joms_tmp_endDate.get( 'min', 'yyyy-mm-dd' ) );
					date = new Date( joms_tmp_endDate.get() );
					if ( !date.getTime() || date.getTime() < min.getTime() ) {
						joms_tmp_endDate.set({ select: min }, { muted: true }, { format: 'yyyy-mm-dd' });
					}

					jQuery('#starttime-hour').triggerHandler('change');
				}
			}
		}) ).pickadate('picker');

		joms_tmp_endDate = jQuery('input[name=enddate]').pickadate( jQuery.extend({}, joms_tmp_pickadateOpts, {
			klass: { frame: 'picker__frame endDate' },
			min: false,
			onSet: function( o ) {
				jQuery('#starttime-hour').triggerHandler('change');
			}
		}) ).pickadate('picker');

		var $shour = jQuery('#starttime-hour'),
			$smin  = jQuery('#starttime-min'),
			$sampm = jQuery('#starttime-ampm'),
			$ehour = jQuery('#endtime-hour'),
			$emin  = jQuery('#endtime-min'),
			$eampm = jQuery('#endtime-ampm'),
			isAmpm = $sampm.length;

		// Validate time.
		$shour.add( $smin ).add( $sampm ).add( $ehour ).add( $emin ).add( $eampm ).change(function() {
			var sdate = new Date( jQuery('input[name=startdate]').val() ).getTime(),
				edate = new Date( jQuery('input[name=enddate]').val() ).getTime(),
				shour, smin, ehour, emin, nextDay;

			if ( !sdate || !edate || edate > sdate ) {
				return;
			}

			shour = +$shour.val();
			smin  = +$smin.val();
			ehour = +$ehour.val();
			emin  = +$emin.val();

			if ( isAmpm ) {
				console.log( isAmpm );
				if ( $sampm.val() === 'PM' ) {
					shour += shour < 12 ? 12 : 0;
				} else if ( shour === 12 ) {
					shour = 0;
				}
				if ( $eampm.val() === 'PM' ) {
					ehour += ehour < 12 ? 12 : 0;
				} else if ( ehour === 12 ) {
					ehour = 0;
				}
			}

			if ( ehour > shour || ( ehour === shour && emin > smin )) {
				return;
			}

			ehour = shour;
			emin = smin + 15;
			if ( emin >= 60 ) {
				emin = 0;
				ehour += 1;
				if ( ehour >= 24 ) {
					ehour = 0;
					nextDay = true;
				}
			}

			$emin.val( emin );

			if ( !isAmpm ) {
				$ehour.val( ehour );
			} else {
				if ( ehour === 0 ) {
					$ehour.val( 12 );
					$eampm.val('AM');
				} else if ( ehour < 12 ) {
					$ehour.val( ehour );
					$eampm.val('AM');
				} else if ( ehour === 12 ) {
					$ehour.val( 12 );
					$eampm.val('PM');
				} else {
					$ehour.val( ehour - 12 );
					$eampm.val('PM');
				}
			}

			if ( nextDay ) {
				edate = new Date( joms_tmp_startDate.get() );
				edate.setDate( edate.getDate() + 1 );
				joms_tmp_endDate.set({ select: edate }, { muted: true }, { format: 'yyyy-mm-dd' });
			}

		});
	}
</script>
