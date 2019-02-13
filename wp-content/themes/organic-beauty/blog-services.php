<?php
/*
Template Name: Services list
*/

/**
 * Make empty page with this template 
 * and put it into menu
 * to display all Services as streampage
 */

organic_beauty_storage_set('blog_filters', 'services');

get_template_part('blog');
?>