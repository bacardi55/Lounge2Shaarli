<?php

require_once('include/pdo55.class.php');

class htmlBookmark55 {
  protected $output;
  protected $privateNdds;
  protected $connection;
  protected $newLinks;
  protected $fileName;

  /* Constructor */
  public function __construct($file = null, Array $private = array()) {
    $isFileExists = false;
    $this->connection = pdo55::getInstance();

    if ($file) {
      $this->filename = $file;
    }
    else {
      $this->filename = 'bookmark.html';
    }

    if (count($private)) {
      $this->privateNdds = $private;
    }

    if(!$isFileExists) {
    }
    $this->newLinks = array();
  }

  /* Get / Set */
  public function getOutput() {
    return $this->output;
  }

  public function setOutput($output) {
    $this->output = $output;
  }

  public function getPrivateNdds() {
    return $this->privateNdds;
  }

  public function setPrivateNdds(Array $private = array()) {
    $this->privateNdds = $private;
    $this->privateNddsInKey = array_flip($this->privateNdds);
  }

  /* Public Class Method */
  /**
   * Method that generate the file with all bookmarks
   * @param Boolean Whether to display the content in the output
   *                or in a file
   * @return void
   */
  public function createBookmarkFile($fake = false) {
    $this->getAllFavLinks();
    $this->createCompleteOutput();

    if(!$fake)
      $this->createFile();
    else {
      echo htmlentities($this->output);
    }
  }

  /**
   * Get all feed from DB
   * @param void
   * @return array
   */
  public function getAllFeed() {
    $retour = array();
    $query = 'SELECT url, name
      FROM feeds WHERE 1';

    $q = pdo55::getInstance()->query($query);
    foreach ($q as $key => $value) {
      $retour[$value['url']] = $value['name'];
    }
    
    return $retour;
  }

  /* Protected Class Method */
  /**
   * return an Array with all links
   * @param @void
   * @return @void
   */
  protected function getAllFavLinks() {
    $query = "
      SELECT title, content, starred, datetime, link, url, category, feeds.name, categories.name AS tag
      FROM `items`
      LEFT JOIN feeds ON items.feed = feeds.id
      LEFT JOIN categories ON categories.id = feeds.category
      WHERE starred =1";
    $return = pdo55::getInstance()->query($query);
    $this->generateArray($return);
  }

  /**
   * Create the all output in $this->ouput
   * @param void
   * @return void
   */
  protected function createCompleteOutput() {
    $links = $this->newLinks;
    for($i = 0, $nb = count($links); $i < $nb; ++$i) {
      $this->addEntry($links[$i]);
    }
  }

  /**
   * Create file
   * @param void
   * @return void
   */
  protected function createFile() {
    if(!$this->check()) {
      throw new Exception('');
    }
    
    if(is_file($this->filename)) {
      $ret = file_put_contents($this->filename, $this->output, FILE_APPEND);
    }
    else {
      $intro = '<!DOCTYPE NETSCAPE-Bookmark-file-1>
        <!-- This is an automatically generated file.
             It will be read and overwritten.
             DO NOT EDIT! -->
        <!-- Shaarli all bookmarks export on 2012/08/27 14:01:10 -->
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
        <TITLE>Bookmarks</TITLE>
        <H1>Bookmarks</H1>';
      $ret = file_put_contents($this->filename, $intro . $this->output);
    }

    if(false === $ret) {
      throw new Exception('File couldn\'t be saved');
    }
  }

  /**
   * Add an new link in output
   * @param Array
   * @return void
   */
  protected function addEntry($links) {
    if (!is_array($links)) {
      throw new Exception('Link configuration need to be an Array');
    }

    $this->output .= '<DT>';
    $this->output .= $this->createEntryLink($links);

    if ($links['content']) 
      $this->output .= $this->createEntryDescription($links['content']);
  }

  /**
   * Create the <a></a> tag for a link
   * @param Array
   * @return string
   */
  protected function createEntryLink(Array $config) {
    // href, add_date, private, tags
    $output = '<A HREF="' . $config['link'] . '"'
      . ' ADD_DATE="' . $config['datetime']. '"'
      . ' PRIVATE="' . ($config['secret']? 1 : 0) . '"'
      . ' TAGS="' . $config['tag'] . '"'
      . '>'. $config['title'] .'</A>';

    return $output;
  }

  /**
   * Create the <DD> tag for a link
   * @param String
   * @return String
   */
  protected function createEntryDescription($description) {
    return '<DD>' . $description;
  }

  /**
   * Generate the array with all links informations
   * @param PDO result
   * @return void
   */
  protected function generateArray($results) {
    $retour = array();
    foreach ($results as $key => $result) {
      $retour[] = array(
        'title'    => $result['title'], 
        'content'  => substr(trim(strip_tags($result['content'])), 0, 140), 
        'datetime' => $this->getTimeStamp($result['datetime']),
        'link'     => $result['link'], 
        'secret'   => $this->isSecretFeed($result['url']), 
        'tag'      => $result['tag'], 
      );
    }

    $this->newLinks = $retour;
  }

  /**
   * Check if the feed should be private
   * @param String
   * @return Boolean
   */
  protected function isSecretFeed($url) {
    if(!is_array($this->privateNdds) 
      || !count($this->privateNdds)) {

      return false;  
    }
    
    if(array_key_exists($url, $this->privateNddsInKey)) {
      return true;
    }
  
    return false;
  }

  /**
   * Return a time stamp from a string complete date
   * @param String
   * @return Timestamp
   */
  protected function getTimeStamp($datetime){
    $tmp   = explode(' ', $datetime); // 1: date; 2: heure
    $heure = explode(':', $tmp[1]); // h min s
    $date  = explode('-', $tmp[0]); // y m d

    return mktime($heure[0], $heure[1], $heure[2], $date[1], $date[2], $date[0]);
  }
}
