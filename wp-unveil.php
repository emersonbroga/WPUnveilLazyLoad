<?php
/*
Plugin Name: WPUnveilLazyLoad
Plugin URI:
Description: A simple and small image lazy loader for wordpress using unveil. Inspired by the lightweight
    of unveil (https://github.com/luis-almeida/unveil) and the lazy load plugin (http://wordpress.org/plugins/lazy-load/)
Version: 0.1
Author: Emerson Carvalho
Author URI: emersoncarvalho.com
License: GPL2
*/

if ( ! class_exists( 'WPUnveilLazyLoad' ) ) :

class WPUnveilLazyLoad {

	const version = '0.1';

	static function init() {
		if ( is_admin() )
			return;

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_scripts' ) );
		add_filter( 'the_content', array( __CLASS__, 'add_image_placeholders' ), 99 );
		add_filter( 'post_thumbnail_html', array( __CLASS__, 'add_image_placeholders' ), 11 );
		add_filter( 'get_avatar', array( __CLASS__, 'add_image_placeholders' ), 11 );
	}

	static function add_scripts() {
        wp_enqueue_script(
            'jquery_unveil',
            self::get_url( 'jquery.unveil.min.js'),
            array( 'jquery' )
        );
        wp_enqueue_script(
            'wp_unveil',
            self::get_url( 'wp.unveil.min.js'),
            array( 'jquery_unveil' )
        );
    }

	static function add_image_placeholders( $content ) {
		// don't lazyload for feeds, previews, mobile
		if( is_feed() || is_preview() || ( function_exists( 'is_mobile' ) && is_mobile() ) )
			return $content;

		// don't lazy-load if the content has already been run through previously
		if ( false !== strpos( $content, 'data-src' ) )
			return $content;

		// blank image
		$loader = self::get_url('blank.gif');

		// regex to add the data-src on the image tag
		$content = preg_replace(
                '#<img([^>]+?)src=[\'"]?([^\'"\s>]+)[\'"]?([^>]*)>#',
                sprintf( '<img${1}src="%s" data-src="${2}"${3}><noscript><img${1}src="${2}"${3}></noscript>', $loader ),
                $content );

		return $content;
	}

	static function get_url( $path = '' ) {
		return plugins_url( ltrim( $path, '/' ), __FILE__ );
	}
}

WPUnveilLazyLoad::init();

endif;

