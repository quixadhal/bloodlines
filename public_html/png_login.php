<?php
if (isset( $_REQUEST['mud_id'] ))
  $mud_id = $_REQUEST['mud_id'];
if (isset( $_REQUEST['filename'] ))
  $filename = $_REQUEST['filename'];

if (isset( $_REQUEST['percent'] ))
  $percent = $_GET['percent'];
if (isset( $_REQUEST['constrain'] ))
  $constrain = $_GET['constrain'];
if (isset( $_REQUEST['width'] ))
  $desired_width = $_GET['width'];
if (isset( $_REQUEST['height'] ))
  $desired_height = $_GET['height'];

$pictype = isset( $_REQUEST['jpg'] ) ? 'jpg' : 'png';

if (isset($mud_id)) {
  $dbconn = pg_connect("host=localhost dbname=mudlist user=quixadhal password=tardis69")
      or die('Could not connect: ' . pg_last_error());

  $query = "SELECT png_login FROM mudlist WHERE mud_id = $mud_id";
  $result = pg_query($query) or die('Query failed: ' . pg_last_error());

  $row = pg_fetch_object($result) or die('Fetch failed: ' . pg_last_error());

  pg_free_result($result);
  pg_close($dbconn);

  $rawimage = base64_decode($row->png_login);
  if(!$rawimage) {
      $rawimage = file_get_contents("gfx/ghostbusters.png");
  }
  $image = imagecreatefromstring($rawimage);
} elseif (isset($filename)) {
  $image = imagecreatefromstring(file_get_contents($filename));
} else {
  header("Content-type: text");
  echo "You must give me some data!\n";
  exit(1);
}

if (!$image) {
  header("Content-type: text");
  echo "Sorry, bad image data.\n";
  exit(1);
}

$image_width = imagesx($image);
$image_height = imagesy($image);

if ($image_width < 1)
  $image_width = 1;
if ($image_height < 1)
  $image_height = 1;
$aspect_ratio = $image_height / $image_width;

if (isset($percent) and $percent > 0 and $percent != 100) {
  $percent = $percent * 0.01;
  $width = $image_width * $percent;
  $height = $image_height * $percent;
} elseif (isset($desired_width) and !isset($desired_height)) {
  // Auto-scale to desired width
  $width = $desired_width;
  $height = @round($aspect_ratio * $desired_width);
} elseif (!isset($desired_width) and isset($desired_height)) {
  // Auto-scale to desired height
  $height = $desired_height;
  $width = @round($desired_height / $aspect_ratio);
} elseif (isset($desired_width) and isset($desired_height) and isset($constrain)) {
  // We asked for it to fit into X by Y, so keep the aspect ratio and
  // make sure it fits on the smallest side.
  $height = @round($aspect_ratio * $desired_width);
  $width = @round($desired_height / $aspect_ratio);

  if ($width > $desired_width) {
    // Take the calculated height
    $width = $desired_width;
  } else {
    // Take the calculated width
    $height = $desired_height;
  }
} elseif (isset($desired_width) and isset($desired_height)) {
  // We asked for an exact width/height without constraint, OK.
  $width = $desired_width;
  $height = $desired_height;
} else {
  // We didn't ask for any changes, so just output the pure image.
  $width = null;
  $height = null;
}

if($pictype === 'jpg') 
  header("Content-type: image/jpeg");
else
  header("Content-Type: image/png");
header("Content-Disposition: inline");

if (isset($width) and isset($height)) {
  // We scaled the image somehow, so DO it.
  $thumb = @ImageCreateTrueColor($width, $height);
  @ImageCopyResampled($thumb, $image, 0, 0, 0, 0, $width, $height, $image_width, $image_height);
  if($pictype === 'jpg') 
    imagejpeg($thumb);
  else
    imagepng($thumb);
  imagedestroy($thumb);
} else {
  // No scaling requested, so just output it raw.
  if($pictype === 'jpg') 
    imagejpeg($image);
  else
    imagepng($image);
}
imagedestroy($image);
?>

