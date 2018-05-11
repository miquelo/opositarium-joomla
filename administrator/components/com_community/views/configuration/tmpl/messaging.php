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

<div class="widget-box">
	<div class="widget-header widget-header-flat">
		<h5><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MESSAGING' ); ?></h5>
		<div class="widget-toolbar no-border">
			<a href="http://documentation.jomsocial.com/wiki/Private_Messaging" target="_blank"><i class="js-icon-info-sign"></i> <?php echo JText::_('COM_COMMUNITY_DOC'); ?></a>
		</div>
	</div>
	<div class="widget-body">
		<div class="widget-main">

			<table>
				<tbody>
					<tr>
						<td width="250" class="key">
							<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_MESSAGING_ENABLE_TIPS'); ?>">
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MESSAGING_ENABLE' ); ?>
							</span>
						</td>
						<td>
							<?php echo CHTMLInput::checkbox('enablepm' ,'ace-switch ace-switch-5', null , $this->config->get('enablepm') ); ?>
						</td>
					</tr>
					<tr>
						<td width="250" class="key">
							<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_MESSAGING_ENABLE_READ_STATUS_TIPS'); ?>">
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MESSAGING_ENABLE_READ_STATUS' ); ?>
							</span>
						</td>
						<td>
							<?php echo CHTMLInput::checkbox('enablereadstatus' ,'ace-switch ace-switch-5', null , $this->config->get('enablereadstatus') ); ?>
						</td>
					</tr>
                    <tr>
                        <td width="250" class="key">
							<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_MESSAGING_FILESHARING_ENABLE_TIPS'); ?>">
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MESSAGING_FILESHARING_ENABLE' ); ?>
							</span>
                        </td>
                        <td>
                            <?php echo CHTMLInput::checkbox('message_file_sharing' ,'ace-switch ace-switch-5', null , $this->config->get('message_file_sharing') ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="250" class="key">
							<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_MESSAGING_FILESHARING_MAXFILESIZE_TIPS'); ?>">
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MESSAGING_FILESHARING_MAXFILESIZE' ); ?>
							</span>
                        </td>
                        <td>
                            <select name="message_file_maxsize">
                                <?php

                                $options = array( 1, 2, 3, 4, 5, 10, 20, 50, 100, 0 );
                                $selectedValue = (int) $this->config->get('message_file_maxsize');
                                $selectedValue = in_array( $selectedValue, $options ) ? $selectedValue : 4;
                                foreach ( $options as $value ) {
                                    echo '<option value="' . $value . '"' . ( $value == $selectedValue ? ' selected' : '' ) .
                                            '>' . ( $value === 0 ? 'Unlimited' : $value ) . '</option>';
                                }

                                ?>
                            </select>
                        </td>
                    </tr>
					<tr>
						<td width="250" class="key">
							<span class="js-tooltip" title="<?php echo str_replace( '"', '&quot;', JText::_('COM_COMMUNITY_CONFIGURATION_MESSAGING_TOTAL_DISPLAYED_MESSAGE_TIPS') ); ?>">
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MESSAGING_TOTAL_DISPLAYED_MESSAGE' ); ?>
							</span>
						</td>
						<td>
                            <select name="message_total_initial_display">
                                <?php

                                $options = array( 5, 10, 20, 50 );
                                $selectedValue = (int) $this->config->get('message_total_initial_display');
                                $selectedValue = in_array( $selectedValue, $options ) ? $selectedValue : 10;
                                foreach ( $options as $value ) {
                                    echo '<option value="' . $value . '"' . ( $value == $selectedValue ? ' selected' : '' ) .
                                            '>' . $value . '</option>';
                                }

                                ?>
                            </select>
						</td>
					</tr>
					<tr>
						<td width="250" class="key">
							<span class="js-tooltip" title="<?php echo str_replace( '"', '&quot;', JText::_('COM_COMMUNITY_CONFIGURATION_MESSAGING_TOTAL_LOADED_MESSAGE_TIPS') ); ?>">
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MESSAGING_TOTAL_LOADED_MESSAGE' ); ?>
							</span>
						</td>
						<td>
                            <select name="message_total_loaded_display">
                                <?php

                                $options = array( 5, 10, 20, 50 );
                                $selectedValue = (int) $this->config->get('message_total_loaded_display');
                                $selectedValue = in_array( $selectedValue, $options ) ? $selectedValue : 10;
                                foreach ( $options as $value ) {
                                    echo '<option value="' . $value . '"' . ( $value == $selectedValue ? ' selected' : '' ) .
                                            '>' . $value . '</option>';
                                }

                                ?>
                            </select>
						</td>
					</tr>
					<tr>
						<td width="250" class="key">
							<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_MESSAGING_RECALL_MESSAGE_TIME_TIPS'); ?>">
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MESSAGING_RECALL_MESSAGE_TIME' ); ?>
							</span>
						</td>
						<td>
                            <select name="message_recall_minutes">
                                <?php

                                $options = array( 1, 5, 10, 30, 60, 1440, 0 );
                                $optionLabels = array( '1 minute', '5 minutes', '10 minutes', '30 minutes', '1 hour', '1 day', 'No limit' );
                                $selectedValue = (int) $this->config->get('message_recall_minutes');
                                $selectedValue = in_array( $selectedValue, $options ) ? $selectedValue : 0;
                                foreach ( $options as $index => $value ) {
                                    echo '<option value="' . $value . '"' . ( $value == $selectedValue ? ' selected' : '' ) .
                                            '>' . $optionLabels[ $index ] . '</option>';
                                }

                                ?>
                            </select>
						</td>
					</tr>
					<tr>
						<td width="250" class="key">
							<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_MESSAGING_ACTIVE_POLLING_TIME_TIPS'); ?>">
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MESSAGING_ACTIVE_POLLING_TIME' ); ?>
							</span>
						</td>
						<td>
                            <select name="message_pooling_time_active">
                                <?php

                                $options = array( 1, 2, 3, 5, 10, 20, 30, 60 );
                                $optionLabels = array( '1 second', '2 seconds', '3 seconds', '5 seconds', '10 seconds', '20 seconds', '30 seconds', '60 seconds' );
                                $selectedValue = (int) $this->config->get('message_pooling_time_active');
                                $selectedValue = in_array( $selectedValue, $options ) ? $selectedValue : 30;
                                foreach ( $options as $index => $value ) {
                                    echo '<option value="' . $value . '"' . ( $value == $selectedValue ? ' selected' : '' ) .
                                            '>' . $optionLabels[ $index ] . '</option>';
                                }

                                ?>
                            </select>
						</td>
					</tr>
                    <tr>
                        <td width="250" class="key">
                            <span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_MESSAGING_INACTIVE_POLLING_TIME_TIPS'); ?>">
                                <?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MESSAGING_INACTIVE_POLLING_TIME' ); ?>
                            </span>
                        </td>
                        <td>
                            <select name="message_pooling_time_inactive">
                                <?php

                                $options = array( 1, 2, 3, 5, 10, 20, 30, 60 );
                                $optionLabels = array( '1 second', '2 seconds', '3 seconds', '5 seconds', '10 seconds', '20 seconds', '30 seconds', '60 seconds' );
                                $selectedValue = (int) $this->config->get('message_pooling_time_inactive');
                                $selectedValue = in_array( $selectedValue, $options ) ? $selectedValue : 30;
                                foreach ( $options as $index => $value ) {
                                    echo '<option value="' . $value . '"' . ( $value == $selectedValue ? ' selected' : '' ) .
                                            '>' . $optionLabels[ $index ] . '</option>';
                                }

                                ?>
                            </select>
                        </td>
                    </tr>
					<tr>
						<td width="250" class="key">
							<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_MESSAGING_SHOW_TIMESTAMP_TIPS'); ?>">
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MESSAGING_SHOW_TIMESTAMP' ); ?>
							</span>
						</td>
						<td>
							<?php echo CHTMLInput::checkbox('message_show_timestamp' ,'ace-switch ace-switch-5', null , $this->config->get('message_show_timestamp') ); ?>
						</td>
					</tr>
					<tr>
						<td width="250" class="key">
							<span class="js-tooltip" title="<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_MESSAGING_TIMEFORMAT_TIPS'); ?>">
								<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MESSAGING_TIMEFORMAT' ); ?>
							</span>
						</td>
						<td>
							<select name="message_time_format">
								<option value="g:i A"<?php echo $this->config->get('message_time_format') == 'g:i A' ? ' selected="selected"' : ''; ?>>g:i A</option>
								<option value="g:i a"<?php echo $this->config->get('message_time_format') == 'g:i a' ? ' selected="selected"' : ''; ?>>g:i a</option>
								<option value="H:i"<?php echo $this->config->get('message_time_format') == 'H:i' ? ' selected="selected"' : ''; ?>>H:i</option>
							</select>

						</td>
					</tr>

				</tbody>
			</table>
		</div>
	</div>
</div>
