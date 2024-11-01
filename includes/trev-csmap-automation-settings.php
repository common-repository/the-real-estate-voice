
<div class="wrap-automation">
	<div class="automation-settings-title">
		<h5><?php esc_html_e('Your automation settings','the-real-estate-voice') ?></h5>
	</div>
		
			
			
				<?php if(trev_csmap_is_pro_license_plugin() ){ ?>
					<ul class="pro-version">
						<li>
							
					<?php 
						$auto_post_options = get_option( 'auto_post_option_name' );

							if ( isset( $auto_post_options['auto_enabled_1'] ) && !empty( $auto_post_options['auto_enabled_1'] ) ) {
								$checked = $auto_post_options['auto_enabled_1'];
							} else {
								$checked = 'no';
							}

							?>
							<input type="checkbox" name="auto_post_option_name[auto_enabled_1]"  id="auto_post_article" value="yes" <?php checked( $checked, 'yes', true ); ?> />
							<label for="auto_post_article"><?php esc_html_e( 'Yes automatically publish new articles on my website', 'the-real-estate-voice' ); ?></label>
							
						
						</li>
						<li>
							
							<?php 
							$auto_social_options 	= get_option( 'auto_post_option_name' );

							$selectedPost = ( isset( $auto_social_options['enabled_sharing_for']['post'] ) && 1 == $auto_social_options['enabled_sharing_for']['post'] ) ? 'checked' : '';	?>

							<input type="checkbox" class="toggle_share" id="toggle_share_article" <?php echo esc_attr( $selectedPost ); ?>  name="auto_post_option_name[enabled_sharing_for][<?php echo esc_attr( 'post' ); ?>]" value="1">
						
							<label for="toggle_share_article">
								<?php esc_html_e( 'Yes automatically share the articles to Facebook', 'the-real-estate-voice' ); ?>
							</label>


						
						</li>
						<li>
							
						<?php 
							$auto_social_options = get_option( 'auto_post_option_name' );

							$selectedListing = ( isset( $auto_social_options['enabled_sharing_for']['listing'] ) && 1 == $auto_social_options['enabled_sharing_for']['listing'] ) ? 'checked' : '';	?>

							<input type="checkbox" class="toggle_share" id="toggle_share_listing" <?php echo esc_attr( $selectedListing ); ?>  name="auto_post_option_name[enabled_sharing_for][<?php echo esc_attr( 'listing' ); ?>]" value="1">

							<label for="toggle_share_listing">
								<?php esc_html_e( 'Yes automatically share new listings to Facebook', 'the-real-estate-voice' ); ?>
							</label>	
							
						</li>
						<li>
							<input type="checkbox" id="instagram_days" disabled>
							<label for="instagram_days" class="tooltip">
								<?php esc_html_e( 'Yes automatically share important days to Instagram ', 'the-real-estate-voice' ); ?>
								<span class="tooltiptext"><?php esc_html_e('Coming Soon')?></span>
							</label>
						</li>
						<li>
							<input type="checkbox" id="instagram_share" disabled>
							<label for="instagram_share" class="tooltip">
								<?php esc_html_e( 'Yes automatically share new listings to Instagram', 'the-real-estate-voice' ); ?>
								<span class="tooltiptext"><?php esc_html_e('Coming Soon')?></span>
							</label>
						</li>

					</ul>

					<p class="see-all-settings">
						<a href="<?php echo esc_url( admin_url('admin.php?page=trev-csmap-settings') )?>" title="<?php echo esc_attr('See all settings')?>">
							<?php esc_html_e('See all settings','the-real-estate-voice');?>
						</a>
					</p>

					<div class="trev-automation-settings">
					
						<p class="submit">
							<a href="<?php echo esc_url( admin_url('admin.php?page=trev-csmap-calendar&tab=scheduled'))?>" class="" ><?php esc_attr_e('See Schedule'); ?></a>
						</p>

					</div>
				<?php }else{ ?>
					<ul class="free-version">
						<li>
							<input type="checkbox" id="auto_enabled_1" class="disabled">
							<label for="">
								<?php esc_html_e( 'Yes automatically publish new articles on my website', 'the-real-estate-voice' ); ?></label>
						</li>
						<li>
							<input type="checkbox" id="auto_share_enabled_0" class="disabled">
							<label for="">
								<?php esc_html_e( 'Yes automatically share the articles to Facebook', 'the-real-estate-voice' ); ?>
							</label>
						</li>
						<li>
							<input type="checkbox" id="listing" class="disabled">
							<label for="">
								<?php esc_html_e( 'Yes automatically share new listings to Facebook', 'the-real-estate-voice' ); ?>
							</label>	
						</li>
						<li>
							<input type="checkbox" id="instagram_days" class="disabled">
							<label for="" class="tooltip">
								<?php esc_html_e( 'Yes automatically share important days to Instagram ', 'the-real-estate-voice' ); ?>
									  <span class="tooltiptext"><?php esc_html_e('Coming Soon')?></span>
								</label>
						</li>
						<li>
							<input type="checkbox" id="instagram_share" class="disabled">
							<label for="" class="tooltip">
								<?php esc_html_e( 'Yes automatically share new listings to Instagram', 'the-real-estate-voice' ); ?>
									  <span class="tooltiptext"><?php esc_html_e('Coming Soon')?></span>
								</label>
						</li>
					</ul>
				<?php } ?>
			
		
		
	
</div>