<?php
/**
 * Plugin Name:     Ultimate Member - Tabindex Settings
 * Description:     Extension to Ultimate Member for setting Tabindex in the Registration form's text and textarea input fields.
 * Version:         1.2.0 
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica?tab=repositories
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.8.3
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'UM' ) ) return;

class UM_Tabindex_Settings {

    public $tabindex_meta_keys= array();
    public $needles = array( '<input ', '<textarea ', '<select ' );

	public function __construct() {

        add_filter( 'um_settings_structure', array( $this, 'um_settings_structure_tabindex' ), 10, 1 );

        $this->tabindex_meta_keys = array_map( 'sanitize_text_field', array_map( 'trim', explode( "\n", UM()->options()->get( 'tabindex_meta_keys' ))));

        if ( is_array( $this->tabindex_meta_keys ) && ! empty( $this->tabindex_meta_keys )) {

            foreach( $this->tabindex_meta_keys as $key ) {
                add_filter( "um_{$key}_form_edit_field", array( $this, 'tabindex_settings' ), 10, 2 );
            }
        }
    }

    public function tabindex_settings( $output, $set_mode ) {

        if ( $set_mode == 'register' ) {

            $key = str_replace( array( 'um_', '_form_edit_field' ), '', current_filter());

            if ( in_array( $key, $this->tabindex_meta_keys )) {

                $tabindex = array_search( $key, $this->tabindex_meta_keys );

                foreach( $this->needles as $needle ) {

                    if ( str_contains( $output, $needle )) {
                        $output = str_replace( $needle, $needle . 'tabindex="' . ++$tabindex . '" ', $output );
                        break;
                    }
                }
            }
        }

        return $output;
    }

    public function um_settings_structure_tabindex( $settings_structure ) {

        $settings_structure['appearance']['sections']['registration_form']['form_sections']['tabindex']['title']       = __( 'Tabindex Settings', 'ultimate-member' );
        $settings_structure['appearance']['sections']['registration_form']['form_sections']['tabindex']['description'] = __( 'Plugin version 1.1.0 - tested with UM 2.8.3', 'ultimate-member' );

        $settings_structure['appearance']['sections']['registration_form']['form_sections']['tabindex']['fields'][] =

            array(
                    'id'          => 'tabindex_meta_keys',
                    'type'        => 'textarea',
                    'label'       => __( 'Enter meta_keys in tabindex order', 'ultimate-member' ),
                    'description' => __( 'One meta_key per line. Tabindex used for text and textarea input fields in Registration Forms.', 'ultimate-member' ),
                    'size'        => 'small',
                );

        return $settings_structure;
    }

}

new UM_Tabindex_Settings();
