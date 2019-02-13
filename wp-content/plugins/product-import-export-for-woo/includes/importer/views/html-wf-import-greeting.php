<?php

	$ftp_server = '';
	$ftp_user = '';
	$ftp_password = '';
	$use_ftps = '';
	$enable_ftp_ie = '';	
	$ftp_server_path = '';	
	if(!empty($ftp_settings)){
		$ftp_server = $ftp_settings[ 'ftp_server' ];
		$ftp_user = $ftp_settings[ 'ftp_user' ];
		$ftp_password = $ftp_settings[ 'ftp_password' ];
		$use_ftps = $ftp_settings[ 'use_ftps' ];
		$enable_ftp_ie = $ftp_settings[ 'enable_ftp_ie' ];
		$ftp_server_path = $ftp_settings[ 'ftp_server_path' ];				
	}

?>
<div>
	<p><?php _e( 'You can import products (in CSV format) in to the shop using any of below methods.', 'wf_csv_import_export' ); ?></p>

	<?php if ( ! empty( $upload_dir['error'] ) ) : ?>
		<div class="error"><p><?php _e('Before you can upload your import file, you will need to fix the following error:'); ?></p>
		<p><strong><?php echo $upload_dir['error']; ?></strong></p></div>
	<?php else : ?>
		<form enctype="multipart/form-data" id="import-upload-form" method="post" action="<?php echo esc_attr(wp_nonce_url($action, 'import-upload')); ?>">
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="upload"><?php _e( 'Method 1: Select a file from your computer' ); ?></label>
						</th>
						<td>
							<input type="file" id="upload" name="import" size="25" />
							<input type="hidden" name="action" value="save" />
							<input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
							<small><?php printf( __('Maximum size: %s' ), $size ); ?></small>
						</td>
					</tr>
					<tr>
						<th><label><?php _e( 'Delimiter', 'wf_csv_import_export' ); ?></label><br/></th>
						<td><input type="text" name="delimiter" placeholder="," size="2" /></td>
					</tr>
					<tr>
						<th><label><?php _e( 'Merge empty cells', 'wf_csv_import_export' ); ?></label><br/></th>
						<td><input type="checkbox" name="merge_empty_cells" placeholder="," size="2" /> <span class="description"><?php _e( 'Check this box to merge empty cells - otherwise (when merging) the empty cells will be ignored when importing things such as attributes.', 'wf_csv_import_export' ); ?></span></td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Upload file and import' ); ?>" />
			</p>
		</form>
	<?php endif; ?>
</div>