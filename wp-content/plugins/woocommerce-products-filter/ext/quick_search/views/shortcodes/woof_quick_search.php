<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
global $WOOF;
?>
<div class="woof">
    <div class="woof_quick_search_wraper <?php echo $class ?>">
        <input id="woof_quick_search_form" class="form-control woof_quick_search_wraper_textinput" data-text_group_logic="<?php echo $text_group_logic ?>" data-term_logic="<?php echo $term_logic ?>" data-tax_logic="<?php echo $tax_logic ?>"  data-target-link="<?php echo $target ?>" data-preload="<?php echo $preload ?>" data-extended="<?php echo $extended_filter ?>" placeholder="<?php echo $placeholder ?>" >

        <?php
        if ($extended_filter) {
            if ($price_filter == 1) {

                wp_enqueue_script('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/js/ion-rangeSlider/ion.rangeSlider.min.js', array('jquery'));
                wp_enqueue_style('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/css/ion.rangeSlider.css');
                $ion_slider_skin = 'skinNice';
                if (isset($this->settings['ion_slider_skin'])) {
                    $ion_slider_skin = $this->settings['ion_slider_skin'];
                }
                wp_enqueue_style('ion.range-slider-skin', WOOF_LINK . 'js/ion.range-slider/css/ion.rangeSlider.' . $ion_slider_skin . '.css');
                //***
                $additional_taxes = "";
                $min_price = $preset_min = WOOF_HELPER::get_min_price($additional_taxes);
                $max_price = $preset_max = WOOF_HELPER::get_max_price($additional_taxes);
                if (wc_tax_enabled() && 'incl' === get_option('woocommerce_tax_display_shop') && !wc_prices_include_tax()) {
                    $tax_classes = array_merge(array(''), WC_Tax::get_tax_classes());
                    $class_max = $max_price;
                    foreach ($tax_classes as $tax_class) {
                        if ($tax_rates = WC_Tax::get_rates($tax_class)) {
                            $class_max = ceil($max_price + WC_Tax::get_tax_total(WC_Tax::calc_exclusive_tax($max_price, $tax_rates)));
                        }
                    }

                    $max_price = $class_max;
                }

                if (class_exists('WOOCS')) {
                    $preset_min = apply_filters('woocs_exchange_value', $preset_min);
                    $preset_max = apply_filters('woocs_exchange_value', $preset_max);
                    $min_price = apply_filters('woocs_exchange_value', $min_price);
                    $max_price = apply_filters('woocs_exchange_value', $max_price);
                }
                //***
                $slider_step = 1;
                //***
                //esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) )
                $slider_prefix = '';
                $slider_postfix = '';
                if (class_exists('WOOCS')) {
                    global $WOOCS;
                    $currencies = $WOOCS->get_currencies();
                    $currency_pos = 'left';
                    if (isset($currencies[$WOOCS->current_currency])) {
                        $currency_pos = $currencies[$WOOCS->current_currency]['position'];
                    }
                } else {
                    $currency_pos = get_option('woocommerce_currency_pos');
                }
                switch ($currency_pos) {
                    case 'left':
                        $slider_prefix = get_woocommerce_currency_symbol();
                        break;
                    case 'left_space':
                        $slider_prefix = get_woocommerce_currency_symbol() . ' ';
                        break;
                    case 'right':
                        $slider_postfix = get_woocommerce_currency_symbol();
                        break;
                    case 'right_space':
                        $slider_postfix = ' ' . get_woocommerce_currency_symbol();
                        break;

                    default:
                        break;
                }

                //***
                //https://wordpress.org/support/topic/results-found/
                if ($preset_max < $max_price) {
                    $max = $max_price;
                } else {
                    $max = $preset_max;
                }
                if ($preset_min > $min_price) {
                    $min = $min_price;
                } else {
                    $min = $preset_min;
                }
                ?>
                <div class="woof_qt_add_filter ">
                    <input class="woof_qt_price_slider"  data-min="<?php echo $min ?>" data-max="<?php echo $max ?>" data-min-now="<?php echo $min_price ?>" data-max-now="<?php echo $max_price ?>" data-step="<?php echo $slider_step ?>" data-slider-prefix="<?php echo $slider_prefix ?>" data-slider-postfix="<?php echo $slider_postfix ?>" value="" />
                </div>
                <?php
            }
            if ($add_filters !== '') {
                $filter_items = array();
                $filter_items = explode(',', $add_filters);
                if (!empty($filter_items)) {
                    $filter_items = array_slice($filter_items, 0, 1);
                }
                $filter_custom_title = array();
                $filter_title = explode(',', $filter_title);
                foreach ($filter_title as $title_itm) {
                    $temp_title = explode(':', $title_itm);
                    if (isset($temp_title[1])) {
                        $filter_custom_title[$temp_title[0]] = $temp_title[1];
                    }
                }
                $taxonomy_info = "";

                foreach ($filter_items as $item) {
                    $filter_struct = array();
                    $terms = array();
                    $filter_struct = explode(':', $item);
                    if (!isset($filter_struct[1])) {
                        continue;
                    }
                    $args = array(
                        'taxonomy' => $filter_struct[1],
                        'hide_empty' => true,
                    );
                    if ($exclude_terms != '') {
                        $args['exclude'] = $exclude_terms;
                    }
                    $terms = get_terms($args);

                    if (!is_array($terms)) {
                        continue;
                    }

                    $taxonomy_info = WOOF_HELPER::wpml_translate(get_taxonomy($filter_struct[1]), (isset($filter_custom_title[$filter_struct[1]]) ? $filter_custom_title[$filter_struct[1]] : ""));
                    switch ($filter_struct[0]) {
                        case 'multi-drop-down':
                            ?>
                            <div class="woof_qt_add_filter woof_qt_add_filter_multiselect_<?php echo $filter_struct[1] ?>">
                                <<?php echo apply_filters('woof_title_tag', 'h4'); ?>><?php echo $taxonomy_info ?></<?php echo apply_filters('woof_title_tag', 'h4'); ?>>
                                <select class="woof_qt_select tax_<?php echo $filter_struct[1] ?>" data-placeholder="<?php echo $taxonomy_info ?>"  data-tax="<?php echo $filter_struct[1] ?>" multiple="multiple" >
                                    <?php
                                    foreach ($terms as $term) {
                                        ?>
                                        <option value="<?php echo $term->term_id ?>"><?php echo $term->name ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <?php
                            break;
                        case 'drop-down':
                            ?>
                            <div class="woof_qt_add_filter woof_qt_add_filter_select_<?php echo $filter_struct[1] ?>">
                                <<?php echo apply_filters('woof_title_tag', 'h4'); ?>><?php echo $taxonomy_info ?></<?php echo apply_filters('woof_title_tag', 'h4'); ?>>
                                <select class="woof_qt_select tax_<?php echo $filter_struct[1] ?>" data-tax="<?php echo $filter_struct[1] ?>">
                                    <option value="-1"><?php _e('Any', 'woocommerce-products-filter') ?></option>
                                    <?php
                                    foreach ($terms as $term) {
                                        ?>
                                        <option value="<?php echo $term->term_id ?>"><?php echo $term->name ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <?php
                            break;
                        case 'checkbox':
                            ?>
                            <div class="woof_qt_add_filter woof_qt_add_filter_checkbox woof_qt_add_filter_checkbox_<?php echo $filter_struct[1] ?>">
                                <<?php echo apply_filters('woof_title_tag', 'h4'); ?>><?php echo $taxonomy_info ?></<?php echo apply_filters('woof_title_tag', 'h4'); ?>>
                                <?php
                                foreach ($terms as $term) {
                                    ?>
                                    <div class="woof_qt_item_container">
                                        <input type="checkbox" name="woof_qt_check_<?php echo $filter_struct[1] ?>" class="woof_qt_checkbox tax_<?php echo $filter_struct[1] ?>" data-tax="<?php echo $filter_struct[1] ?>"value="<?php echo $term->term_id ?>" >
                                        <label class="woof_qt_checkbox_label"><?php echo $term->name ?></label>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            break;
                        case 'radio':
                            ?>
                            <div class="woof_qt_add_filter woof_qt_add_filter_radio woof_qt_add_filter_radio_<?php echo $filter_struct[1] ?>">
                                <<?php echo apply_filters('woof_title_tag', 'h4'); ?>><?php echo $taxonomy_info ?></<?php echo apply_filters('woof_title_tag', 'h4'); ?>>
                                <?php
                                foreach ($terms as $term) {
                                    $unique_id= uniqid();
                                    ?>
                                    <div class="woof_qt_item_container">
                                        <input type="radio" id="term_<?php echo $unique_id ?>" name="woof_qt_radio_<?php echo $filter_struct[1] ?>" class="woof_qt_radio tax_<?php echo $filter_struct[1] ?>" data-tax="<?php echo $filter_struct[1] ?>" value="<?php echo $term->term_id ?>" >
                                        <label class="woof_qt_radio_label" for="term_<?php echo $unique_id ?>"><?php echo $term->name ?>
                                            <span class="woof_qt_radio_reset tax_<?php echo $filter_struct[1] ?>_reset" data-tax="<?php echo $filter_struct[1] ?>" ><img src="<?php echo WOOF_LINK ?>img/delete.png" height="12" width="12" alt="" /></span>
                                        </label>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            break;
                        default :
                            break;
                    }
                }
            }
            if ($reset_btn == 1) {
                ?>
                <div class="woof_qt_reset_filter_con">
                    <button class="woof_qt_reset_filter_btn"><?php echo $reset_text ?></button>
                </div>
                <?php
            }
        }
        ?>
    </div>  
</div>       
