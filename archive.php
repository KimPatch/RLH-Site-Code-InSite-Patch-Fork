<header class="contentHeader contentHeader--archive">
  <h2><?= get_queried_object()->name; ?></h2>
</header>
<aside class="researchMenu">
  <button class="researchMenu-toggle">Expand Menu <?= icon( 'down', 'link' ); ?></button>
  <?php dynamic_sidebar('blog'); ?>
</aside>
<section class="genericContent genericContent--research">
  <?php while(have_posts()): the_post(); ?>
    <article class="blog-single">
      <h3 class="blog-article-head"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
      <div class="blog-meta">Posted <?php the_date(); ?> by <?php the_author(); ?></div>
      <div class="blog-excerpt"><?php the_excerpt(); ?><a href="<?php the_permalink(); ?>">Read More</a></div>
    </article>
  <?php endwhile; ?>
  <div class="blog-pagination">
    <?= paginate_links([
      'total' => $posts->max_num_pages
    ]); ?>
  </div>
</section>
