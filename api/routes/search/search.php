<?php
include_once(get_template_directory().'/models/ContentNode.php');
include_once("search-helpers.php");
$search = new Route('/search/(?P<term>.*)', 'GET', function($data){
  $args = $data->get_query_params();
  $term = str_replace('+', ' ', urldecode($data['term']));

  if(strlen($term) < 4) {
    return [
      "term" => $term,
      "error" => "Search term too short.",
      "message" => "Try a longer search."
    ];
  }

  function get_collection_arg($args) {
    if(isset($args['collection'])) {
      return [
        [
          'taxonomy' => 'collection',
          'field' => 'term_id',
          'terms' => $args['collection']
        ]
      ];
    }
    else return null;
  }

  $results = array_merge(
    $posts_search = get_posts([
      'post_type' => [ 'timeline', 'interview' ],
      'posts_per_page' => -1,
      's' => $term,
      'tax_query' => get_collection_arg($args)
    ]),
    get_posts([
      'post_type' => [ 'timeline', 'interview' ],
      'posts_per_page' => -1,
      // 'suppress_filters' => false,
      'tax_query' => get_collection_arg($args),
      'meta_query' => [
        'relation' => 'OR',
        [
          'key' => 'transcript_raw',
          'value' => $term,
          'compare' => 'LIKE'
        ],
        [
          'key' => 'description_raw',
          'value' => $term,
          'compare' => 'LIKE'
        ],
        [
          'key' => 'supporting_content_raw',
          'value' => $term,
          'compare' => 'LIKE'
        ],
        [
          'key' => 'events_$_title',
          'value' => $term,
          'compare' => 'LIKE'
        ],
        [
          'key' => 'events_$_content',
          'value' => $term,
          'compare' => 'LIKE'
        ]
      ],
      // Prevent duplicate terms from previous query
      'exclude' => array_reduce($posts_search, function($excluded_posts, $post) {
        $excluded_posts[] = $post->ID;
        return $excluded_posts;
      }, [])
    ]),
    $terms_search = (!isset($args['collection']) ? get_terms([
      'number' => 0,
      'search' => $term
    ]) : []),
    (!isset($args['collection']) ? get_terms([
      'taxonomy' => 'collection',
      'meta_query' => [
        [
          'key' => 'collection_description',
          'value' => $term,
          'compare' => 'LIKE'
        ]
      ],
      // Prevent duplicate terms from previous search
      'exclude' => array_reduce($terms_search, function($excluded_terms , $term) {
        $excluded_terms[] = $term->term_id;
        return $excluded_terms;
      }, [])
    ]) : [])
 );

  $count = isset($args['count']) ? $args['count'] : false;
  $offset = isset($args['offset']) ? $args['offset'] : false;

  $total_results = count($results);
  $total_hits = 0;

  $fields = [
    'interview' => [
      'introduction' => function($id) {
        $lines = get_lines_from_sentences(get_field('introduction', $id));
        return $lines;
      },
      'transcript_raw',
      'description_raw',
      'supporting_content_raw'
     ],
    'timeline' => [
      'introduction' => function($id) {
        return get_lines_from_sentences(get_field('introduction', $id));
      },
      'content' => function($id) {
        $nodes = get_field('events', $id);
        $content = '';
        foreach($nodes as $node) {
          $content .= $node['title'] . ' - ' . strip_tags($node['content']);
        }
        return $content;
      }
    ],
    'collection' => [
      'collection_descripton' => function($term_id) {
        return get_lines_from_sentences(get_field('collection_description', 'collection_'.$term_id));
      }
    ]
  ];

  // Loop over all results
  foreach($results as $result) {
    if(isset($result->ID)){
      $item = new ContentNode($result->ID);
    } else {
      $item = new ContentNodeCollection($result->term_id);
    }

    $hits = [];

    foreach($fields[$item->type] as $key => $field) {
      if(is_string($field)) {
        $value = clean_vtt(get_field($field, $item->id));
        $timestamp_method = $field;
      } else {
        $value = clean_vtt($field($item->id));
        $timestamp_method = $key;
      }
      $lines = get_matching_lines($value, $term, $timestamp_method);
      $hits = array_merge($hits, $lines);
    }

    $item->hits = $hits;
    $item->title = highlight_term($item->title, $term);

    if(count($hits)) {
      $total_hits = $total_hits + count($hits);
    }

    $returns['items'][] = $item;
  }

  if($count !== false && $offset !== false){
    $results = array_slice($results, $offset, $count);
  }

  $returns['name'] = 'Search for '.$data['term'];
  $returns['total_hits'] = $total_hits;
  $returns['results'] = $total_results;
  return $returns;
});
