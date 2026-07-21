<?php

namespace DynamicOOO\PluginUtils\AdminPages\Pages;

if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
class LicensePage extends \DynamicOOO\PluginUtils\AdminPages\Pages\Base
{
    /**
     * Render page content
     *
     * @return void
     */
    protected function render_content()
    {
        if ('POST' === $_SERVER['REQUEST_METHOD'] && (!isset($_POST[$this->plugin_utils_manager->get_config('prefix') . '-settings-page']) || !wp_verify_nonce($_POST[$this->plugin_utils_manager->get_config('prefix') . '-settings-page'], $this->plugin_utils_manager->get_config('prefix') . '-settings-page'))) {
            wp_die(esc_html__('Nonce verification error.', 'dynamic-ooo'));
        }
        $license_system = $this->plugin_utils_manager->license;
        if (isset($_POST['license_key'])) {
            if ($_POST['license_activated']) {
                list($success, $msg) = $license_system->deactivate_license();
                if (!$success) {
                    $this->plugin_utils_manager->admin_pages->admin_notices->error($msg);
                } else {
                    $msg = esc_html__('License key successfully deactivated for this site', 'dynamic-ooo');
                    $this->plugin_utils_manager->admin_pages->admin_notices->success($msg);
                }
            } else {
                $license_key = $_POST['license_key'];
                list($success, $msg) = $license_system->activate_new_license_key($license_key);
                if (!$success) {
                    $this->plugin_utils_manager->admin_pages->admin_notices->error($msg);
                } else {
                    $msg = esc_html__('License key successfully activated for this site', 'dynamic-ooo');
                    $this->plugin_utils_manager->admin_pages->admin_notices->success($msg);
                }
            }
        } else {
            $license_system->refresh_and_repair_license_status();
        }
        $license_system->domain_mismatch_check();
        if (isset($_POST['beta_status'])) {
            if (isset($_POST['enable_beta'])) {
                $license_system->activate_beta_releases();
            } else {
                $license_system->deactivate_beta_releases();
            }
        }
        $license_key = $license_system->get_license_key();
        $is_license_active = $license_system->is_license_active();
        $license_domain = get_option($this->plugin_utils_manager->get_config('prefix') . '_license_domain');
        ?>
		<div class="ooo-content-wrapper">
			<div class="ooo-license-tab">
				<h2 class="ooo-license-status">
					<?php 
        esc_html_e('License Status', 'dynamic-ooo');
        ?>: 
					<?php 
        if ($is_license_active) {
            ?>
						<span class="ooo-status-active"><?php 
            esc_html_e('Active', 'dynamic-ooo');
            ?></span>
					<?php 
        } else {
            ?>
						<span class="ooo-status-inactive"><?php 
            esc_html_e('Not Active', 'dynamic-ooo');
            ?></span>
					<?php 
        }
        ?>
				</h2>

				<form action="" method="post" class="ooo-license-key">
					<?php 
        wp_nonce_field($this->plugin_utils_manager->get_config('prefix') . '-settings-page', $this->plugin_utils_manager->get_config('prefix') . '-settings-page');
        ?>
					<input type="text" 
						   autocomplete="new-password" 
						   autocorrect="off"
						   autocapitalize="off"
						   spellcheck="false"
						   style="-webkit-text-security: disc; text-security: disc; width: 100%; max-width: 300px;"
						   name="license_key" 
						   value="<?php 
        echo esc_attr($license_key);
        ?>" 
						   placeholder="<?php 
        esc_attr_e('Insert License Key', 'dynamic-ooo');
        ?>" 
						   id="license_key">
					<input type="hidden" name="license_activated" value="<?php 
        echo $is_license_active;
        ?>">
					<?php 
        if ($is_license_active) {
            submit_button(esc_html__('Deactivate', 'dynamic-ooo'), 'cancel', 'submit', \false);
        } else {
            submit_button(esc_html__('Save key and activate', 'dynamic-ooo'), 'primary', 'submit', \false);
        }
        ?>
				</form>

				<?php 
        if ($is_license_active) {
            ?>
					<?php 
            if ($license_domain && $license_domain !== $license_system->get_current_domain()) {
                ?>
						<p><strong class="ooo-text-warning">
							<?php 
                esc_html_e('Your license is valid but there is something wrong: license mismatch.', 'dynamic-ooo');
                ?>
						</strong></p>
						<p><?php 
                esc_html_e('Your license key doesn\'t match your current domain. This is most likely due to a change in the domain URL. Please deactivate the license and reactivate it', 'dynamic-ooo');
                ?></p>
					<?php 
            } else {
                ?>
						<p><strong class="ooo-status-active">
							<?php 
                \printf(esc_html__('Your license ending in "%1$s" is valid and active.', 'dynamic-ooo'), '<strong>' . esc_html($license_system->get_license_key_last_4_digits()) . '</strong>');
                ?>
						</strong></p>
					<?php 
            }
            ?>
				<?php 
        } else {
            ?>
					<div class="ooo-license-get-it-now">
						<p><?php 
            esc_html_e('Enter your license to keep the plugin updated, obtaining new features, future compatibility, increased stability, security, and technical support.', 'dynamic-ooo');
            ?></p>
						<p>
							<?php 
            esc_html_e('You still don\'t have one?', 'dynamic-ooo');
            ?> 
							<a href="<?php 
            echo esc_url($this->plugin_utils_manager->get_config('pricing_url'));
            ?>" class="button button-small" target="_blank">
								<?php 
            esc_html_e('Get it now!', 'dynamic-ooo');
            ?>
							</a>
						</p>
					</div>
				<?php 
        }
        ?>
			</div>

			<?php 
        if ($is_license_active) {
            ?>
				<?php 
            if ($this->plugin_utils_manager->get_supports_beta()) {
                $beta_enabled = get_option($this->plugin_utils_manager->get_config('prefix') . '_beta');
                ?>
				<div class="ooo-license-tab ooo-beta-section">
					<h3><?php 
                esc_html_e('Beta Release', 'dynamic-ooo');
                ?></h3>
					<form action="" method="post" class="ooo-beta-form">
						<?php 
                wp_nonce_field($this->plugin_utils_manager->get_config('prefix') . '-settings-page', $this->plugin_utils_manager->get_config('prefix') . '-settings-page');
                ?>
						<label>
							<input type="checkbox" name="enable_beta" value="1" <?php 
                checked($beta_enabled);
                ?>>
							<?php 
                esc_html_e('Enable beta releases. Important: Do not use in production, consider this only for staging sites.', 'dynamic-ooo');
                ?>
						</label>
						<input type="hidden" name="beta_status" value="1">
						<?php 
                submit_button(esc_html__('Save my preference', 'dynamic-ooo'), 'secondary', 'submit', \false);
                ?>
					</form>
				</div>
				<?php 
            }
            ?>

				<?php 
            $this->maybe_render_rollback();
            ?>
			<?php 
        }
        ?>
		</div>
		<?php 
    }
    /**
     * @return void
     */
    protected function maybe_render_rollback()
    {
        if (!$this->plugin_utils_manager->get_supports_rollback()) {
            return;
        }
        ?>
		<div class="ooo-license-tab ooo-rollback-section">
			<h3><?php 
        esc_html_e('Rollback Version', 'dynamic-ooo');
        ?></h3>
			<?php 
        $rollback_versions = $this->plugin_utils_manager->rollback->get_rollback_versions();
        $current_version = $this->plugin_utils_manager->get_config('version');
        ?>
			<h4><?php 
        \printf(esc_html__('Your current version: %s', 'dynamic-ooo'), $current_version);
        ?></h4>
			<p>
				<?php 
        \printf(esc_html__('Experiencing an issue with %1$s version %2$s? Rollback to a previous version before the issue appeared.', 'dynamic-ooo'), $this->plugin_utils_manager->get_config('product_name'), $current_version);
        ?>
			</p>

			<?php 
        if (!empty($rollback_versions)) {
            ?>
				<form id="ooo-rollback-form" class="ooo-rollback-form">
					<?php 
            wp_nonce_field($this->plugin_utils_manager->get_config('prefix') . '_plugin_rollback', 'rollback_nonce');
            ?>
					<select name="version">
						<?php 
            foreach ($rollback_versions as $version) {
                ?>
							<option value="<?php 
                echo esc_attr($version);
                ?>">
								<?php 
                echo esc_html($version);
                ?>
							</option>
						<?php 
            }
            ?>
					</select>
					<?php 
            submit_button(esc_html__('Rollback now', 'dynamic-ooo'), 'secondary', 'submit', \false);
            ?>
					<span class="spinner" style="float: none; margin: 0 0 0 5px;"></span>
				</form>
				<p class="description">
					<?php 
            esc_html_e('Warning: Please backup your site and database before making the rollback.', 'dynamic-ooo');
            ?>
				</p>
			<?php 
        } else {
            ?>
				<p><?php 
            esc_html_e('No versions available for rollback.', 'dynamic-ooo');
            ?></p>
			<?php 
        }
        ?>
		</div>
		<script>
		jQuery(document).ready(function($) {
			$('#ooo-rollback-form').on('submit', function(e) {
				e.preventDefault();
				
				if (!confirm('<?php 
        echo esc_js(__('Are you sure you want to rollback?', 'dynamic-ooo'));
        ?>')) {
					return;
				}
				
				var $form = $(this);
				var $spinner = $form.find('.spinner');
				var $submit = $form.find('button[type="submit"]');
				
				$spinner.addClass('is-active');
				$submit.prop('disabled', true);
				
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {
						action: '<?php 
        echo esc_js($this->plugin_utils_manager->get_config('prefix'));
        ?>_rollback_plugin',
						version: $form.find('select[name="version"]').val(),
						nonce: $form.find('[name="rollback_nonce"]').val()
					},
					success: function(response) {
						if (response.success) {
							alert(response.data);
							location.reload();
						} else {
							alert('Error: ' + (response.data || 'Unknown error'));
						}
					},
					error: function(xhr, status, error) {
						var errorMessage;
						try {
							var response = JSON.parse(xhr.responseText);
							errorMessage = response.message || response.data || xhr.responseText;
						} catch(e) {
							errorMessage = xhr.responseText || error || 'Unknown error';
						}
						alert('Error: ' + errorMessage);
						console.error('Full error details:', {
							status: status,
							error: error,
							xhr: xhr.responseText
						});
					},
					complete: function() {
						$spinner.removeClass('is-active');
						$submit.prop('disabled', false);
					}
				});
			});
		});
		</script>
		<?php 
    }
}
