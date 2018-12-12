<!-- assets/templates/site-multidomain.php -->
<div class="wrap">

	<h1><?php _e( 'CiviCRM Admin Utilities', 'civicrm-admin-utilities' ); ?></h1>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo $urls['settings']; ?>" class="nav-tab"><?php _e( 'Settings', 'civicrm-admin-utilities' ); ?></a>
		<?php

		/**
		 * Allow others to add tabs.
		 *
		 * @since 0.5.4
		 *
		 * @param array $urls The array of subpage URLs.
		 * @param str The key of the active tab in the subpage URLs array.
		 */
		do_action( 'civicrm_admin_utilities_settings_nav_tabs', $urls, 'multidomain' );

		?>
	</h2>

	<form method="post" id="civicrm_admin_utilities_multidomain_form" action="<?php echo $this->page_submit_url_get(); ?>">

		<?php wp_nonce_field( 'civicrm_admin_utilities_multidomain_action', 'civicrm_admin_utilities_multidomain_nonce' ); ?>

		<h3><?php _e( 'CiviCRM Domain Information', 'civicrm-admin-utilities' ); ?></h3>

		<?php if ( ! $multisite ) : ?>
			<div class="updated error">
				<p><?php _e( 'You need to install and activate the CiviCRM Multisite extension.', 'civicrm-admin-utilities' ); ?></p>
			</div>
		<?php endif; ?>

		<ul>

			<li><?php echo sprintf(
				__( 'The current domain for this site is: "%1$s" (ID: %2$s)', 'civicrm-admin-utilities' ),
				'<span class="cau_domain_name">' . $domain['name'] . '</span>',
				'<span class="cau_domain_id">' . $domain['id'] . '</span>'
			); ?></li>

			<li><?php echo sprintf(
				__( 'The current domain group for this site is: "%1$s" (ID: %2$s)', 'civicrm-admin-utilities' ),
				'<span class="cau_domain_group_name">' . $domain_group['name'] . '</span>',
				'<span class="cau_domain_group_id">' . $domain_group['id'] . '</span>'
			); ?></li>

			<li><?php echo sprintf(
				__( 'The current domain organisation for this site is: "%1$s" (ID: %2$s)', 'civicrm-admin-utilities' ),
				'<span class="cau_domain_org_name">' . $domain_org['name'] . '</span>',
				'<span class="cau_domain_org_id">' . $domain_org['id'] . '</span>'
			); ?></li>

		</ul>

		<hr />

		<h3><?php _e( 'Domain', 'civicrm-admin-utilities' ); ?></h3>

		<div class="cau-domain-edit">

			<table class="form-table">

				<tr>
					<th scope="row">
						<label class="civicrm_admin_utilities_settings_label" for="cau_domain_select">
							<?php _e( 'Choose a Domain', 'civicrm-admin-utilities' ); ?>
						</label>
					</th>

					<td>
						<select id="cau_domain_select" name="cau_domain_select">
							<option value=""><?php _e( 'Select existing Domain', 'civicrm-admin-utilities' ); ?></option>
						</select>
					</td>
				</tr>

			</table>

		</div>

		<div class="cau-domain-create">

			<table class="form-table">

				<tr>
					<th scope="row">
						<label class="civicrm_admin_utilities_settings_label" for="cau_domain_name">
							<?php _e( 'Domain Name', 'civicrm-admin-utilities' ); ?>
						</label>
					</th>

					<td>
						<input id="cau_domain_name" name="cau_domain_name" class="cau_text_input" value="" />
					</td>
				</tr>

			</table>

		</div>

		<hr />

		<h3><?php _e( 'Domain Group', 'civicrm-admin-utilities' ); ?></h3>

		<div class="cau-domain-group-edit">

			<table class="form-table">

				<tr>
					<th scope="row">
						<label class="civicrm_admin_utilities_settings_label" for="cau_domain_group_select">
							<?php _e( 'Choose a Domain Group', 'civicrm-admin-utilities' ); ?>
						</label>
					</th>

					<td>
						<select id="cau_domain_group_select" name="cau_domain_group_select">
							<option value=""><?php _e( 'Select existing Group', 'civicrm-admin-utilities' ); ?></option>
						</select>
					</td>
				</tr>

			</table>

		</div>

		<hr />

		<h3><?php _e( 'Domain Organisation', 'civicrm-admin-utilities' ); ?></h3>

		<div class="cau-domain-org-edit">

			<table class="form-table">

				<tr>
					<th scope="row">
						<label class="civicrm_admin_utilities_settings_label" for="cau_domain_org_select">
							<?php _e( 'Choose a Domain Organisation', 'civicrm-admin-utilities' ); ?>
						</label>
					</th>

					<td>
						<p><select id="cau_domain_org_select" name="cau_domain_org_select">
							<option value=""><?php _e( 'Select existing Organisation', 'civicrm-admin-utilities' ); ?></option>
						</select></p>
					</td>
				</tr>

			</table>

		</div>

		<hr />

		<div class="cau-domain-submit">
			<p class="submit">
				<input class="button-primary" type="submit" id="civicrm_admin_utilities_multidomain_submit" name="civicrm_admin_utilities_multidomain_submit" value="<?php _e( 'Save Changes', 'civicrm-admin-utilities' ); ?>" />
			</p>
		</div>

	</form>

</div><!-- /.wrap -->
