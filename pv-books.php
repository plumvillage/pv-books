<?php
/*
Plugin Name: Library
Description: A plugin which adds the ability to add library features in wordpress
Version: 0.1
Author: Maarten Hunink
License: GPL2
*/
add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'pv_book',
    array(
      'labels' => array(
        'name' => __( 'Books' ),
        'singular_name' => __( 'Books' ),
        'menu_name' => __( 'Books' ),
        'not_found' => __( 'No books found' ),
        'not_found_in_trash' => __( 'No books found in the trash'),
        'view_item' => __( 'View book' ),
        'new_item' => __( 'New book' ),
        'add_new_item' => __( 'Add new book' ),
        'edit_item' => __( 'Edit book' ),
        'search_items' => __ ( 'Search books' ),
      ),
      'public' => true,
      'has_archive' => true,
      'menu_position' => 20,
      'menu_icon' => 'dashicons-book',
      'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
      'taxonomies' => array('genre', 'subject'),
      'rewrite' => array(
	      'slug' => 'books',
      ),
      'query_var' => 'books',
    )
  );
  register_taxonomy( 'genre', 'pv_book', array(
	  'labels' => array(
		  	'name' => __( 'Genres'),
	        'singular_name' => __( 'Genre' ),
	        'menu_name' => __( 'Genres' ),
			'all_items' => __( 'All genres' ),
			'edit_item' => __( 'Edit genre' ),
			'view_item' => __( 'View genre' ),
			'update_item' => __( 'Update genre' ),
			'add_new_item' => __( 'Add new genre' ),
			'new_item_name' => __( 'New genre name' ),
			'search_items' => __( 'Search genres' ),
			'popular_items' => __( 'Popular genres' ),
			'separate_items_with_commas' => __( 'Separate genres with commas' ),
			'add_or_remove_items' => __( 'Add or remove genres' ),
			'choose_from_most_used' => __( 'Choose from the most used genres' ),
			'not_found' => __( 'No genres found' ),
	  )
  ) );
  register_taxonomy( 'subject', 'pv_book', array(
	  'labels' => array(
		  	'name' => __( 'Subjects'),
	        'singular_name' => __( 'Subject' ),
	        'menu_name' => __( 'Subjects' ),
			'all_items' => __( 'All subjects' ),
			'edit_item' => __( 'Edit subject' ),
			'view_item' => __( 'View subject' ),
			'update_item' => __( 'Update subject' ),
			'add_new_item' => __( 'Add new subject' ),
			'new_item_name' => __( 'New subject name' ),
			'search_items' => __( 'Search subjects' ),
			'popular_items' => __( 'Popular subjects' ),
			'separate_items_with_commas' => __( 'Separate subjects with commas' ),
			'add_or_remove_items' => __( 'Add or remove subjects' ),
			'choose_from_most_used' => __( 'Choose from the most used subjects' ),
			'not_found' => __( 'No subjects found' ),
	  )
  ) );
  register_taxonomy( 'book_author', 'pv_book', array(
	  'labels' => array(
		  	'name' => __( 'Author'),
	        'singular_name' => __( 'Author' ),
	        'menu_name' => __( 'Authors' ),
			'all_items' => __( 'All authors' ),
			'edit_item' => __( 'Edit author' ),
			'view_item' => __( 'View author' ),
			'update_item' => __( 'Update author' ),
			'add_new_item' => __( 'Add new author' ),
			'new_item_name' => __( 'New author name' ),
			'search_items' => __( 'Search authors' ),
			'popular_items' => __( 'Popular authors' ),
			'separate_items_with_commas' => __( 'Separate authors with commas' ),
			'add_or_remove_items' => __( 'Add or remove authors' ),
			'choose_from_most_used' => __( 'Choose from the most used authors' ),
			'not_found' => __( 'No authors found' ),
	  )
  ) );
}
add_action( 'admin_head', 'remove_my_meta_boxen' );

function remove_my_meta_boxen() {
   remove_meta_box('powerpress-podcast', 'pv_book', 'normal');
   remove_meta_box('tagsdiv-book_author', 'pv_book', 'side');
   remove_meta_box('customsidebars-mb', 'pv_book', 'side');
}

// makes it possible to filter the books
class Walker_Books extends Walker_Category {


	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		/** This filter is documented in wp-includes/category-template.php */
		$cat_name = apply_filters(
			'list_cats',
			esc_attr( $category->name ),
			$category
		);

		// Don't generate an element if the category name is empty.
		if ( ! $cat_name ) {
			return;
		}

		$link = '<a data-filter=".'.$category->taxonomy. '-'. $category->slug .'" href="' . esc_url( get_term_link( $category ) ) . '" ';
		if ( $args['use_desc_for_title'] && ! empty( $category->description ) ) {
			/**
			 * Filter the category description for display.
			 *
			 * @since 1.2.0
			 *
			 * @param string $description Category description.
			 * @param object $category    Category object.
			 */
			$link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
		}

		$link .= '>';
		$link .= $cat_name . '</a>';

		if ( ! empty( $args['feed_image'] ) || ! empty( $args['feed'] ) ) {
			$link .= ' ';

			if ( empty( $args['feed_image'] ) ) {
				$link .= '(';
			}

			$link .= '<a href="' . esc_url( get_term_feed_link( $category->term_id, $category->taxonomy, $args['feed_type'] ) ) . '"';

			if ( empty( $args['feed'] ) ) {
				$alt = ' alt="' . sprintf(__( 'Feed for all posts filed under %s' ), $cat_name ) . '"';
			} else {
				$alt = ' alt="' . $args['feed'] . '"';
				$name = $args['feed'];
				$link .= empty( $args['title'] ) ? '' : $args['title'];
			}

			$link .= '>';

			if ( empty( $args['feed_image'] ) ) {
				$link .= $name;
			} else {
				$link .= "<img src='" . $args['feed_image'] . "'$alt" . ' />';
			}
			$link .= '</a>';

			if ( empty( $args['feed_image'] ) ) {
				$link .= ')';
			}
		}

		if ( ! empty( $args['show_count'] ) ) {
			$link .= ' (' . number_format_i18n( $category->count ) . ')';
		}
		if ( 'list' == $args['style'] ) {
			$output .= "\t<li";
			$css_classes = array(
				'cat-item',
				'cat-item-' . $category->term_id,
			);

			if ( ! empty( $args['current_category'] ) ) {
				$_current_category = get_term( $args['current_category'], $category->taxonomy );
				if ( $category->term_id == $args['current_category'] ) {
					$css_classes[] = 'current-cat';
				} elseif ( $category->term_id == $_current_category->parent ) {
					$css_classes[] = 'current-cat-parent';
				}
			}

			/**
			 * Filter the list of CSS classes to include with each category in the list.
			 *
			 * @since 4.2.0
			 *
			 * @see wp_list_categories()
			 *
			 * @param array  $css_classes An array of CSS classes to be applied to each list item.
			 * @param object $category    Category data object.
			 * @param int    $depth       Depth of page, used for padding.
			 * @param array  $args        An array of wp_list_categories() arguments.
			 */
			$css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) );

			$output .=  ' class="' . $css_classes . '"';
			$output .= ">$link\n";
		} else {
			$output .= "\t$link<br />\n";
		}
	}

}

function wpcf_create_temp_column($fields) {
  global $wpdb;
  $matches = 'A|An|The|La|Les';
  $has_the = " CASE 
      WHEN $wpdb->posts.post_title regexp( '^($matches)[[:space:]]' )
        THEN trim(substr($wpdb->posts.post_title from 4)) 
      ELSE $wpdb->posts.post_title 
        END AS title2";
  if ($has_the) {
    $fields .= ( preg_match( '/^(\s+)?,/', $has_the ) ) ? $has_the : ", $has_the";
  }
  return $fields;
}

function wpcf_sort_by_temp_column ($orderby) {
  $custom_orderby = " UPPER(title2) ASC";
  if ($custom_orderby) {
    $orderby = $custom_orderby;
  }
  return $orderby;
}

add_action( 'pre_get_posts', 'my_change_sort_order'); 
function my_change_sort_order($query){
    if(is_post_type_archive( 'pv_book' )){
     //If you wanted it for the archive of a custom post type use: 
       //Set the order ASC or DESC
		add_filter('posts_fields', 'wpcf_create_temp_column');
		add_filter('posts_orderby', 'wpcf_sort_by_temp_column');

       $query->set( 'order', 'ASC' );
       //Set the orderby
       $query->set( 'orderby', 'title' );
    }
};