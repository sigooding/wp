<?php
/* Template Name: Trade Portal Full Width */
get_header(); ?>
<main class="full-width-page">
<?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
</main>
<?php get_footer(); ?>
