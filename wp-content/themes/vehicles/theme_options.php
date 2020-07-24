<?php
/**
 * Create A Simple Theme Options Panel
 */

if (!class_exists('AdminPanel')) {

    class AdminPanel
    {
        private static $year_results;
        private static $make_results;
        private static $model_results;
        private static $sku_results;
        private static $location_results;

        /**
         * AdminPanel constructor.
         */
        public function __construct()
        {
            global $wpdb;
            $prefix = $wpdb->prefix;

            if (is_admin()) {
                add_action('admin_menu', ['AdminPanel', 'add_admin_menu']);
                add_action( 'admin_init', [ 'AdminPanel', 'register_settings' ] );
            }
            self::$year_results = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT year FROM " . $prefix ."vehicles"));
            self::$make_results = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT make FROM " . $prefix ."vehicles"));
            self::$model_results = $wpdb->get_results($wpdb->prepare("SELECT model FROM " . $prefix ."vehicles"));
            self::$sku_results = $wpdb->get_results($wpdb->prepare("SELECT sku FROM " . $prefix ."locations"));
            self::$location_results = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT location FROM " . $prefix ."locations"));
        }

        /**
         * @return bool|mixed|void
         */
        public static function get_theme_options()
        {
            return get_option('theme_options');
        }

        /**
         * @param $id
         * @return mixed
         */
        public static function get_theme_option($id)
        {
            $options = self::get_theme_options();
            if (isset($options[$id])) {
                return $options[$id];
            }
        }

        public static function add_admin_menu()
        {
            add_menu_page(
                'Vehicle Settings',
                'Vehicle Settings',
                'manage_options',
                'theme-settings',
                array('AdminPanel', 'create_admin_page')
            );
        }

        public static function register_settings()
        {
            register_setting('theme_options', 'theme_options', ['AdminPanel', 'sanitize']);
        }

        public static function sanitize($options)
        {
            return $options;
        }

        public static function create_admin_page() { ?>

            <div class="wrap">

                <form method="post" id="admin_panel_form" action="options.php">

                    <h1>Vehicle Details</h1>

                    <?php settings_fields( 'theme_options' ); ?>

                    <table class="form-table vehicle-details">

                        <?php // Checkbox example ?>
                        <tr valign="top">
                            <th scope="row">Price Type</th>
                            <td>
                                <?php $value = self::get_theme_option( 'price_type' ); ?>
                                <select name="theme_options[price_type]" id="price_type">
                                    <?php
                                    $options = [
                                        'regular' => 'Regular',
                                        'sale' => 'Sale'
                                    ];
                                    foreach ( $options as $id => $label ) { ?>
                                        <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $value, $id, true ); ?>>
                                            <?php echo strip_tags( $label ); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <th scope="row">Price Currency</th>
                            <td>
                                <?php $value = self::get_theme_option( 'price_currency' ); ?>
                                <select name="theme_options[price_currency]" id="price_currency">
                                    <?php
                                    $options = [
                                        'USD' => '$',
                                        'EUR' => '&#8364;'
                                    ];
                                    foreach ( $options as $id => $label ) { ?>
                                        <option value="<?php echo esc_attr( $id ); ?>" <?php selected( $value, $id, true ); ?>>
                                            <?php echo strip_tags( $label ); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <th scope="row">Price Value</th>
                            <td>
                                <?php $value = self::get_theme_option( 'price_value' ); ?>
                                <input type="number" name="theme_options[price_value]" value="<?php echo esc_attr( $value ); ?>" id="price_value">
                            </td>
                        </tr>

                        <?php // Text input example ?>
                        <tr valign="top">
                            <th scope="row">Sold</th>
                            <td>
                                <?php $value = self::get_theme_option( 'sold_yes' ); ?>
                                <input type="checkbox" name="theme_options[sold_yes]" <?php checked( $value, 'on' ); ?> class="checkable checkable_sold" id="sold_yes"> <span>Yes</span>
                                <?php $value = self::get_theme_option( 'sold_no' ); ?>
                                <input type="checkbox" name="theme_options[sold_no]" <?php checked( $value, 'on' ); ?> class="checkable checkable_sold" id="sold_no"> <span>No</span>
                            </td>
                            <th scope="row">Arriving Soon</th>
                            <td>
                                <?php $value = self::get_theme_option( 'arraving_yes' ); ?>
                                <input type="checkbox" name="theme_options[arraving_yes]" <?php checked( $value, 'on' ); ?> class="checkable checkable_arrive" id="arriving_yes"> <span>Yes</span>
                                <?php $value = self::get_theme_option( 'arraving_no' ); ?>
                                <input type="checkbox" name="theme_options[arraving_no]" <?php checked( $value, 'on' ); ?> class="checkable checkable_arrive" id="arriving_no"> <span>No</span>
                            </td>
                            <th scope="row">Available</th>
                            <td>
                                <?php $value = self::get_theme_option( 'available_yes' ); ?>
                                <input type="checkbox" name="theme_options[available_yes]" <?php checked( $value, 'on' ); ?> class="checkable checkable_available" id="available_yes"> <span>Yes</span>
                                <?php $value = self::get_theme_option( 'available_no' ); ?>
                                <input type="checkbox" name="theme_options[available_no]" <?php checked( $value, 'on' ); ?> class="checkable checkable_available" id="available_no"> <span>No</span>
                            </td>
                        </tr>

                    </table>
                    <hr>
                    <h1>Vehicle Meta</h1>

                    <table class="form-table vehicle-meta">

                        <?php // Checkbox example ?>
                        <tr valign="top">
                            <th scope="row">Year</th>
                            <td>
                                <?php $value = self::get_theme_option( 'year' );?>
                                <select class="selectable" name="theme_options[year]" id="year_dropdown">
                                    <?php
                                    foreach ( self::$year_results as $id => $year ) { ?>
                                        <option value="<?php echo esc_attr( $year->year ); ?>" <?php echo $value === $year->year ? 'selected' : ''; ?>>
                                            <?php echo strip_tags( $year->year ); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <th scope="row">Add Year</th>
                            <td>
                                <?php $value = self::get_theme_option( 'input_add_year' ); ?>
                                <input type="number" name="theme_options[input_add_year]" id="add_year_input" value="<?php echo esc_attr( $value ); ?>">
                                <input type="button" name="theme_options[button_add_year]" id="add_year_btn" value="Add Year">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Make</th>
                            <td>
                                <?php $value = self::get_theme_option( 'make' ); ?>
                                <select class="selectable" name="theme_options[make]" id="make_dropdown">
                                    <?php
                                    $options = [];
                                    foreach ( self::$make_results as $id => $make ) { ?>
                                        <option value="<?php echo esc_attr( $make->make ); ?>" <?php echo $value === $make->make ? 'selected' : ''; ?>>
                                            <?php echo strip_tags( $make->make ); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <th scope="row">Add Make</th>
                            <td>
                                <?php $value = self::get_theme_option( 'input_add_make' ); ?>
                                <input type="text" name="theme_options[input_add_make]" id="add_make_input" value="<?php echo esc_attr( $value ); ?>">
                                <input type="button" name="theme_options[button_add_make]" id="add_make_btn" value="Add Make">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Model</th>
                            <td>
                                <?php $value = self::get_theme_option( 'model' ); ?>
                                <select class="selectable" name="theme_options[model]" id="model_dropdown">
                                    <?php
                                    $options = [];
                                    foreach ( self::$model_results as $id => $model ) { ?>
                                        <option value="<?php echo esc_attr( $model->model ); ?>" <?php echo $value === $model->model ? 'selected' : ''; ?>>
                                            <?php echo strip_tags( $model->model ); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <th scope="row">Add Model</th>
                            <td>
                                <?php $value = self::get_theme_option( 'input_add_model' ); ?>
                                <input type="text" name="theme_options[input_add_model]" id="add_model_input" value="<?php echo esc_attr( $value ); ?>">
                                <input type="button" name="theme_options[button_add_model]" id="add_model_btn" value="Add Model">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">SKU</th>
                            <td>
                                <?php $value = self::get_theme_option( 'sku' ); ?>
                                <select class="selectable" name="theme_options[sku]" id="sku_dropdown">
                                    <?php
                                    $options = [];
                                    foreach ( self::$sku_results as $id => $sku ) { ?>
                                        <option value="<?php echo esc_attr( $sku->sku ); ?>" <?php echo $value === $sku->sku ? 'selected' : ''; ?>>
                                            <?php echo strip_tags( $sku->sku ); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <th scope="row">Add SKU</th>
                            <td>
                                <?php $value = self::get_theme_option( 'input_add_sku' ); ?>
                                <input type="text" name="theme_options[input_add_sku]" id="add_sku_input" value="<?php echo esc_attr( $value ); ?>">
                                <input type="button" name="theme_options[button_add_sku]" id="add_sku_btn" value="Add SKU">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Location</th>
                            <td>
                                <?php $value = self::get_theme_option( 'location' ); ?>
                                <select class="selectable" name="theme_options[location]" id="location_dropdown">
                                    <?php
                                    $options = [];
                                    foreach ( self::$location_results as $id => $location ) { ?>
                                        <option value="<?php echo esc_attr( $location->location ); ?>" <?php echo $value === $location->location ? 'selected' : ''; ?>>
                                            <?php echo strip_tags( $location->location ); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                            <th scope="row">Add Location</th>
                            <td>
                                <?php $value = self::get_theme_option( 'input_add_location' ); ?>
                                <input type="text" name="theme_options[input_add_location]" id="add_location_input" value="<?php echo esc_attr( $value ); ?>">
                                <input type="button" name="theme_options[button_add_location]" id="add_location_btn" value="Add Location">
                            </td>
                        </tr>
                    </table>
                    <hr>
                    <h1>Description</h1>
                    <table class="form-table description">
                        <tr valign="top">
                            <th>Add Description</th>
                            <td>
                                <?php $value = self::get_theme_option( 'description' ); ?>
                                <textarea name="theme_options[description]" id="description" cols="50" rows="10"><?=$value?></textarea>
                            </td>
                        </tr>
                    </table>
                    <hr>
                    <h1>Images</h1>
                    <table class="form-table description">
                        <tr valign="top">
                            <th>Add Image Paths</th>
                            <td>
                                <?php $value = self::get_theme_option( 'image_1' ); ?>
                                <input type="text" name="theme_options[image_1]" value="<?php echo esc_attr( $value ); ?>" id="image_1" style="width: 40%">
                                <?php $value = self::get_theme_option( 'image_2' ); ?>
                                <input type="text" name="theme_options[image_2]" value="<?php echo esc_attr( $value ); ?>" id="image_2" style="width: 40%">
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>

                </form>

            </div><!-- .wrap -->
        <?php }

    }

}
