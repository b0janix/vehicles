<?php
require_once 'theme_options.php';
require_once 'vehicles_api.php';

function create_vehicles_table()
{
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $charset_collate = $wpdb->get_charset_collate();
    $prefix = $wpdb->prefix;
    $table_name = $prefix . "vehicles";
    $version = (int) get_site_option('vehicles_db_version');
    $defaultImageUrl = site_url().'/wp-content/uploads/2020/07/sport-car-vector-icon-illustration_138676-341.jpg';

    if ($version < 1) {
        $sql ="CREATE TABLE " . $table_name . " (
        id int(11) NOT NULL auto_increment,
        year smallint(4) unsigned NOT NULL,
        make varchar(50) NOT NULL,
        model varchar(100) NOT NULL,
        price_type tinyint(1) unsigned NOT NULL, 
        price_currency char(3) NOT NULL, 
        price_value int(11) unsigned NOT NULL, 
        sold tinyint(1) unsigned NOT NULL, 
        arriving tinyint(1) unsigned NOT NULL, 
        available tinyint(1) unsigned NOT NULL,
        description varchar(2500) NOT NULL DEFAULT 'No description',
        image_1 varchar(500) NOT NULL DEFAULT '" . $defaultImageUrl . "',
        image_2 varchar(500) NOT NULL DEFAULT '" . $defaultImageUrl . "',
        PRIMARY KEY  (id),
        UNIQUE KEY vehicles_model_key (model)
        ) " . $charset_collate . ";";
        dbDelta($sql);
        update_site_option( 'vehicles_db_version', 1 );
    }
}

create_vehicles_table();

function create_locations_table()
{
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $charset_collate = $wpdb->get_charset_collate();
    $prefix = $wpdb->prefix;
    $table_name = $prefix . "locations";
    $version = (int) get_site_option('locations_db_version');

    if ($version < 1) {
        $sql = "CREATE TABLE " . $table_name . " (
        id int(11) NOT NULL auto_increment,
        sku varchar(20) NOT NULL,
        location varchar(200) NOT NULL,
        vehicle_id int(11) unsigned NOT NULL,
        PRIMARY KEY  (id),
        KEY locations_vehicle_id_key (vehicle_id),
        UNIQUE KEY locations_sku_key (sku)
        ) " . $charset_collate . ";";
        dbDelta($sql);
        update_site_option( 'locations_db_version', 1 );
    }
}

create_locations_table();

register_activation_hook( __FILE__, 'create_locations_table' );

add_action('wp_enqueue_scripts', 'vehicle_files');

function vehicle_files() {
    wp_enqueue_style('vehicle_styles', get_stylesheet_uri());
    wp_enqueue_script('vehicle-js-scripts', get_template_directory_uri() .'/main.js', array('jquery'), null, true);
}

new AdminPanel();

function ap_get_theme_option( $id = '' ) {
    return AdminPanel::get_theme_option( $id );
}

function ap_get_theme_options( $id = '' ) {
    return AdminPanel::get_theme_options(  );
}

add_action( 'admin_enqueue_scripts', 'load_admin_script' );

function load_admin_script() {
    wp_enqueue_script('admin_js_script', get_template_directory_uri() .'/admin.js', array('jquery'), null, true);
    $dataToBePassed = [
        'theme_options' => ap_get_theme_options(),
    ];
    wp_localize_script( 'admin_js_script', 'php_vars', $dataToBePassed );
}

$api = new VehiclesApi();
$api->hook_api();

