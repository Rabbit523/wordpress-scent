<div class="tool-box">
    <h3 class="title"><?php _e('Export Product in CSV Format:', 'wf_csv_import_export'); ?></h3>
    <p><?php _e('Export and download your products in CSV format. This file can be used to import products back into your Woocommerce shop.', 'wf_csv_import_export'); ?></p>
    <form action="<?php echo admin_url('admin.php?page=wf_woocommerce_csv_im_ex&action=export'); ?>" method="post">

        <table class="form-table">
            <tr>
                <th>
                    <label for="v_offset"><?php _e('Offset', 'wf_csv_import_export'); ?></label>
                </th>
                <td>
                    <input type="text" name="offset" id="v_offset" placeholder="<?php _e('0', 'wf_csv_import_export'); ?>" class="input-text" />
                </td>
            </tr>
            <tr>
                <th>
                    <label for="v_limit"><?php _e('Limit', 'wf_csv_import_export'); ?></label>
                </th>
                <td>
                    <input type="text" name="limit" id="v_limit" placeholder="<?php _e('Unlimited', 'wf_csv_import_export'); ?>" class="input-text" />
                </td>
            </tr>
            <tr>
                <th>
                    <label for="v_columns"><?php _e('Columns', 'wf_csv_import_export'); ?></label>
                </th>
            <table id="datagrid">
                <th style="text-align: left;">
                    <label for="v_columns"><?php _e('Column', 'wf_csv_import_export'); ?></label>
                </th>
                <th style="text-align: left;">
                    <label for="v_columns_name"><?php _e('Column Name', 'wf_csv_import_export'); ?></label>
                </th>
                <?php 
                $post_columns['images'] = 'Images (featured and gallery)';
                $post_columns['file_paths'] = 'Downloadable file paths';
                $post_columns['taxonomies'] = 'Taxonomies (cat/tags/shipping-class)';
                $post_columns['attributes'] = 'Attributes';
                ?>
                <?php foreach ($post_columns as $pkey => $pcolumn) {
                            
                         ?>
            <tr>
                <td>
                    <input name= "columns[<?php echo $pkey; ?>]" type="checkbox" value="<?php echo $pkey; ?>" checked>
                    <label for="columns[<?php echo $pkey; ?>]"><?php _e($pcolumn, 'wf_csv_import_export'); ?></label>
                </td>
                <td>
                    <?php 
                    $tmpkey = $pkey;
                    if (strpos($pkey, 'yoast') === false) {
                            $tmpkey = ltrim($pkey, '_');
                        }
                    ?>
                     <input type="text" name="columns_name[<?php echo $pkey; ?>]"  value="<?php echo $tmpkey; ?>" class="input-text" />
                </td>
            </tr>
                <?php } ?>
                
            </table><br/>
            </tr>
            
            <tr>
                <th>
                    <label for="v_include_hidden_meta"><?php _e('Include hidden meta data', 'wf_csv_import_export'); ?></label>
                </th>
                <td>
                    <input type="checkbox" name="include_hidden_meta" id="v_include_hidden_meta" class="checkbox" />
                </td>
            </tr>
        </table>
        <p class="submit"><input type="submit" class="button button-primary" value="<?php _e('Export Products', 'wf_csv_import_export'); ?>" /></p>
    </form>
</div>