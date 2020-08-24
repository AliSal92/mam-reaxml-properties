<?php
$data = apply_filters('mam-property-form-data', 'buy');
$optionsActive = '';
if ((isset($_GET['price_from']) && $_GET['price_from'] != '') || (isset($_GET['price_to']) && $_GET['price_to'] != '') ||
    (isset($_GET['bed']) && $_GET['bed'] != '1') || (isset($_GET['bath']) && $_GET['bath'] != '1') ||
    (isset($_GET['car']) && $_GET['car'] != '1') || (isset($_GET['type']) && $_GET['type'] != '') ){
    $optionsActive = 'active';
}
?>
<div class="mam-property-form-container">
    <form method="get" action="">
        <input type="hidden" name="sort" value="<?php if(isset($_GET['sort'])){ echo $_GET['sort'];} ?>">
        <div class="row search-main">

            <div class="col-md-8">
                <div class="form-group">
                    <select id="suburb" name="suburb" class="form-control selectpicker" title="<?php _e('Suburb'); ?>">
                        <?php foreach ($data['suburb'] as $option) {
                            if (!$option) continue; ?>
                            <?php if (isset($_GET['suburb']) && $_GET['suburb'] == $option) { ?>
                                <option selected value="<?php echo $option; ?>"><?php echo $option; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <button type="button" class="btn btn-info toggle-search-options"><?php echo _e('More Options'); ?> <i class="fas fa-caret-down"></i></button>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-search"><i class="fas fa-search"></i> <?php echo _e('Search'); ?></button>
                </div>
            </div>
        </div>

        <div class="row search-options <?php echo $optionsActive; ?>">
            <div class="col-md-2">
                <div class="form-group">
                    <select id="price_from" name="price_from" class="form-control selectpicker" title="<?php _e('Rent Per Week From'); ?>">
                        <?php foreach ($data['price-from'] as $value => $option) {
                            if (!$option) continue; ?>
                            <?php if (isset($_GET['price_from']) && $_GET['price_from'] == $value) { ?>
                                <option selected value="<?php echo $value; ?>"><?php echo $option; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $value; ?>"><?php echo $option; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <select id="price_to" name="price_to" class="form-control selectpicker" title="<?php _e('Rent Per Week To'); ?>">
                        <?php foreach ($data['price-from'] as $value => $option) {
                            if (!$option) continue; ?>
                            <?php if (isset($_GET['price_to']) && $_GET['price_to'] == $value) { ?>
                                <option selected value="<?php echo $value; ?>"><?php echo $option; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $value; ?>"><?php echo $option; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <select id="bed" name="bed" class="form-control selectpicker" title="<?php _e('Bed'); ?>">
                        <?php foreach ($data['bed'] as $value => $option) {
                            if (!$option) continue; ?>
                            <?php if (isset($_GET['bed']) && $_GET['bed'] == $value) { ?>
                                <option selected value="<?php echo $value; ?>"><?php echo $option; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $value; ?>"><?php echo $option; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <select id="bath" name="bath" class="form-control selectpicker" title="<?php _e('Bath'); ?>">
                        <?php foreach ($data['bath'] as $value => $option) {
                            if (!$option) continue; ?>
                            <?php if (isset($_GET['bath']) && $_GET['bath'] == $value) { ?>
                                <option selected value="<?php echo $value; ?>"><?php echo $option; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $value; ?>"><?php echo $option; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <select id="car" name="car" class="form-control selectpicker" title="<?php _e('Car'); ?>">
                        <?php foreach ($data['car'] as $value => $option) {
                            if (!$option) continue; ?>
                            <?php if (isset($_GET['car']) && $_GET['car'] == $value) { ?>
                                <option selected value="<?php echo $value; ?>"><?php echo $option; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $value; ?>"><?php echo $option; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <select id="type" name="type[]" multiple class="form-control selectpicker" title="<?php _e('Type'); ?>">
                        <?php foreach ($data['type'] as $value => $option) {
                            if (!$option) continue; ?>
                            <?php if (isset($_GET['type']) && in_array($value, $_GET['type'])) { ?>
                                <option selected value="<?php echo $value; ?>"><?php echo $option; ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $value; ?>"><?php echo $option; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
            </div>

        </div>

    </form>
</div>
