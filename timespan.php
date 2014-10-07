<?php 
/* 
Plugin Name: TimeSpan
Plugin URI: http://paper-leaf.com
Description: A simple, easy to use plugin which can be used to display the average time it will take to read a post.
Version: 1.0.2
Author: Paper Leaf Design
Author URI: http://paper-leaf.com
License: GPLv2 or Later
*/

add_action( 'admin_menu', 'ttr_menu' );

function ttr_menu() {
	add_options_page( 'TimeSpan Options', 'TimeSpan', 'manage_options', 'ttr-identifier', 'ttr_options' );
}


add_action( 'admin_init', 'register_ttrsettings' );

function register_ttrsettings() {
	//register our settings
    register_setting( 'ttr-group', 'wpm' );
	register_setting( 'ttr-group', 'lt1' );
    register_setting( 'ttr-group', 'gt1' );
    register_setting( 'ttr-group', 'gt1po' );
	register_setting( 'ttr-group', 'autots' );
}

function ttr_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	include('includes/ttr-options.php');

}



function time_to_read() {
    $content = get_post_field( 'post_content', $post->ID );
    $word_count = str_word_count( strip_tags( $content ) );
    $words_per_minute = 250;
    if (get_option('wpm') != '') {
        $words_per_minute = get_option('wpm');
    }
    $time_in_minutes = $word_count/$words_per_minute;

    if ($time_in_minutes < 1) {
        $full = '<span class="timespan less-than"><p>';
        if (get_option('lt1') != '') {
            $full .= get_option('lt1'); // get the option from WP backend
        } else {
            $full .= 'less than one minute.';
        }
        $full .= '</p></span>';
        return $full;
    } else {
        $time_in_minutes = round($time_in_minutes); // round the time to a full number
        $strTime = strval($time_in_minutes); // convert time to a string
        $full = '<span class="timespan greater-than"><p>';
        if (get_option('gt1') != '') {
            $full .= get_option('gt1'); // get the option from WP backend
        } else {
            $full .= 'greater than';
        }
        $full .= '<span class="ts-time"> ' . $strTime . '</span> '; // add time onto the end of the content
        if (get_option('gt1po') != '') {
            $full .= get_option('gt1po'); // get the option from WP backend
        } else {
            $full .= 'minutes';
        }
        return $full; // return rounded time
    }
}
add_shortcode('time-span', 'time_to_read');

function auto_TTR( $content ) {
    if ( is_single() && is_main_query() ) {
        if (get_option('autots') == 1) {    
            $word_count = str_word_count( strip_tags( $content ) );
            $words_per_minute = 250;
            if (get_option('wpm') != '') {
                $words_per_minute = get_option('wpm');
            }
            $time_in_minutes = $word_count/$words_per_minute;

            if ($time_in_minutes < 1) {
                $full = '<span class="timespan less-than"><p>';
                if (get_option('lt1') != '') {
                    $full .= get_option('lt1'); // get the option from WP backend
                } else {
                    $full .= 'less than one minute.';
                }
                $full .= '</p></span>';
                // $full .= strval($time_in_minutes);
                return $full . $content;
            } else {
                $time_in_minutes = round($time_in_minutes); // round the time to a full number
                $strTime = strval($time_in_minutes); // convert time to a string
                $full = '<span class="timespan greater-than"><p>';
                if (get_option('gt1') != '') {
                    $full .= get_option('gt1'); // get the option from WP backend
                } else {
                    $full .= 'greater than';
                }
                $full .= '<span class="ts-time"> ' . $strTime . '</span> '; // add time onto the end of the content
                if (get_option('gt1po') != '') {
                    $full .= get_option('gt1po'); // get the option from WP backend
                } else {
                    $full .= 'minutes';
                }
                return $full . $content; // return rounded time
            }
        }
    } else {
        return $content;
    }
}
add_filter('the_content', 'auto_TTR');

?>