<?php

class VehiclesApi {
    public $my_namespace = 'vehicles/v';
    public $my_version   = '1';

    public function register_routes()
    {
        $namespace = $this->my_namespace . $this->my_version;
        $base = 'insert';
        register_rest_route(
            $namespace,
            '/' . $base,
            [
                'methods' => "POST",
                'callback' => [$this, 'insert_vehicle']
            ]
        );
        $base = 'filter';
        register_rest_route(
            $namespace,
            '/' . $base,
            [
                'methods' => "POST",
                'callback' => [$this, 'filter_vehicle']
            ]
        );
    }

    public function hook_api()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function insert_vehicle(WP_REST_Request $request)
    {
        global $wpdb;
        $params = $request->get_params();
        $price_type = $params['price_type'] ?? 0;
        $price_currency = $params['price_currency'] ?? "USD";
        $description = $params['description'] ?? '';
        $image_1 = $params['image_1'] ?? '';
        $image_2 = $params['image_2'] ?? '';
        $required_params = [
            "sold", "arrive", "available", "year", "make", "model", "sku", "location", "price_value"
        ];
        foreach($required_params as $required_param) {

            if(!isset($params[$required_param])) {
                return ['status' => 0, 'message' => "Required param " . $required_param . " not set"];
            }

            if(in_array($required_param, ["sold", "arrive", "available"])) {
                $tinyint_param = (int) $params[$required_param];
                if(!in_array($tinyint_param, [0,1])) {
                    return ['status' => 0, 'message' => "Invalid checkbox value"];
                }
            }

            if($required_param === "year") {
                $year_param = (int) $params[$required_param];
                $current_year = (int) date('Y');
                if($year_param < 1886 && $year_param > $current_year) {
                    return ['status' => 0, 'message' => "Invalid year value"];
                }
            }

            if($required_param === "make" && (strlen($params[$required_param]) < 2 ||  strlen($params[$required_param]) > 50)) {
                return ['status' => 0, 'message' => "Invalid make value"];
            }

            if($required_param === "model" && (strlen($params[$required_param]) < 2 ||  strlen($params[$required_param]) > 100)) {
                return ['status' => 0, 'message' => "Invalid model value"];
            }

            if($required_param === "sku" && (strlen($params[$required_param]) < 8 ||  strlen($params[$required_param]) > 12)) {
                return ['status' => 0, 'message' => "Invalid sku value"];
            }

            if($required_param === "location" && (strlen($params[$required_param]) < 2 ||  strlen($params[$required_param]) > 200)) {
                return ['status' => 0, 'message' => "Invalid location value"];
            }

            if($required_param === "price_value") {
                $price_value = (int) $params[$required_param];
                if($price_value < 500 || $price_value > 10000000) {
                    return ['status' => 0, 'message' => "Invalid price value"];
                }

            }

        }

        $table_name_1 = $wpdb->prefix.'vehicles';
        $data_1 = [
            'price_type' => $price_type,
            'price_currency' => $price_currency,
            'price_value' => $params['price_value'],
            'sold' => $params['sold'],
            'arriving' => $params['arrive'],
            'available' => $params['available'],
            'year' => $params['year'],
            'make' => $params['make'],
            'model' => $params['model']
        ];

        if($description) {
            $data_1['description'] = $description;
        }
        if($image_1) {
            $data_1['image_1'] = $image_1;
        }
        if($image_2) {
            $data_1['image_2'] = $image_2;
        }

        $format_1 = ['%d','%s','%d','%d','%d','%d','%d','%s','%s','%s','%s'];

        $table_name_2 = $wpdb->prefix.'locations';
        $data_2 = ['sku' => $params['sku'], 'location' => $params['location'], 'vehicle_id' => 0];
        $format_2 = ['%s','%s','%d'];
        try {
            $result = $wpdb->get_results($wpdb->prepare("SELECT id FROM `".$table_name_1."` WHERE `make` = %s AND `model` = %s", $params['make'], $params['model']));
            if (isset($result[0]->id)) {
                $vehicle_id = $result[0]->id;
                $where_1 = ['id' => $result[0]->id];
                $where_format_1 = ['%d'];
                $wpdb->update($table_name_1, $data_1, $where_1, $format_1, $where_format_1);
                $result = $wpdb->get_results($wpdb->prepare("SELECT id FROM `".$table_name_2."` WHERE `vehicle_id` = %d AND `sku` = %s", $vehicle_id, $params['sku']));
                if (isset($result[0]->id)) {

                    $data_2['vehicle_id'] = $vehicle_id;
                    $where_2 = ['id' => $result[0]->id];
                    $where_format_2 = ['%d'];
                    $wpdb->update($table_name_2, $data_2, $where_2, $format_2, $where_format_2);
                    return ['status' => 1, 'message' => 'Vehicle updated'];

                }
                $data_2['vehicle_id'] = $vehicle_id;
                $wpdb->insert($table_name_2,$data_2,$format_2);
                return ['status' => 1, 'message' => 'New sku inserted'];
            }

            $results = $wpdb->get_results($wpdb->prepare("SELECT `sku` FROM `" . $table_name_2 . "`"));

            foreach ($results as $result) {
                if($result->sku === $params['sku'] ) {
                    return ['status' => 0, 'message' => "The sku already exists"];
                }
            }

            $wpdb->insert($table_name_1,$data_1,$format_1);
            $vehicle_id = $wpdb->insert_id;
            $data_2['vehicle_id'] = $vehicle_id;
            $wpdb->insert($table_name_2,$data_2,$format_2);
            return ['status' => 1, 'message' => 'Vehicle inserted'];
        } catch (Exception $e) {
            return ['status' => 0, 'message' => $e->getMessage()];
        }
    }

    public function filter_vehicle(WP_REST_Request $request)
    {
        global $wpdb;
        $params = $request->get_params();
        $variables = [];
        $table_name_1 = $wpdb->prefix.'vehicles';
        $table_name_2 = $wpdb->prefix.'locations';

        $sql = "SELECT " . $table_name_2 . ".id as location_id FROM "
            . $table_name_1 . " INNER JOIN "
            . $table_name_2 . " ON "
            . $table_name_1 . ".id = "
            . $table_name_2 . ".vehicle_id WHERE ";

        if(!empty($params["year-filter"])) {
            $sql .= "year = %d AND ";
            $variables[] = $params["year-filter"];
        }

        if(!empty($params["location-filter"])) {
            $sql .= " location = %s AND ";
            $variables[] = $params["location-filter"];
        }

        if(!empty($params["make-filter"])) {
            $sql .= "make = %s AND ";
            $variables[] = $params["make-filter"];
        }

        if(!empty($params["model-filter"])) {
            $sql .= "model = %s AND ";
            $variables[] = $params["model-filter"];
        }

        $arr = str_split($sql);
        array_splice($arr,-4);
        $sql = implode('', $arr);

        $results = $wpdb->get_results($wpdb->prepare($sql, $variables));

        $ids = [];
        foreach($results as $result) {
            $ids[] = $result->location_id;
        }

        return $ids;

    }

}