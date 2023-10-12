<?php
 /**
 * Plugin Name: Pixzl Featured App Image
 * Description: Fügt ein spezielles "App Featured Image" zum WordPress-Editor hinzu, um das Bild optimal in einer Pixzl App anzuzeigen.
 * Version: 1.0.0
 * Author: Pixzl
 * Author URI:  https://www.pixzl.de
 * Plugin URI:  https://www.pixzl.de/downloads/wp-pixzl-featured-app-image/
 * Text Domain: pixzl-featured-app-image
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 **/
 /*
 * Copyright (C)  2023 Pixzl
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */
 
 
 
 
 
 function pixzl_add_metabox() {
	 add_meta_box('pixzl_featured_app_image', 'Pixzl Featured App Image', 'pixzl_metabox_callback', 'post');
 }
 
 function pixzl_metabox_callback($post) {
	 $image_id = get_post_meta($post->ID, 'pixzl_featured_app_image_id', true);
	 echo '<input type="hidden" name="pixzl_featured_app_image_id" id="pixzl_featured_app_image_id" value="' . esc_attr($image_id) . '">';
	 echo '<input type="button" id="pixzl_upload_image_button" class="button" value="Bild hochladen" />';
	 echo '<input type="button" id="pixzl_remove_image_button" class="button" value="Bild entfernen" />';
 }
 
 add_action('add_meta_boxes', 'pixzl_add_metabox');


function pixzl_enqueue_scripts() {
	 wp_enqueue_script('media-upload');
	 wp_enqueue_script('thickbox');
	 wp_enqueue_style('thickbox');
	 wp_enqueue_script('pixzl-script', plugins_url('admin.js', __FILE__), array('jquery'));
 }
 
 add_action('admin_enqueue_scripts', 'pixzl_enqueue_scripts');


function pixzl_save_post($post_id) {
	 if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	 if (isset($_POST['pixzl_featured_app_image_id'])) {
		 update_post_meta($post_id, 'pixzl_featured_app_image_id', $_POST['pixzl_featured_app_image_id']);
	 }
 }
 
 add_action('save_post', 'pixzl_save_post');


function pixzl_rest_prepare_post($response, $post, $request) {
	 $image_id = get_post_meta($post->ID, 'pixzl_featured_app_image_id', true);
	 $response->data['pixzl_featured_app_image'] = wp_get_attachment_url($image_id);
	 return $response;
 }
 
 add_filter('rest_prepare_post', 'pixzl_rest_prepare_post', 10, 3);


function pixzl_add_featured_app_image_to_api() {
	 register_rest_field(
		 'post', 
		 'pixzl_featured_app_image',
		 array(
			 'get_callback'    => 'pixzl_get_featured_app_image',
			 'update_callback' => null,
			 'schema'          => null
		 )
	 );
 }
 
 function pixzl_get_featured_app_image($object, $field_name, $request) {
	 $image_id = get_post_meta($object['id'], 'pixzl_featured_app_image_id', true);
	 return wp_get_attachment_url($image_id);
 }
 
 add_action('rest_api_init', 'pixzl_add_featured_app_image_to_api');
 
 
 function pixzl_custom_endpoint_data($response, $server, $request) {
	 if ($request->get_route() == '/wp/v2') {
		 $posts = get_posts(array(
			 'numberposts' => -1 // oder wie viele Sie auch immer holen möchten
		 ));
		 
		 $response_data = $response->get_data();
		 
		 foreach ($posts as $post) {
			 $image_id = get_post_meta($post->ID, 'pixzl_featured_app_image_id', true);
			 $response_data['posts'][$post->ID]['pixzl_featured_app_image'] = wp_get_attachment_url($image_id);
		 }
		 
		 $response->set_data($response_data);
	 }
	 return $response;
 }
 add_filter('rest_post_dispatch', 'pixzl_custom_endpoint_data', 10, 3);
