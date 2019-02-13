<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Rev_addon_gal
 * @subpackage Rev_addon_gal/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div id="eg-wrap" class="view_wrapper">
	<div id="" class='wrap'>
		<div class="clear_both"></div>
		<h2 class="topheader"></h2>
		
		<div class="clear_both"></div>

		<div class="title_line sub_title">
			<div id="icon-options-configure" class="icon32"></div> 
			<span>Install &amp; Configure Add-ons<a href="?page=essgrid_addon&amp;checkforupdates=true" class="esg-reload-shop"><i class="fa-icon-refresh"></i>Check for new Add-ons</a></span>
		</div>

	<div style="width:100%;height:40px"></div>
		<span id="ajax_essgrid_addon_nonce" class="hidden"><?php echo wp_create_nonce( 'ajax_essgrid_addon_nonce' ) ?></span>
		<div class="esg-dashboard esg-dash-addons">


		<?php 
	
			//load $addons from repository
			$addons = get_option('essential-addons');
			$addons = (array)$addons;
			$addons = apply_filters( 'eg_addons_filter', $addons );

			$plugins = get_plugins();

			foreach($addons as $addon){
				if(version_compare(Essential_Grid::VERSION, $addon->version_from, '<') || version_compare(Essential_Grid::VERSION, $addon->version_to, '>')){
					continue;
				}
				if( empty($addon->title) ) continue;
				
				$eg_dash_background_style = !empty($addon->background) ? 'style="background-image: url('.$addon->background.');"' : "";
				?>
				<!-- <?php echo $addon->slug; ?> WIDGET -->
					<div class="esg-dash-widget <?php echo $addon->slug; ?>" <?php echo $eg_dash_background_style; ?>>
						<div class="esg-dash-title-wrap">
							<div class="esg-dash-title"><?php echo $addon->title; ?></div>
							<?php 
								//Plugin Status
								$eg_addon_not_activated = $eg_addon_activated = $eg_addon_not_installed = 'style="display:none"';
								$eg_addon_version = "";
								if (array_key_exists($addon->slug.'/'.$addon->slug.'.php', $plugins)) {
									if (is_plugin_inactive($addon->slug.'/'.$addon->slug.'.php')) {
										$eg_addon_not_activated = 'style="display:block"'; 									
									} else {
										$eg_addon_activated = 'style="display:block"';
									}
									$eg_addon_version = $plugins[$addon->slug.'/'.$addon->slug.'.php']['Version'];
								} else { 
									$eg_addon_not_installed = 'style="display:block"';
								}
							
								//Check for registered slider
								$eg_addon_validated = get_option('tp_eg_valid', 'false');
								//TODO: check validated
								$eg_addon_validated = $eg_addon_validated=='true' ? true : false;
								//$eg_addon_validated = true;

								if($eg_addon_validated){
							?>
									<div class="esg-dash-title-button esg-status-orange" <?php echo $eg_addon_not_activated; ?> data-plugin="<?php echo $addon->slug.'/'.$addon->slug.'.php';?>" data-alternative="<i class='icon-no-problem-found'></i>Activate"><i class="icon-update-refresh"></i><?php _e("Not Active", EG_TEXTDOMAIN); ?></div>
									<div class="esg-dash-button-gray esg-dash-deactivate-addon esg-dash-title-button" <?php echo $eg_addon_activated; ?> data-plugin="<?php echo $addon->slug.'/'.$addon->slug.'.php';?>" data-alternative="<i class='icon-update-refresh'></i>Deactivate"><i class="icon-update-refresh"></i><?php _e("Deactivate", EG_TEXTDOMAIN); ?></div>
									<div class=" esg-dash-title-button esg-status-green" <?php echo $eg_addon_activated; ?> data-plugin="<?php echo $addon->slug.'/'.$addon->slug.'.php';?>" data-alternative="<i class='icon-update-refresh'></i>Deactivate"><i class="icon-no-problem-found"></i><?php _e("Active", EG_TEXTDOMAIN); ?></div>
									<div class=" esg-dash-title-button esg-status-red" <?php echo $eg_addon_not_installed; ?> data-alternative="<i class='icon-update-refresh'></i>Install" data-plugin="<?php echo $addon->slug;?>"><i class="icon-not-registered"></i><?php _e("Not Installed", EG_TEXTDOMAIN); ?></div>
							<?php } else { 
									$eg_addon_version="";
									$result = deactivate_plugins( $addon->slug.'/'.$addon->slug.'.php' );
							?>
									<div class="esg-dash-title-button esg-status-red" style="display:block"><i class="icon-not-registered"></i><?php _e("Add-on locked", EG_TEXTDOMAIN); ?></div>
							<?php }
							?>
						</div>
						<div class="esg-dash-widget-inner esg-dash-widget-registered">
							
							<div class="esg-dash-content">
								<div class="esg-dash-strong-content"><?php echo $addon->line_1; ?></div>
								<div><?php echo $addon->line_2; ?></div>				
							</div>
							<div class="esg-dash-content-space"></div>
							<?php if(!empty($eg_addon_version)){ ?>
								<div class="esg-dash-version-info">
									<div class="esg-dash-strong-content ">
										<?php _e('Installed Version',EG_TEXTDOMAIN); ?>
									</div>
									<?php 
										//$eg_addon_version = strtoupper($addon->slug."_VERSION"); 
										echo $eg_addon_version;
										$eg_addon = "";
									?>
								</div>
							<?php } ?>
							<div class="esg-dash-version-info">
								<div class="esg-dash-strong-content esg-dash-version-info">
									<?php _e('Available Version',EG_TEXTDOMAIN); ?>
								</div>
								<?php echo $addon->available; ?>
							</div>
							<?php if(!empty($eg_addon_version)){ ?>
							<div class="esg-dash-content-space"></div>	
							<a class="esg-dash-invers-button" href="?page=essgrid_addon&amp;checkforupdates=true" id="eg_check_version"><?php _e('Check for Update',EG_TEXTDOMAIN); ?></a>
							<div class="esg-dash-content-space"></div>
							<?php } ?>
							<div class="esg-dash-bottom-wrapper">
								<?php if(!empty($eg_addon_version)){ ?>
									<?php 
										if( version_compare($eg_addon_version, $addon->available) >= 0 ){ ?>
											<span class="esg-dash-button-gray"><?php _e('Up to date',EG_TEXTDOMAIN); ?></span>							
									<?php
										} else { ?>
										    <a href="update-core.php?checkforupdates=true" class="esg-dash-button"><?php _e('Update Now', EG_TEXTDOMAIN); ?></a>							
									<?php	
										}
									?>
								<?php } else { 
										if($eg_addon_validated){?>
										<span data-plugin="<?php echo $addon->slug;?>" class="esg-addon-not-installed esg-dash-button"><?php _e('Install this Add-on', EG_TEXTDOMAIN); ?></span>
								<?php 
										} else { ?>
											<a href="<?php echo admin_url( 'admin.php?page=essential-grid');?>" class="esg-dash-button"><?php _e('Register Essential Grid', EG_TEXTDOMAIN); ?></a>
									<?php 
										}
									} ?>
									
								<?php if(!empty($addon->button) && $eg_addon_validated && !empty($eg_addon_version) ){  // && !empty($rev_addon_code)
										if($eg_addon_activated=='style="display:block"'){
								?>		
											<span <?php echo $eg_addon_activated=='style="display:none"' ? $eg_addon_activated : ''; ?> href="javascript:void(0)" class="esg-dash-button esg-dash-action-button esg-dash-margin-left-10" id="esg-dash-addons-slide-out-trigger_<?php echo $addon->slug; ?>"><?php echo $addon->button; ?></span>				
								<?php 	} else {?>
											<span data-plugin="<?php echo $addon->slug.'/'.$addon->slug.'.php';?>" class="esg-addon-not-activated esg-dash-button esg-dash-action-button esg-dash-margin-left-10" id="esg-dash-addons-slide-out-trigger_<?php echo $addon->slug; ?>"><?php _e('Activate Plugin',EG_TEXTDOMAIN); ?></span>
								<?php 	}  
									} ?>
							</div>
						</div>		
						
					</div>
				<!-- END OF <?php echo $addon->slug; ?> WIDGET -->
				<?php
			} // end foreach
		?>

			<div class="tp-clearfix"></div>
		<!--/div>
	</div>
</div-->		
<!-- SOURCE SLIDE OUT SETTINGS -->
<?php apply_filters( 'eg_addon_dash_slideouts',''); ?>
<!--End Add-On Area--> 
</div> </div>
<div id="waitaminute">
	<div class="waitaminute-message"><i class="eg-icon-emo-coffee"></i><br><?php _e("Please Wait...", EG_TEXTDOMAIN); ?></div>
</div>