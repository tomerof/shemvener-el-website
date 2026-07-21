<?php

namespace DynamicOOO\PluginUtils;

if (!\defined('ABSPATH')) {
    exit;
}
interface LicenseInterface
{
    /**
     * @return void
     */
    public function init();
    /**
     * @param bool $fresh
     * @return bool
     */
    public function is_license_active($fresh = \false);
    /**
     * @return array{0:bool,1:string}
     */
    public function deactivate_license();
    /**
     * @param string $key
     * @return array{0:bool,1:string}
     */
    public function activate_new_license_key($key);
    /**
     * @return void
     */
    public function activate_beta_releases();
    /**
     * @return void
     */
    public function deactivate_beta_releases();
    /**
     * @return string
     */
    public function get_license_error();
    /**
     * @return string
     */
    public function get_license_key();
    /**
     * @return string
     */
    public function get_license_key_last_4_digits();
    /**
     * @return string
     */
    public function get_last_active_domain();
    /**
     * @param string $domain
     * @return void
     */
    public function set_last_active_domain($domain);
    /**
     * @return string
     */
    public function get_current_domain();
    /**
     * @return void
     */
    public function refresh_license_status();
    /**
     * @return void
     */
    public function refresh_and_repair_license_status();
    /**
     * @return void
     */
    public function domain_mismatch_check();
}
