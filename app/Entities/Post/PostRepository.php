<?php namespace NestedPages\Entities\Post;

class PostRepository {

	/**
	* Get count of hidden posts
	* @since 1.1.4
	*/
	public function getHiddenCount($type)
	{
		if ( in_array('page', $type) ) array_push($type, 'np-redirect');
		$hidden = new \WP_Query(array(
			'post_type' => $type,
			'meta_key' => 'nested_pages_status',
			'meta_value' => 'hide',
			'perm' => 'readable'));
		return $hidden->found_posts;
	}


	/**
	* Get Trash Count (pages)
	* @since 1.1.4
	*/
	public function trashedCount($post_type)
	{
		$trashed = new \WP_Query(array('post_type'=>$post_type,'post_status'=>'trash','posts_per_page'=>-1));
		return $trashed->found_posts;
	}


	/**
	* Get count of published posts
	* @param object $pages
	*/
	public function publishCount($pages)
	{
		$publish_count = 1;
		foreach ( $pages->posts as $p ){
			if ( $p->post_status !== 'trash' ) $publish_count++;
		}
		return $publish_count;
	}


	/**
	* Get an array of pages given an array of IDs
	* @since 1.1.8 (used in creation of new pages)
	* @param ids array
	* @return array
	*/
	public function pageArray($ids)
	{
		$pages = array();
		$page_query = new \WP_Query(array(
			'post_type' => 'page',
			'posts_per_page' => -1,
			'post__in' => $ids,
			'post_status' => array('publish', 'draft')
		));
		if ( $page_query->have_posts() ) : $c = 0; while ( $page_query->have_posts() ) : $page_query->the_post();
			global $post;
			
			$pages[$c]['id'] = get_the_id();
			$pages[$c]['title'] = get_the_title();
			$pages[$c]['slug'] = $post->post_name;
			$pages[$c]['author'] = get_the_author_meta('ID');
			$pages[$c]['status'] = ucfirst(get_post_status());
			$pages[$c]['page_template'] = get_page_template_slug($post->ID);
			$pages[$c]['post_parent'] = $post->post_parent;
			$pages[$c]['edit_link'] = get_edit_post_link($post->ID);
			$pages[$c]['view_link'] = get_the_permalink();
			$pages[$c]['delete_link'] = get_delete_post_link($post->ID);
			$pages[$c]['comment_status'] = $post->comment_status;
			$pages[$c]['comment_count'] =  wp_count_comments($post->ID);

			// Date Vars
			$pages[$c]['day'] = get_the_time('d');
			$pages[$c]['month'] = get_the_time('m');
			$pages[$c]['year'] = get_the_time('Y');
			$pages[$c]['hour'] = get_the_time('H');
			$pages[$c]['minute'] = get_the_time('i');


		$c++; endwhile; endif; wp_reset_postdata();
		return $pages;
	}

}