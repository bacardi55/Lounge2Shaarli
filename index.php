<?php 
// Include
include('htmlBookmark55.class.php');

// Class Instance
$htmlBookmark55 = new htmlBookmark55('test.txt'); 

if($_POST && $_POST['Envoyer'] == 'Envoyer') {
  $ndds = $_POST['private'];
  $ndds = array_keys($ndds);
  
  $htmlBookmark55->setPrivateNdds($ndds);
  $htmlBookmark55->createBookmarkFile();
}
else {
  $feed = $htmlBookmark55->getAllFeed();
  
  $html = '<p>Select the rss feed that will have private link</p>';
  $html .= '<form action="" method="POST">';

  foreach($feed as $url => $name) {
    $html .= '<input type="checkbox" value="'.$url.'" name="private['.$url.']">'.$name.'</input><br/>';
  }

  $html .= '<input type="submit" name="Envoyer" value="Envoyer">';
  $html .= '</form>';

  echo $html;
}
