<?php
/*
Plugin Name: Tag Posts Widget
Plugin URI: http://www.nightdrops.com/2010/wordpress-widget-tagposts/
Description: Adds a widget that can display posts from a single tag. Based on the Category Posts Widget by James Lao.
Author: Carlo 'kj'
Version: 1.0
Author URI: http://nightdrops.com
*/
?>
<?php
/*  Copyright 2010  Carlo 'kj'  (email : carlo.panzi@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
class TagPosts extends WP_Widget {

function TagPosts() {
	parent::WP_Widget(false, $name='Tag Posts');
}

/**
 * Displays category posts widget on blog.
 */
function widget($args, $instance) {
	global $post;
	$post_old = $post; // Save the post object.
	
	extract( $args );
	if ('on' == $instance['archive'] && is_tag()) {
		$all = get_tags();
		foreach ($all as $tag) {
			if (is_tag($tag->slug)) $instance['tags'][] = $tag->term_id;	
		}
	}
	$tags = get_tags('include=' . implode(',', $instance['tags']));

	if( !$instance['title'] ) {
		$instance['title'] = 'Tag Posts';
	}
	
	
	if ('on' == $instance['append'] && $tags) {	
		foreach($tags as $tag) {
			$names[] = $tag->name;
		}
		$instance['title'] .= ' ' .implode(', ', $names);
	}
	
	// Get array of post info.
	if ('0' == $instance['num']) $instance['num'] = '-1';
	$selected_posts = new WP_Query(array('tag__in' => $instance['tags'], 'posts_per_page' => $instance['num']));

	echo $before_widget;
	
	// Widget title
	echo $before_title;
	echo $instance["title"];
	echo $after_title;	

	// Post list
	echo "<ul>\n";
	
	while ( $selected_posts->have_posts() )
	{
		$selected_posts->the_post();
	?>
		<li class="tag-post-item">
			<a class="post-title" href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
		</li>
	<?php
	}
	
	echo "</ul>\n";
	
	echo $after_widget;
	
	// Restore the post object.
	$post = $post_old;
	wp_reset_query();
}

/**
 * Form processing... Dead simple.
 */
function update($new_instance, $old_instance) {
	return $new_instance;
}

/**
 * The configuration form.
 */
function form($instance) {
	
	$tags = get_tags('orderby=name&order=ASC');
	if (!$tags) {
		echo 'You don\'t have any tag&hellip;';
		return;
	}
	if (!$instance["tags"]) $instance["tags"] = array();
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php _e( 'Title' ); ?>:
				<input class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
			</label>
			<label for="<?php echo $this->get_field_id('append'); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('append'); ?>" name="<?php echo $this->get_field_name('append'); ?>"<?php checked( (bool) $instance['append'], true ); ?> />
				<?php _e( 'Append tags name to title' ); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('archive'); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('archive'); ?>" name="<?php echo $this->get_field_name('archive'); ?>"<?php checked( (bool) $instance['archive'], true ); ?> />
				<?php _e( 'If the page is a tag archive, include that tag' ); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('tags'); ?>">
				<?php _e( 'Tags to show' ); ?>:
				<select id="<?php echo $this->get_field_id('tags'); ?>" name="<?php echo $this->get_field_name('tags'); ?>[]" class="widefat" style="height:auto;"multiple="multiple" size="10">
				<?php foreach($tags as $tag) {?>
              		<option value="<?php echo $tag->term_id;?>" <?php selected( in_array($tag->term_id, $instance['tags']) ); ?> ><?php echo $tag->name; ?></option>			   
            	<?php }?>
				</select>
       		</label> 
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('num'); ?>">
				<?php _e('Number of posts shown'); ?>:
				<input style="text-align: center;" id="<?php echo $this->get_field_id('num'); ?>" name="<?php echo $this->get_field_name('num'); ?>" type="text" value="<?php echo absint($instance['num']); ?>" size='3' /><br />
			</label>
		</p>

<?php

}

}

add_action( 'widgets_init', create_function('', 'return register_widget("TagPosts");') );

?>
