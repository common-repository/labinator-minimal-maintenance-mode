<?php
/**
 * Content of the "Maintenance Mode" page.
 *
 * @package WordPress
 * @subpackage Lmm
 */

if ( ! defined( 'LMM_ADMIN_INIT' ) ) {
	exit;
} ?>

<div class="wrap lmm-wrap lmm-section-wrap">
	<div class="wp-header-end"></div><!-- admin notices go after .wp-header-end or .wrap>h2:first-child -->
	<h1><?php esc_html_e( 'Labinator Minimal Maintenance Mode', 'lmm' ); ?></h1>

	<div class="labinator-marketplace-calltoaction">
	    <div class="desc">
	            <?php esc_html_e('All your WordPress needs in one package! 📦 Learn more →', 'lmm');?>
	    </div>
	    <a class="btn" href="https://labinator.com/wordpress-marketplace/" target="_blank" rel="noopener noreferrer nofollow">Labinator WordPress Marketplace</a>
    </div>

<form id="mache-maintenance-options" action="" method="POST">

<?php wp_nonce_field( 'lmm_save_maintenance' ); ?>
<input type="hidden" name="lmm-maintenance-saved" value="true">

	<table class="form-table">
		<tr>
		<th scope="row"><?php esc_html_e( 'Set site status', 'lmm' ); ?></th>
		<td><fieldset>
			<label>
				<input name="site_status" value="online" type="radio"
				<?php checked( $this->settings['site_status'], 'online', true ); ?>>
				<?php echo wp_kses( __( '<strong>Online</strong> — WordPress works as usual', 'lmm' ), array( 'strong' => array() ) ); ?>
			</label><br>

			<label>
				<input name="site_status" value="coming_soon" type="radio"
				<?php checked( $this->settings['site_status'], 'coming_soon' ); ?>>
				<?php echo wp_kses( __( '<strong>Coming soon</strong> — Site closed. All pages have a meta robots noindex, nofollow', 'lmm' ), array( 'strong' => array() ) ); ?>
			</label><br>
			<label>
				<input name="site_status" value="maintenance" type="radio"
				<?php checked( $this->settings['site_status'], 'maintenance' ); ?>>
				<?php echo wp_kses( __( '<strong>Maintenance</strong> — Site closed. All pages return 503 Service unavailable', 'lmm' ), array( 'strong' => array() ) ); ?>
			</label><br>
		</fieldset></td>
		</tr>

		<tr valign="top"><th scope="row"><?php esc_html_e( 'Magic Link', 'lmm' ); ?></th>
			<td>
				<input type="hidden" name="token" id="token_fld" value="<?php echo esc_attr( $this->settings['token'] ); ?>">
				<a href="<?php echo esc_url( $this->magic_url ); ?>" id="lmm_magic_link"><?php echo esc_url( $this->magic_url ); ?></a>
				<button name="copy_token" id="mct_copy_token_btn" class="button"><?php esc_html_e( 'copy to clipboard', 'lmm' ); ?></button>
				<button name="change_token" id="change_token_btn" class="button action"><?php esc_html_e( 'change secret token', 'lmm' ); ?></button>
		<p class="description"><?php esc_html_e( 'You can use this link to grant anyone access to the website when it is in maintenance mode.', 'lmm' ); ?></p>

			</td>
		</tr>

		<tr valign="top"><th scope="row"><?php esc_html_e( 'Choose a page for the content', 'lmm' ); ?></th>
			<td>
				<select name="page_id" id="page_id_fld">
					<option value=""><?php esc_html_e( 'Use default content', 'lmm' ); ?></option>
					<?php
					$lmm_pages = get_pages();
					foreach ( $lmm_pages as $lmm_page ) {
						echo '<option value="' . esc_attr( $lmm_page->ID ) . '" ' .
						selected( $lmm_page->ID, $this->settings['page_id'] ) . '>' .
						esc_html( $lmm_page->post_title ) .
						'</option>';
					}
					?>
				</select>

				<a href="<?php echo esc_url( $this->preview_url ); ?>" target="lmm_preview" id="preview_maintenance_btn" class="button action"><?php esc_html_e( 'Preview', 'lmm' ); ?></a>
			</td>
		</tr>

	</table>

<?php submit_button(); ?>

</form>

</div>


<script>

(function($){

	var lmm_preview_base_url = '<?php echo esc_url( $this->preview_base_url ); ?>';
	var lmm_magic_base_url   = '<?php echo esc_url( $this->magic_base_url ); ?>';

	var random_token = function(){
		var chrs = '0123456789ABCDEF';
		var token = '';
		for (var i = 0, n = 12; i < n; i++){
			token += chrs.substr(Math.round(Math.random()*15),1);
		}
		return token;
	}

	$('#mct_copy_token_btn').click(function(e){
		navigator.clipboard.writeText( $('#lmm_magic_link').attr('href') ).then(
			() => { mct_toast_copy_token() },
			() => { console.log('There was an error copying to clipboard.'); },
		); 
		e.preventDefault();
	});

	var mct_toast_copy_token = function(){
		$('#mct_copy_token_btn').html('<?php esc_html_e( 'copied to clipboard!', 'lmm' ); ?>');
		setTimeout(function() {
			$('#mct_copy_token_btn').html('<?php esc_html_e( 'copy to clipboard', 'lmm' ); ?>');
		}, 1000);
	}

	$('#change_token_btn').click(function(e){

		if (confirm('<?php esc_html_e( 'Are you sure you want to change the secret token?\nThis will invalidate previously-shared links.', 'lmm' ); ?>')){
			var new_token = random_token();
			var new_magic_url = lmm_magic_base_url + new_token;

			$('#lmm_magic_link').attr('href',new_magic_url).html(new_magic_url);
			$('#token_fld').val(new_token);
		}
		e.preventDefault();
		return;
	});

	$('#page_id_fld').change(function(e){
		var content_page_id = '';
		if (content_page_id = $('#page_id_fld option:selected').val()){

			$('#preview_maintenance_btn').attr('href',lmm_preview_base_url+'&mct_page_id='+content_page_id);
			console.log(content_page_id);
		}else{
			$('#preview_maintenance_btn').attr('href', lmm_preview_base_url);
			console.log('vacío');
		}

	});

	$('button.handlediv').click(function( e ){
		$( this ).parent().find('.hndle').click();
	});

	$('.lmm-helpbox .hndle').click(function( e ){
		var container  = $( this ).parent();

		if (container.attr('data-collapsed') == 'false'){
			close_helpbox(container);
		} else {
			open_helpbox(container);
		}

	});

	var close_helpbox  = function(elem){
		elem.attr('data-collapsed','true');
		elem.find('div.inside').hide();
		elem.find('.toggle-indicator span').attr('class','dashicons dashicons-arrow-down');
	}
	var open_helpbox  = function(elem){
		elem.attr('data-collapsed','false');
		elem.find('div.inside').show();
		elem.find('.toggle-indicator span').attr('class','dashicons dashicons-arrow-up');
	}

	$('.lmm-helpbox').each(function (index){
		close_helpbox($( this ));
	});


	$('#mache-maintenance-options').submit(function( e ) {

	});
})(jQuery);
</script>
