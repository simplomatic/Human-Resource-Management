<?php
class HRM_Uploader {
    private static $_instance;

    public static function getInstance() {
        if ( !self::$_instance ) {
            self::$_instance = new HRM_Uploader();
        }

        return self::$_instance;
    }
    function upload_file() {

        // check if guest post enabled for guests
        if ( ! is_user_logged_in() ) {
            die( 'error' );
        }

        $upload = array(
            'name'     => $_FILES['file']['name'],
            'type'     => $_FILES['file']['type'],
            'tmp_name' => $_FILES['file']['tmp_name'],
            'error'    => $_FILES['file']['error'],
            'size'     => $_FILES['file']['size']
        );

        header('Content-Type: text/html; charset=' . get_option('blog_charset'));

        $attach = $this->handle_upload( $upload );

        if ( $attach['success'] ) {

            $response = array( 'success' => true );
            return $this->attach_html( $attach['attach_id'] );
        } else {
            return 'error';
        }

        exit;
    }

    /**
     * Generic function to upload a file
     *
     * @param string $field_name file input field name
     * @return bool|int attachment id on success, bool false instead
     */
    function handle_upload( $upload_data ) {

        $uploaded_file = wp_handle_upload( $upload_data, array('test_form' => false) );

        // If the wp_handle_upload call returned a local path for the image
        if ( isset( $uploaded_file['file'] ) ) {
            
            $file_loc  = $uploaded_file['file'];
            $file_name = basename( $upload_data['name'] );
            $file_type = wp_check_filetype( $file_name );

            $attachment = array(
                'post_mime_type' => $file_type['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            $attach_id   = wp_insert_attachment( $attachment, $file_loc );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file_loc );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            return array('success' => true, 'attach_id' => $attach_id);
        }

        return array('success' => false, 'error' => $uploaded_file['error']);
    }

    public static function attach_html( $attach_id, $custom_attr = [] ) {

        $attachment = get_post( $attach_id );

        if ( ! $attachment ) {
            return;
        }

        if (wp_attachment_is_image( $attach_id)) {
            $image = wp_get_attachment_image_src( $attach_id, array( '80', '80' ) );
            $image = $image[0];
        } else {
            $image = wp_mime_type_icon( $attach_id );
        }

        $html = '<li class="hrm-image-wrap thumbnail">';
        $html .= sprintf( '<div class="attachment-name"><img class="hrm-file-mime" '.implode( ' data-', $custom_attr ).' height="80" width="80" src="%s" alt="%s" /></div>', $image, esc_attr( $attachment->post_title ) );
        $html .= sprintf( '<input type="hidden" name="files[]" value="%d">', $attach_id );
        $html .= sprintf( '<div class="caption"><a href="#" class="hrm-del-attc-button btn-danger btn-small attachment-delete" data-attach_id="%d">X</a></div>', $attach_id );
        $html .= '</li>';

        return $html;
    }

    function delete_file( $attach_id ) {
        
        $attachment = get_post( $attach_id );

        //post author or editor role
        if ( get_current_user_id() == $attachment->post_author || current_user_can( 'delete_private_pages' ) ) {
            wp_delete_attachment( $attach_id, true );
            return true;        
        }

        exit;
    }
}