<?php

require_once LIB.'base/ent.php';

class Gatekeeper {
  
  private 
    $keybox_ = NOT_SET,
    $main_versions_ = NOT_SET;
  
  public function __construct() {
    // Build the $keybox.
    $this->keybox_ = array(
      'ios_v0.1'=>'semi-god-semi-devil-v0.1-acdjiac5tq-ios',
      'android_v0.1'=>'semi-god-semi-devil-v0.1-acdjiac5tq-android');
    
    $this->main_versions_ = array(
      'ios_v0.1'=>1, 
      'android_v0.1'=>1);
  }
  
  public function validatePost($parameters) {
    
    if (isset($parameters['UDID']) &&
        isset($parameters['KEY']) &&
        isset($parameters['CONTENT'])) {
      error_log('OK');
      $udid = $parameters['UDID'];
      $key = $parameters['KEY'];
      $content = $parameters['CONTENT'];
      $content['UDID'] = $udid;
      $client = array_search($key, $this->keybox_);
    
      if (!isset($this->main_versions_[$client]) ) {
        log_message('info', 'A post from non-supporting version.');
        return FALSE;
      } else {
        return $content;
      }
    } else {
      log_message('info', 'A post from invalid source.');
      return FALSE;
    }
  }
}