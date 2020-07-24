<?php
/*
 * Template Name: Vehicles
 */
global $wpdb;
$vehicles_table = $wpdb->prefix.'vehicles';
$locations_table = $wpdb->prefix.'locations';

$sql = "SELECT " . $locations_table . ".id as location_id, make, model, image_1, sku, available FROM "
    . $vehicles_table . " INNER JOIN "
    . $locations_table . " ON "
    . $vehicles_table . ".id = "
    . $locations_table . ".vehicle_id";
$results = $wpdb->get_results($sql);
$sql = "SELECT DISTINCT year from ".$vehicles_table;
$results_year = $wpdb->get_results($sql);
$sql = "SELECT DISTINCT location from ".$locations_table;
$results_location = $wpdb->get_results($sql);
$sql = "SELECT DISTINCT make from ".$vehicles_table;
$results_make = $wpdb->get_results($sql);
$sql = "SELECT model from ".$vehicles_table;
$results_model = $wpdb->get_results($sql);
get_header();
?>
<div class="content">
    <div class="filters-container">
        <div class="row-filters">
            <div class="filter-container">
                <label for="year-filter" style="margin-right: 10px">Year:</label>
                <select name="" id="year-filter" class="">
                    <option value="">None</option>
                    <?php foreach($results_year as $year): ?>
                        <option value="<?= $year->year ?>"> <?= $year->year ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-container">
                <label for="location-filter" style="margin-right: 10px">Location:</label>
                <select name="" id="location-filter" class="">
                    <option value="">None</option>
                    <?php foreach($results_location as $location): ?>
                        <option value="<?= $location->location ?>"> <?= $location->location ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row-filters">
            <div class="filter-container">
                <label for="make-filter" style="margin-right: 10px">Make:</label>
                <select name="" id="make-filter" class="">
                    <option value="">None</option>
                    <?php foreach($results_make as $make): ?>
                        <option value="<?= $make->make ?>"> <?= $make->make ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-container">
                <label for="model-filter" style="margin-right: 10px">Model:</label>
                <select name="" id="model-filter" class="">
                    <option value="">None</option>
                    <?php foreach($results_model as $model): ?>
                        <option value="<?= $model->model ?>"> <?= $model->model ?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row-filters">
            <div class="filter-container">
                <button id="filter-button">Apply Filters</button>
            </div>
        </div>
    </div>
    <div class="vehicles-container">
        <?php foreach($results as $result): ?>
        <div class="vehicle" id ="<?= $result->location_id ?>">
            <img class="vehicle-avatar" src="<?= $result->image_1 ?>" alt="">
            <span class="vehicle-details"><?= "Make: " . $result->make ?></span>
            <span class="vehicle-details"><?= "Model: " . $result->model ?></span>
            <span class="vehicle-details"><?= "SKU: " . $result->sku ?></span>
            <?php $available = ((int) $result->available) ? "yes" : "no"?>
            <span class="vehicle-details"><?= "Available: " . $available  ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
get_footer();
?>
