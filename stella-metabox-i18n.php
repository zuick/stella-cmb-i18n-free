<?php
/*
Plugin Name: Stella metabox i18n
Author: Ruslan Khakimov (Frumatic)
Version: 0.1
*/

class Stella_CMB_i18n {
    private $langs;
    private $use_default_lang_values;
    
    public function __construct() {
        add_action( 'stella_parameters', array($this, 'start'), 1, 3);

        if( file_exists( dirname(__FILE__) . '/stella-cmb-settings.php'  ) ) include_once 'stella-cmb-settings.php';;
    }
    
    function start($langs, $use_hosts, $use_default_lang_values) {
        
        $this->use_default_lang_values = $use_default_lang_values;
        $this->langs = $langs;
        $this->cmb_ids = array();

        add_filter( 'cmb_meta_boxes', array( $this, 'filter_cmb'), 100, 1 );
        add_filter( 'get_post_metadata', array($this, 'filter_metadata'), 1, 4);
        add_action( 'admin_enqueue_scripts', array($this, 'add_scripts'));
    }
    function get_language_copy( $meta_box, $lang_code, $lang_name ){
        $meta_box['title'] = $meta_box['title'].' ( '.$lang_name.' )';
        $meta_box['id'] = $meta_box['id'].'_'.$lang_code;
        if( isset( $meta_box['fields'] ) ){
            for( $i = 0; $i < count( $meta_box['fields'] ); $i++ ){
                $meta_box['fields'][$i]['id'] = $meta_box['fields'][$i]['id'].'_'.$lang_code;
            }            
        }
        return $meta_box;
    }

    function add_scripts(){
        wp_enqueue_script('stella_smb_tabs', plugins_url('js/cmb-tabs.js', __FILE__ ), array(), false, true);
        wp_localize_script('stella_smb_tabs', 'stella_cmb_ids', json_encode( $this->cmb_ids ));
    }
    function filter_cmb( $meta_boxes ){
        $options = ( is_multisite() ) ? get_blog_option( get_current_blog_id(), 'stella-cmb-options' ) : get_option( 'stella-cmb-options' );

        if( is_admin() && isset( $meta_boxes ) && $options ){
            // save ids in purpise to send them to js after
            $this->cmb_ids['default'] = array();
            foreach ( $meta_boxes as $key => $box ){
                if( in_array( $box['id'], $options ) ) $this->cmb_ids['default'][] = $box['id'];
            }

            // add langs copies
            foreach ($this->langs['others'] as $code => $value) {
                $this->cmb_ids[$code] = array();
                foreach ( $meta_boxes as $key => $box ){
                    if( in_array( $box['id'], $options ) ){
                        $new_box = $this->get_language_copy($box, $code, $value['name']);
                        $this->cmb_ids[$code][] = $new_box['id'];
                        $meta_boxes[$key.'_'.$code] = $new_box;
                    }
                }
            }


        }

        return $meta_boxes;            
    }
    
    function filter_metadata( $value, $object_id, $meta_key, $single ){
        if (STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG) {
            $meta_cache = wp_cache_get($object_id, 'post_meta');

            if (!$meta_cache) {
                    $meta_cache = update_meta_cache('post', array($object_id));
                    $meta_cache = $meta_cache[$object_id];
            }

            $value_new = $value;

            if ( ! $meta_key)
                    $value_new = $meta_cache;

            if (isset($meta_cache[$meta_key . '_' . STELLA_CURRENT_LANG])) {
                    if ($single){
                            $value_new = maybe_unserialize($meta_cache[$meta_key . '_' . STELLA_CURRENT_LANG][0]);
                        //var_dump($value_new ); die;
                    }else{
                            $value_new = array_map('maybe_unserialize', $meta_cache[$meta_key . '_' . STELLA_CURRENT_LANG]);
                    }
            }

            if ('' != $value_new || !$this->use_default_lang_values) {
                    return $value_new;
            }
    }
        return $value;
    }
}

new Stella_CMB_i18n();