<?php
include 'lib/plist.php';

$doc = Plist::parse($_FILES['file']['tmp_name']);

$track_names = array(); // needed cuz playlists refer to tracks by id numbers
foreach($doc['Tracks'] as $track) {
  $track_name = $track['Name'];
  // push id info
  $track_names[$track['Track ID']] = $track_name;
  // set data
  Graph::set($track_name,'type','song');
  Graph::set($track_name,'Artist',$track['Artist']);
  Graph::set($track_name,'Album',$track['Album']);
  Graph::set($track_name,'Genre',$track['Genre']);
  // set artist & album data
  Graph::set($track['Artist'],'type','artist');
  Graph::set($track['Album'],'type','album');
  // description
  Graph::describe($track_name,"<audio src='$track[Location]' controls='true'><p class='notice'>your browser doesn't support the HMTL 5 audio tag.</p></audio>");
}
// get playlists
foreach($doc['Playlists'] as $playlist) {
  $members = array();
  foreach($playlist['Playlist Items'] as $track) {
    $track_id = $track['Track ID'];
    $members[] = $track_names[$track_id];
  }
  Graph::set($playlist['Name'],'members',$members);
}

redirect('');
?>