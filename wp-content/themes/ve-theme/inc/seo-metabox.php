<?php

add_action( 'add_meta_boxes', 'bc_seo_add_metabox' );
function bc_seo_add_metabox() {
	$post_types = [ 'post', 'page', 'bc_location', 'bc_quote_author' ];
	add_meta_box(
		'bc_seo',
		'SEO',
		'bc_seo_metabox_html',
		$post_types,
		'normal',
		'high'
	);
}

function bc_seo_metabox_html( $post ) {
	$meta_title       = get_post_meta( $post->ID, '_bc_meta_title', true );
	$meta_description = get_post_meta( $post->ID, '_bc_meta_description', true );
	wp_nonce_field( 'bc_seo_save', 'bc_seo_nonce' );
	?>
	<p>
		<label for="bc_meta_title" style="display:block;font-weight:600;margin-bottom:4px;">
			Título SEO (sobreescribe <code>&lt;title&gt;</code>)
		</label>
		<input type="text" id="bc_meta_title" name="bc_meta_title"
			value="<?php echo esc_attr( $meta_title ); ?>" class="large-text"
			placeholder="Dejar vacío para usar el título por defecto">
		<span class="description" id="bc_meta_title_desc">
			Recomendado: 50–60 caracteres. Actual: <span id="bc_seo_title_count"><?php echo strlen( $meta_title ); ?></span>
		</span>
	</p>
	<p>
		<label for="bc_meta_description" style="display:block;font-weight:600;margin-bottom:4px;">
			Meta descripción (sobreescribe la descripción por defecto)
		</label>
		<textarea id="bc_meta_description" name="bc_meta_description"
			class="large-text" rows="3"
			placeholder="Dejar vacío para usar el extracto o un resumen automático"
			oninput="document.getElementById('bc_seo_desc_count').textContent=this.value.length"><?php echo esc_textarea( $meta_description ); ?></textarea>
		<span class="description">
			Recomendado: 120–160 caracteres. Actual: <span id="bc_seo_desc_count"><?php echo strlen( $meta_description ); ?></span>
		</span>
	</p>
	<script>
	jQuery(function($) {
		$('#bc_meta_title').on('input', function() {
			$('#bc_seo_title_count').text(this.value.length);
		});
	});
	</script>
	<?php
}

add_action( 'save_post', 'bc_seo_save_metabox' );
function bc_seo_save_metabox( $post_id ) {
	if ( ! isset( $_POST['bc_seo_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['bc_seo_nonce'], 'bc_seo_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$meta_title = isset( $_POST['bc_meta_title'] )
		? sanitize_text_field( $_POST['bc_meta_title'] )
		: '';
	$meta_description = isset( $_POST['bc_meta_description'] )
		? sanitize_textarea_field( $_POST['bc_meta_description'] )
		: '';

	if ( $meta_title ) {
		update_post_meta( $post_id, '_bc_meta_title', $meta_title );
	} else {
		delete_post_meta( $post_id, '_bc_meta_title' );
	}

	if ( $meta_description ) {
		update_post_meta( $post_id, '_bc_meta_description', $meta_description );
	} else {
		delete_post_meta( $post_id, '_bc_meta_description' );
	}
}

function bc_get_meta_title( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$meta_title = get_post_meta( $post_id, '_bc_meta_title', true );
	return $meta_title ?: '';
}

function bc_get_meta_description( $post_id = 0 ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$meta_description = get_post_meta( $post_id, '_bc_meta_description', true );
	return $meta_description ?: '';
}
