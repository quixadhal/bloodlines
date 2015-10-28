<?php

$my_url = sprintf( 'http%s://%s%s%s',
                   (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == TRUE ? 's': ''),
                   $_SERVER['SERVER_NAME'],
                   (isset($_SERVER["SERVER_PORT"]) ? ":".$_SERVER["SERVER_PORT"] : ""),
                   $_SERVER['REQUEST_URI'] );

if (isset( $_REQUEST['zoom'] ))
  $zoom = $_REQUEST['zoom'];
else
  $zoom = 1;
if ( !is_numeric( $zoom )  || ( $zoom < 1 ) || ( $zoom > 3 ) )
  $zoom = 1;

if (isset( $_REQUEST['offset'] ))
  $offset = $_REQUEST['offset'];
else
  $offset = 0;
if ( !is_numeric( $offset )  || ( $offset < 0 ) )
  $offset = 0;

if (isset( $_REQUEST['limit'] ))
  $limit = $_REQUEST['limit'];
else {
  global $limit;
  if( $zoom == 1 ) {
    $limit = 30;
  } elseif( $zoom == 2 ) {
    $limit = 16;
  } elseif( $zoom == 3 ) {
    $limit = 9;
  } else {
    $limit = 30;
  }
}
if ( !is_numeric( $limit )  || ( $limit < 1 ) )
  $limit = 20;

if (isset( $_REQUEST['reversed'] ))
  $reversed = 1;
else
  $reversed = 0;

if (isset( $_REQUEST['order_by'] ))
  $order_by = $_REQUEST['order_by'];
if ( !is_numeric( $order_by )  || ( $order_by < 1 ) )
  $order_by = 2;

if( $order_by == 1 ) {
  $order_by_term = "ORDER BY mud_id " . ($reversed ? "DESC" : "ASC");
} else if( $order_by == 2 ) {
  $order_by_term = "ORDER BY UPPER(name) " . ($reversed ? "DESC" : "ASC");
} else if( $order_by == 3 ) {
  $order_by_term = "ORDER BY UPPER(site), port " . ($reversed ? "DESC" : "ASC");
} else {
  $order_by_term = "ORDER BY mud_id " . ($reversed ? "DESC" : "ASC");
}

if (isset( $_REQUEST['search'] )) {
  $search_param = $_REQUEST['search'];
  //print "Search: $search_param<br>";
} else {
  $search_param = "";
}
$search_term = "";
if($search_param !== "") {
    $search_term = str_replace("?", "%", $search_param);
    $search_term = str_replace("*", "%", $search_term);
    $search_term = preg_replace("/[^a-zA-Z0-9_% .:,']+/", "", $search_term);
    if(substr($search_term, -1) != "%") {
        $search_term .= "%";
    }
    $search_term = pg_escape_string($search_term);
    //print "SQL: $search_term<br>";
}

// $result = pg_query_params($dbconn, 'SELECT * FROM shops WHERE name = $1', array("Joe's Widgets"));
// $my_data = pg_escape_string(utf8_encode($_POST['my_data'])); 

function get_count() {
  global $search_term, $order_by_term;

  if($search_term !== "") {
    $query = "SELECT count(*) AS count FROM mudlist WHERE live AND name ILIKE $1";
    $result = pg_query_params($query, array($search_term)) or die('Query failed: ' . pg_last_error());
  } else {
    $query = "SELECT count(*) AS count FROM mudlist WHERE live $search_term";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
  }
  $row = pg_fetch_object($result);
  if(!$row) 
      return 0;
  pg_free_result($result);
  return $row->count;
}

function get_last_verified() {
  global $search_term, $order_by_term;

  if($search_term !== "") {
    $query = "SELECT to_char(last_verified, 'HH:MI:SS am on FMDay, DD FMMonth, YYYY TZ') AS last_verified FROM mudlist WHERE live AND name ILIKE $1 ORDER BY last_verified DESC LIMIT 1";
    $result = pg_query_params($query, array($search_term)) or die('Query failed: ' . pg_last_error());
  } else {
    $query = "SELECT to_char(last_verified, 'HH:MI:SS am on FMDay, DD FMMonth, YYYY TZ') AS last_verified FROM mudlist WHERE live $search_term ORDER BY last_verified DESC LIMIT 1";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
  }
  $row = pg_fetch_object($result);
  if(!$row) 
      return 0;
  pg_free_result($result);
  return $row->last_verified;
}

function get_muds( $offset, $limit ) {
  global $search_term, $order_by_term;

  if($search_term !== "") {
    $query = "SELECT mud_id, name FROM mudlist WHERE live AND name ILIKE $1 $order_by_term OFFSET $offset LIMIT $limit";
    $result = pg_query_params($query, array($search_term)) or die('Query failed: ' . pg_last_error());
  } else {
    $query = "SELECT mud_id, name FROM mudlist WHERE live $search_term $order_by_term OFFSET $offset LIMIT $limit";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
  }
  $rows = array();
  while( $row = pg_fetch_object($result) ) {
    array_push( $rows, $row ); 
  }
  pg_free_result($result);
  return $rows;
}



$dbconn = pg_connect("host=localhost dbname=mudlist user=quixadhal password=tardis69")
    or die('Could not connect: ' . pg_last_error());

$mud_count = get_count();
$last_verified = get_last_verified();

$muds = get_muds( $offset, $limit );
$result_count = count( $muds );

if( $zoom == 1 ) {
  global $font_size, $pic_scale;

  $font_size = -2;
  $pic_scale = 0.5;
} elseif( $zoom == 2 ) {
  global $font_size, $pic_scale;

  $font_size = -1;
  $pic_scale = 0.75;
} elseif( $zoom == 3 ) {
  global $font_size, $pic_scale;

  $font_size = 0;
  $pic_scale = 1.0;
}

$pic_width = 240 * $pic_scale;
$pic_height = 150 * $pic_scale;
$text_height = 20;
$col_factor = floor(800 / $pic_width);
$col_count = min($col_factor, max($col_factor, $result_count));
$row_count = ceil($limit / $col_count);
$offset_delta = $row_count * $col_count;

//$dark_row = "#DDDDDD";
//$light_row = "#DDFFDD";
$dark_row = "#303030";
$light_row = "#505050";
/*
echo "mud_count: $mud_count<br>";
echo "last_verified: $last_verified<br>";
echo "offset: $offset<br>";
echo "limit: $limit<br>";
echo "result_count: $result_count<br>";
echo "col_count: $col_count<br>";
echo "row_count: $row_count<br>";
*/

?>
<html>
  <head>
    <script type="text/javascript">
        function JumpTo(theUrl) {
            document.location.href = theUrl;
        }
    </script>
    <title> <?php echo $mud_count; ?> Living MUD's </title>
  </head>
  <body background="gfx/dark_wood.jpg" bgcolor="#505050" text="#d0d0d0" link="#ffffbf" vlink="#ffa040">
    <style type="text/css">
        table.mud {
            color: #d0d000;
            border-color: rgb(52, 52, 0);
        }
        table.mud th {
            color: #d0d000;
            border-color: rgb(52, 52, 0);
        }
        table.mud td {
            color: #d0d000;
            border-color: rgb(52, 52, 0);
        }
    </style>
    <table border=0 cellspacing=0 cellpadding=0 width=80% align="center">
    <tr>
                <td align="right" valign="top">
                    <a href="/~bloodlines">
                        <img src="gfx/bloodlines.png" border=0 width=234 height=80 alt="(Bloodlines:)">
                    </a>
                </td>
                <td align="left" valign="bottom">
                    <a href="/~bloodlines">
                        <img src="gfx/wileymud4.png" border=0 width=177 height=40 alt="(WileyMUD IV)">
                    </a>
                </td>
    </tr>
    <tr><td colspan="2">

    <table border="0" cellspacing="0" cellpadding="1" width="100%">
    <tr width="<?php echo $pic_width * $col_count; ?>">
    <td width="<?php echo $pic_width * $col_count; ?>">

    <table class="mud" border="1" cellspacing="0" cellpadding="1" width="<?php echo $pic_width * $col_count; ?>">
      <tr height="<?php echo $text_height; ?>">
          <td colspan="<?php echo $col_count * 2; ?>" align="center"> <?php echo $mud_count; ?> Living MUD's </td>
      </tr>
      <?php for( $i = 0; $i < $row_count; $i++ ) { ?>
      <tr height="<?php echo $pic_height + $text_height; ?>">
        <?php for( $j = 0; $j < $col_count && (($i * $col_count) + $j < $result_count); $j++ ) {
             $mud_id = $muds[($i * $col_count) + $j]->mud_id;
             $mud_name = $muds[($i * $col_count) + $j]->name;
             $bgcolor = ( $i % 2 ) ? $dark_row : $light_row;
        ?>
          <!-- <td colspan="2" align="center" width="<?php echo $pic_width; ?>" bgcolor="<?php echo $bgcolor;?>"> -->
          <td colspan="2" align="center" width="<?php echo $pic_width; ?>" onclick="JumpTo('<?php echo "mud_entry.php?mud_id=$mud_id"; ?>');">
            <a href="<?php echo "mud_entry.php?mud_id=$mud_id"; ?>">
              <img border="0" src="<?php echo "png_login.php?mud_id=$mud_id&width=$pic_width&height=$pic_height&constrain"; ?>">
            </a>
          </td>
        <?php } ?>
      </tr>
      <tr height="<?php echo $text_height; ?>">
        <?php for( $j = 0; $j < $col_count && (($i * $col_count) + $j < $result_count); $j++ ) {
             $mud_id = $muds[($i * $col_count) + $j]->mud_id;
             $mud_name = $muds[($i * $col_count) + $j]->name;
             $bgcolor = ( $i % 2 ) ? $dark_row : $light_row;
        ?>
          <!-- <td align="left" width="75%" bgcolor="<?php echo $bgcolor;?>"><font size="<?php echo $font_size; ?>"> <?php echo $mud_name; ?> </font></td> -->
          <td align="center" width="70%"><font size="<?php echo $font_size; ?>"> <?php echo $mud_name; ?> </font></td>
          <!-- <td align="right" width="25%" bgcolor="<?php echo $bgcolor;?>"><font size="<?php echo $font_size; ?>"> #&nbsp;<?php printf( "%04d", $mud_id ); ?> </font></td> -->
          <td align="right" width="30%"><font size="<?php echo $font_size; ?>"> <?php printf( "%05d", $mud_id ); ?> &nbsp; </font></td>
        <?php } ?>
      </tr>
      <?php } ?>
    </table>

  </td>
  <td align="right" valign="top">
    <table border="0" cellspacing="0" cellpadding="1" width="100%">
      <tr>
        <td colspan="2" align="right">
          <form action="<?php echo $my_url; ?>">
            <table border="0" cellspacing="0" cellpadding="1" width="100%">
                <tr valign="center">
                    <td align="left">
                        <fieldset>
                            <legend>Search</legend>
                            <input type="text" name="search" value="<?php echo $search_param; ?>" size="20" maxlength="20">
                        </fieldset>
                    </td>
                </tr>
              <tr valign="top">
                <td align="left">
                    <fieldset>
                      <legend>Zoom Level</legend>
                      <input type="radio" name="zoom" value="1" <?php if($zoom == 1) echo "checked"; ?>>1<br>
                      <input type="radio" name="zoom" value="2" <?php if($zoom == 2) echo "checked"; ?>>2<br>
                      <input type="radio" name="zoom" value="3" <?php if($zoom == 3) echo "checked"; ?>>3<br>
                    </fieldset>
                </td>
</tr><tr>
                <td align="left">
                    <fieldset>
                      <legend>Order by</legend>
                      <input type="radio" name="order_by" value="1" <?php if($order_by == 1) echo "checked"; ?>>ID<br>
                      <input type="radio" name="order_by" value="2" <?php if($order_by == 2) echo "checked"; ?>>Name<br>
                      <input type="radio" name="order_by" value="3" <?php if($order_by == 3) echo "checked"; ?>>Site/Port<br>
                      <input type="checkbox" name="reversed" value="1" <?php if($reversed == 1) echo "checked"; ?>>reversed<br>
                    </fieldset>
                </td>
              </tr>
            </table>
            <input type="hidden" name="offset" value="<?php echo $offset; ?>">
            <input type="submit" value="Update">
          </form>
        </td>
      </tr>
      <tr height="<?php echo $text_height; ?>">
          <td align="left" width="50%"><font size="<?php echo $font_size; ?>">
            <?php global $my_back_url, $back_offset, $offset;
                  $pat = '/offset=(\d+)/i';
                  $back_offset = $offset - $offset_delta;
                  if( $back_offset < 0 )
                    $back_offset = 0;
                  $rep = "offset=$back_offset";
                  if( preg_match( $pat, $my_url ) == 0 ) {
                    if( preg_match( '/\?/i', $my_url ) == 0 )
                      $my_back_url = $my_url . "?offset=$back_offset";
                    else
                      $my_back_url = $my_url . "&offset=$back_offset";
                  } else {
                    $my_back_url = preg_replace( $pat, $rep, $my_url );
                  }
                  if( $offset > $offset_delta ) {
            ?>
              <a href="<?php echo "$my_back_url"?>">Back&nbsp;<?php echo "$offset_delta ($back_offset)"; ?></a>
            <?php } else { ?>
              <a href="<?php echo "$my_back_url"?>">Beginning</a>
            <?php } ?>
            </a>
          </font></td>
          <td align="right" width="50%"><font size="<?php echo $font_size; ?>">
            <?php global $my_forward_url, $forward_offset, $offset;
                  $pat = '/offset=(\d+)/i';
                  $forward_offset = $offset + $offset_delta;
                  if( $forward_offset > $mud_count + $offset_delta )
                    $forward_offset = $mud_count + $offset_delta;
                  $rep = "offset=$forward_offset";
                  if( preg_match( $pat, $my_url ) == 0 ) {
                    if( preg_match( '/\?/i', $my_url ) == 0 )
                      $my_forward_url = $my_url . "?offset=$forward_offset";
                    else
                      $my_forward_url = $my_url . "&offset=$forward_offset";
                  } else {
                    $my_forward_url = preg_replace( $pat, $rep, $my_url );
                  }
                if( $offset < $mud_count - $offset_delta ) {
            ?>
              <a href="<?php echo "$my_forward_url"?>">Forward&nbsp;<?php echo "$offset_delta ($forward_offset)"; ?></a>
            <?php } else { ?>
              <a href="<?php echo "$my_forward_url"?>">End</a>
            <?php } ?>
            </a>
          </font></td>
      </tr>
    </table>
  </td>
  </tr>
  </table>
  </td></tr></table>
  </body>
</html>

