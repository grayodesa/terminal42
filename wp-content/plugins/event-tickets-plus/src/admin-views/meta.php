<?php
if ( empty( $fieldset_form ) ) {
	?>
	<tr class="tribe-tickets-show-attendee-info ticket_advanced ticket_advanced_meta">
		<td style="width: 20%;">
			<?php _e( 'Attendee Information:', 'tribe-events-calendar' ); ?>
		</td>
		<td>
			<?php /* save as checked if the ticket has attendee info */ ?>
			<label>
				<input type="checkbox" name="show_attendee_info" <?php checked( $enable_meta ); ?> id="show_attendee_info" class="ticket_field show_attendee_info" data-tribe-toggle="tribe-tickets-attendee-info-form" value="1">
				<?php esc_html_e( 'Request information for each attendee during registration', 'event-tickets-plus' ); ?>
			</label>
			<p class="detailed description">
				<?php esc_html_e( 'The name and contact info of the person acquiring tickets is collected by default' ); ?>
			</p>
		</td>
	</tr>
	<?php
}
?>
<tr id="tribe-tickets-attendee-info-form" class="eventtable tribe-tickets-attendee-info-form ticket_advanced ticket_advanced_meta">
	<td colspan="2">
		<table class="eventtable">
			<?php
			if ( empty( $fieldset_form ) ) {
				?>
				<tr>
					<td colspan="2" class="titlewrap">
						<h4 class="tribe_sectionheader"><?php esc_html_e( 'Attendee Information', 'event-tickets-plus' ); ?></h4>
					</td>
				</tr>
				<?php
			}
			?>

			<tr class="tribe-attendee-fields-box">
				<td style="width:20%">
					<h5><?php esc_html_e( 'Add New Field:', 'event-tickets-plus' ); ?></h5>
					<ul class="tribe-tickets-attendee-info-options">
						<li id="tribe-tickets-add-text" class="tribe-tickets-attendee-info-option">
							<a href="#" class="add-attendee-field" data-type="text"><?php esc_html_e( 'Text', 'event-tickets-plus' ); ?> <span class="dashicons dashicons-plus-alt"></span></a>
						</li>
						<li id="tribe-tickets-add-radio" class="tribe-tickets-attendee-info-option">
							<a href="#" class="add-attendee-field" data-type="radio"><?php esc_html_e( 'Radio', 'event-tickets-plus' ); ?> <span class="dashicons dashicons-plus-alt"></span></a>
						</li>
						<li id="tribe-tickets-add-checkbox" class="tribe-tickets-attendee-info-option">
							<a href="#" class="add-attendee-field" data-type="checkbox"><?php esc_html_e( 'Checkbox', 'event-tickets-plus' ); ?> <span class="dashicons dashicons-plus-alt"></span></a>
						</li>
						<li id="tribe-tickets-add-select" class="tribe-tickets-attendee-info-option">
							<a href="#" class="add-attendee-field" data-type="select"><?php esc_html_e( 'Dropdown', 'event-tickets-plus' ); ?> <span class="dashicons dashicons-plus-alt"></span></a>
						</li>
					</ul>

				</td>
				<td>
					<h5><?php esc_html_e( 'Active Fields:', 'event-tickets-plus' ); ?></h5>
					<div class="tribe-tickets-attendee-saved-fields">
						<div class="tribe-tickets-saved-fields-select">
							<p><?php esc_html_e( 'No active fields.', 'event-tickets-plus' ); ?> <span class="tribe-tickets-add-new-fields"><?php esc_html_e( 'Add new fields or', 'event-tickets-plus' ); ?></span></p>

							<select class="chosen ticket-attendee-info-dropdown" name="ticket-attendee-info[MetaID]" id="saved_ticket-attendee-info" title="Start with a saved fieldset..." >

								<option selected value="0"><?php esc_html_e( 'Start with a saved fieldset...', 'event-tickets-plus' ); ?></option>
									<?php foreach ( $templates as $template ): ?>
										<option data-attendee-group="<?php echo esc_attr( $template->post_title ); ?>"
										        value="<?php echo esc_attr( $template->ID ); ?>"><?php echo esc_attr( $template->post_title ); ?></option>
									<?php endforeach; ?>
							</select>
						</div>

					</div>
					<div id="tribe-tickets-attendee-sortables" class="meta-box-sortables ui-sortable">
						<?php
						$meta_object = Tribe__Tickets_Plus__Main::instance()->meta();
						foreach ( $active_meta as $meta ) {
							$field = $meta_object->generate_field( $ticket_id, $meta->type, (array) $meta );
							echo $field->render_admin_field();
						}
						?>

					</div>
					<?php
					if ( empty( $fieldset_form ) ) {
						?>
						<div class="tribe-tickets-input tribe-tickets-attendee-save-fieldset">
							<label>
								<input type="checkbox" name="tribe-tickets-save-fieldset" id="save_attendee_fieldset" value="on" class="ticket_field save_attendee_fieldset" data-tribe-toggle="tribe-tickets-attendee-saved-fieldset-name">
								<?php esc_html_e( 'Save this fieldset for use on other tickets?', 'event-tickets-plus' ); ?>
							</label>
						</div>
						<div id="tribe-tickets-attendee-saved-fieldset-name" class="tribe-tickets-input tribe-tickets-attendee-saved-fieldset-name">
							<label for="tribe-tickets-saved-fieldset-name"><?php esc_html_e( 'Name this fieldset:', 'event-tickets-plus' ); ?></label>
								<input type="text" class="ticket_field" name="tribe-tickets-saved-fieldset-name" value="">
						</div>
						<?php
					}
					?>
				</td>
			</tr>
		</table>
	</td>
</tr>
