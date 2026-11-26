<?php
/** Meta boxes for resources (file, version) and firmware (product, version, kind, date, notes). */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function apr_meta_boxes() {
    add_meta_box( 'apr_resource_file', 'Resource file', 'apr_resource_file_box', 'apr_resource', 'normal', 'high' );
    add_meta_box( 'apr_firmware_details', 'Release details', 'apr_firmware_box', 'apr_firmware', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'apr_meta_boxes' );

function apr_resource_file_box( $post ) {
    wp_nonce_field( 'apr_save_meta', 'apr_meta_nonce' );
    $file_id  = (int) get_post_meta( $post->ID, '_apr_file_id', true );
    $version  = get_post_meta( $post->ID, '_apr_version', true );
    $external = get_post_meta( $post->ID, '_apr_external_url', true );
    $file     = $file_id ? (string) wp_get_attachment_url( $file_id ) : '';
    ?>
    <p><label><strong>Upload or choose a file</strong></label><br>
    <input type="hidden" name="apr_file_id" id="apr_file_id" value="<?php echo esc_attr( $file_id ); ?>">
    <button type="button" class="button" id="apr_pick_file">Choose file</button>
    <button type="button" class="button-link" id="apr_clear_file" style="margin-left:8px<?php echo $file_id ? '' : ';display:none'; ?>">Remove file</button>
    <span id="apr_file_name" style="margin-left:8px"><?php echo $file ? esc_html( basename( $file ) ) : 'No file selected'; ?></span></p>
    <p><label for="apr_external_url"><strong>…or link to an external URL</strong></label><br>
    <input type="url" class="widefat" id="apr_external_url" name="apr_external_url" value="<?php echo esc_attr( $external ); ?>" placeholder="https://"></p>
    <p><label for="apr_version"><strong>Document version / date</strong></label><br>
    <input type="text" class="regular-text" id="apr_version" name="apr_version" value="<?php echo esc_attr( $version ); ?>" placeholder="e.g. Rev 3, Feb 2026"></p>
    <p class="description">Set <em>Audience → Partners only</em> to require login. Partner-only files are served through a protected URL, not a direct uploads link.</p>
    <script>
    jQuery(function($){
      $('#apr_pick_file').on('click', function(e){ e.preventDefault(); var f = wp.media({ title: 'Select resource file', multiple: false }); f.on('select', function(){ var a = f.state().get('selection').first().toJSON(); $('#apr_file_id').val(a.id); $('#apr_file_name').text(a.filename); $('#apr_clear_file').show(); }); f.open(); });
      $('#apr_clear_file').on('click', function(e){ e.preventDefault(); $('#apr_file_id').val('0'); $('#apr_file_name').text('No file selected'); $(this).hide(); });
    });
    </script>
    <?php
}

function apr_firmware_box( $post ) {
    wp_nonce_field( 'apr_save_meta', 'apr_meta_nonce' );
    $v       = fn( $k ) => get_post_meta( $post->ID, $k, true );
    $kind    = $v( '_apr_kind' ) ?: 'Feature';
    $date    = $v( '_apr_fw_date' ) ?: current_time( 'Y-m-d' );
    $file_id = (int) $v( '_apr_file_id' );
    $file    = $file_id ? (string) wp_get_attachment_url( $file_id ) : '';
    ?>
    <table class="form-table">
      <tr><th><label for="apr_fw_version">Version</label></th><td><input type="text" id="apr_fw_version" name="apr_fw_version" value="<?php echo esc_attr( $v( '_apr_fw_version' ) ); ?>" placeholder="2.1.0" class="regular-text"></td></tr>
      <tr><th><label for="apr_fw_date">Release date</label></th><td><input type="date" id="apr_fw_date" name="apr_fw_date" value="<?php echo esc_attr( $date ); ?>"></td></tr>
      <tr><th><label for="apr_kind">Type</label></th><td><select id="apr_kind" name="apr_kind"><?php foreach ( array( 'Feature', 'Fix', 'Security' ) as $k ) { printf( '<option %s>%s</option>', selected( $kind, $k, false ), esc_html( $k ) ); } ?></select></td></tr>
      <tr><th>Firmware file (optional)</th><td><input type="hidden" name="apr_file_id" id="apr_file_id" value="<?php echo esc_attr( $file_id ); ?>"><button type="button" class="button" id="apr_pick_file">Choose file</button> <button type="button" class="button-link" id="apr_clear_file" style="margin-left:8px<?php echo $file_id ? '' : ';display:none'; ?>">Remove file</button> <span id="apr_file_name" style="margin-left:8px"><?php echo $file ? esc_html( basename( $file ) ) : 'No file'; ?></span><p class="description">Set Audience → Partners only so only logged-in partners can download it. Otherwise partners request it through support.</p></td></tr>
    </table>
    <p class="description">Use the main editor above for release notes (one bullet per line is fine). Tag a <em>Related product</em> so it appears on that product page.</p>
    <script>
    jQuery(function($){
      $('#apr_pick_file').on('click', function(e){ e.preventDefault(); var f = wp.media({ title: 'Select firmware file', multiple: false }); f.on('select', function(){ var a = f.state().get('selection').first().toJSON(); $('#apr_file_id').val(a.id); $('#apr_file_name').text(a.filename); $('#apr_clear_file').show(); }); f.open(); });
      $('#apr_clear_file').on('click', function(e){ e.preventDefault(); $('#apr_file_id').val('0'); $('#apr_file_name').text('No file'); $(this).hide(); });
    });
    </script>
    <?php
}

function apr_save_meta( $post_id ) {
    if ( ! isset( $_POST['apr_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['apr_meta_nonce'] ) ), 'apr_save_meta' ) ) { return; }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
    if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
    $fields = array(
        '_apr_file_id'      => array( 'apr_file_id', 'absint' ),
        '_apr_external_url' => array( 'apr_external_url', 'esc_url_raw' ),
        '_apr_version'      => array( 'apr_version', 'sanitize_text_field' ),
        '_apr_fw_version'   => array( 'apr_fw_version', 'sanitize_text_field' ),
        '_apr_fw_date'      => array( 'apr_fw_date', 'sanitize_text_field' ),
        '_apr_kind'         => array( 'apr_kind', 'sanitize_text_field' ),
    );
    foreach ( $fields as $meta => $cfg ) {
        if ( isset( $_POST[ $cfg[0] ] ) ) {
            $value = call_user_func( $cfg[1], wp_unslash( $_POST[ $cfg[0] ] ) );
            if ( '_apr_kind' === $meta && ! in_array( $value, array( 'Feature', 'Fix', 'Security' ), true ) ) { $value = 'Feature'; }
            if ( '_apr_fw_date' === $meta && '' === $value && 'apr_firmware' === get_post_type( $post_id ) ) { $value = current_time( 'Y-m-d' ); }
            update_post_meta( $post_id, $meta, $value );
        }
    }
}
add_action( 'save_post_apr_resource', 'apr_save_meta' );
add_action( 'save_post_apr_firmware', 'apr_save_meta' );

function apr_admin_media() {
    $screen = get_current_screen();
    if ( $screen && in_array( $screen->post_type, array( 'apr_resource', 'apr_firmware' ), true ) ) { wp_enqueue_media(); }
}
add_action( 'admin_enqueue_scripts', 'apr_admin_media' );
