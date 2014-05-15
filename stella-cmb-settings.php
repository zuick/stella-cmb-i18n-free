<?php
class Stella_CMB_i18n_settings {
    private $whole_cmb_list;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_options_page' ) );
        add_filter( 'cmb_meta_boxes', array( $this, 'store_cmb'), 100, 1 );
    }

    function store_cmb( $meta_boxes ){
        $this->whole_cmb_list = $meta_boxes;
        return $meta_boxes;
    }
    function add_options_page(){
        add_options_page( __( 'Stella CMB','stella-plugin' ), __( 'Stella CMB', 'stella-plugin' ), 'manage_options', 'stella-cmb-options', array( $this, 'options_page' ) );
    }

    function options_page_update(){
        if ( isset( $_POST['Submit'] ) ) {
            $selected_cmb = array();
            foreach( $_POST as $key => $value ){
                if( $key != 'Submit' ){
                    array_push( $selected_cmb, $key );
                }
            }

            // update options
            if( is_multisite() )
                update_blog_option( get_current_blog_id(), 'stella-cmb-options', $selected_cmb );
            else
                update_option( 'stella-cmb-options', $selected_cmb );
        }
    }
    function cmb_list_html(){
        $options = ( is_multisite() ) ? get_blog_option( get_current_blog_id(), 'stella-cmb-options' ) : get_option( 'stella-cmb-options' );

        $html = "";
        foreach( $this->whole_cmb_list as $key => $value ){
            $id = $value["id"];
            $title = $value["title"];
            $pages = implode( ",", $value["pages"] );
            $checked = ( $options && in_array( $id, $options ) ) ? 'checked="checked"' : '';
            $html .= "<input type='checkbox' value='' name='$id' $checked>$title <span style='font-style: italic; color: #aaa;'>( $pages )</span></br>";
        }
        return $html;
    }

    function options_page_html(){

        $p_title = __('Stella custom metaboxes settings page.', 'stella-plugin');
        $p_subtitle = __('Select metaboxes which should be localized', 'stella-plugin');
        $p_update = __('Save Changes');
        $p_inputs = $this->cmb_list_html();
        $options_html = <<<options_html
        <div class="stella-cmb-settings">
            <h2>$p_title</h2>
            <h4>$p_subtitle</h4>
            <form method="post" name="options" target="_self">
            $p_inputs
            <p class="submit">
				<input id="submit-options" class="button-primary" type="submit" name="Submit" value="$p_update">
			</p>
            </form>
        </div>
options_html;

        return $options_html;
    }
    function options_page(){
        // checking permissions
        if ( !current_user_can( 'manage_options' ) ) {
            echo "<h3>You do not have sufficient permissions to access this page</h3>";
        }else{
            echo $this->options_page_update();
            echo $this->options_page_html();
        }
    }
}

new Stella_CMB_i18n_settings();