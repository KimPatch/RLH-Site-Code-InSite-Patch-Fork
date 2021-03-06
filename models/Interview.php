<?php

include_once('Content.php');

class Interview extends Content {
  function __construct($id){
    parent::__construct($id);
    $this->introduction    = get_field('introduction', $id);
    $this->instructions    = get_field('interview_instructions', 'option');
    $this->video_id        = get_field('youtube_id', $id);
    $this->transcript_url  = get_field('transcript', $id)['url'];
    $this->description_url = get_field('description', $id)['url'];
    $this->no_media        = get_field('no_media', $id);
  }
}
