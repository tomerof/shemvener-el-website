<?php

add_filter( 'plugins_list', 'ambundle_handle_plugin_display' );
function ambundle_handle_plugin_display( $plugins ) {
	if ( array_key_exists( 'mustuse', $plugins ) && array_key_exists( 'am-bundle.php', $plugins['mustuse'] ) ) {
		unset( $plugins['mustuse']['am-bundle.php'] );
	}
	return $plugins;
}

add_filter( 'aioseo_upgrade_link', function( $url ) {
    return 'https://www.shareasale.com/r.cfm?b=1491200&u=410666&m=94778';
});

add_filter( 'wpforms_upgrade_link', function( $url ) {
    return 'https://www.shareasale.com/r.cfm?b=834775&u=410666&m=64312';
});

add_filter( 'monsterinsights_shareasale_id', function( $id ) {
    return '410666';
});

add_filter('duplicator_disable_onboarding_redirect', '__return_true');
add_filter('duplicator_upsell_url_filter', 'modify_upsell_url', 20);
function modify_upsell_url($url){
    return 'https://duplicator.com/?affid=316';
}

update_option( 'optin_monster_api_activation_redirect_disabled', true );
update_option( 'optinmonster_sas_id', '410666' );