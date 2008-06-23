<?php
include 'lib/plist.php';

$doc = Plist::parse($_FILES['file']['tmp_name']);

$track_names = array(); // needed cuz playlists refer to tracks by id numbers
foreach($doc['Tracks'] as $track) {
  $track_name = $track['Name'];
  // push id info
  $track_names[$track['Track ID']] = $track_name;
  // set data
  BQL::set($track_name,'type','song');
  BQL::set($track_name,'Artist',$track['Artist']);
  BQL::set($track_name,'Album',$track['Album']);
  BQL::set($track_name,'Genre',$track['Genre']);
  // set artist & album data
  BQL::set($track['Artist'],'type','artist');
  BQL::set($track['Album'],'type','album');
  // description
  BQL::describe($track_name,"<audio src='$track[Location]' controls='true'><p class='notice'>your browser doesn't support the HMTL 5 audio tag.</p></audio>");
}
// get playlists
foreach($doc['Playlists'] as $playlist) {
  $members = array();
  foreach($playlist['Playlist Items'] as $track) {
    $track_id = $track['Track ID'];
    $members[] = $track_names[$track_id];
  }
  BQL::set($playlist['Name'],'members',$members);
}

redirect('');
?>