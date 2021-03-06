<?php

// get featured content
$feat_cont_type = get_field( 'home_type', 'options' );
$feat_cont_id = get_field( 'home_'.$feat_cont_type, 'options' );
if( $feat_cont_type === 'collection' ){
  $feat_cont = new ContentNodeCollection( $feat_cont_id );
} else {
  $feat_cont = new ContentNode( $feat_cont_id );
}

$curated_items = get_field( 'curated_home_content', 'options' );

if( $curated_items ){
  foreach( $curated_items as $curated_item ){
    if( $curated_item['type'] === 'collection' ){
      $curated_by_id[] = $curated_item['curated_collection'];
      $curated[] = new ContentNodeCollection( $curated_item['curated_collection'] );
    } else {
      $curated_by_id[] = $curated_item[ 'curated_'.$curated_item['type'] ];
      $curated[] = new ContentNode( $curated_item[ 'curated_'.$curated_item['type'] ] );
    }
  }
} else {
  $curated_by_id = [];
  $curated = [];
}
$curated_count = count( $curated );
$total_results = 11;

//limit total results to number set above
if( $curated_count < $total_results ){

  // get interviews/transcripts
  $interviews_and_transcripts = get_posts( [
    'post_type' => ['timeline', 'interview'],
    'posts_per_page' => $total_results,
    'exclude' => $curated_by_id,
    'meta_query' => [
      [
        'key' => 'hide',
        'compare' => '!=',
        'value' => 1
      ]
    ]
  ] );

  // normalize data
  if( count( $interviews_and_transcripts ) ){
    foreach( $interviews_and_transcripts as $piece ){
      if( !in_array( $piece->ID, $curated_by_id ) ){
        $pieces[] = new ContentNode( $piece->ID );
      }
    }
  }

  // get collections
  $collections = get_terms( [
    'taxonomy' => ['collection'],
    'posts_per_page' => $total_results
  ] );

  // normalize data
  if( count( $collections ) ){
    foreach( $collections as $piece ){
      if( !in_array( $piece->term_id, $curated_by_id ) ){
        $pieces[] = new ContentNodeCollection( $piece->term_id );
      }
    }
  }

  // sort by publish date
  usort( $pieces, function( $b, $a ){
    return strcmp( $a->date, $b->date );
  } );

  $pieces = array_merge( $curated, $pieces );
  $pieces = array_slice( $pieces, 0, $total_results );


} else {
  $pieces = $curated;
}

?>

  <article class="homeFeat">
    <header class="post-header">
      <div class="post-type"><?= icon( $feat_cont->type, 'type' ); ?><?= ucfirst( $feat_cont->type ); ?></div>
    </header>
    <div class="homeFeat-inner">
      <?php if( $feat_cont->type === 'collection' ){ ?>
        <i class="post-meta-label">A collection of</i>
        <dl class="post-meta">
          <dt class="sr-only">Number of interviews:</dt>
          <dd>
            <?= icon( 'interview', 'type' ); ?>
            <?= $feat_cont->interview_count; ?> interview<?= $feat_cont->interview_count > 1 ? 's' : ''; ?>
          </dd>
          <?php if($feat_cont->timeline_count): ?>
          <dt class="sr-only">Number of timelines:</dt>
          <dd>
            <?= icon( 'timeline', 'type' ); ?>
            <?= $feat_cont->timeline_count; ?> timeline<?= $feat_cont->timeline_count > 1 ? 's' : ''; ?>
          </dd>
          <?php endif; ?>
        </dl>
      <?php } ?>
      <div class="post-image js-img" data-showcredit data-img="<?= $feat_cont->img; ?>">
        <a href="<?= $feat_cont->link; ?>">
          <?= wp_get_attachment_image( $feat_cont->img, 'feat_home' ); ?>
        </a>
      </div>
      <h2 class="post-title"><?= $feat_cont->title; ?></h2>
      <?php if( $feat_cont->excerpt ){ ?>
        <p class="post-excerpt"><?= $feat_cont->excerpt; ?></p>
      <?php } ?>
      <a class="post-link" href="<?= $feat_cont->link; ?>">View The <?= ucfirst( $feat_cont->type ); ?></a>
    </div>
  </article>

  <?php if( count($pieces) > 3 ){ ?>
  <section class="postRoll postRoll--featured">
      <?php for( $i=0; $i<3; $i++ ){ $pieces[$i]->html(); } ?>
  </section>

    <?php if( get_field('show_roll_home', 'option') ){ ?>
    <section class="postRoll postRoll--home">
      <?php for( $i=3; $i<count($pieces); $i++ ){ $pieces[$i]->html(); } ?>
    </section>
    <?php } ?>
  <?php } ?>

  <section class="siteDescription">
    <h2><?= get_field( 'site_description_header', 'options' ); ?></h2>
    <?= get_field( 'site_description', 'options' ); ?>
    <?php $links = get_field( 'site_description_links', 'options' ); ?>
    <?php if( $links ){ ?>
      <ul class="siteDescription-links">
      <?php foreach( $links as $link ){ ?>
        <li class="siteDescription-link">
          <a href="<?= get_the_permalink( $link['home_link'] ); ?>">
            <?= get_the_title( $link['home_link'] ); ?>
            <?= icon( 'right', 'link' ); ?>
          </a>
        </li>
      <?php } ?>
      </ul>
    <?php } ?>
  </section>

  <?php get_template_part('templates/buckets'); ?>
