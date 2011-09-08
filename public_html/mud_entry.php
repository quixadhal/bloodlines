<?php

function mudconnectURL( $mud_name ) {
  $baseurl = 'http://mudconnect.com/mud-bin/adv_search.cgi?Mode=MUD&mud=';
  $name = preg_replace("/\s+/mix", "+", $mud_name);
  return $baseurl . $name;
}

if (isset( $_REQUEST['mud_id'] ))
  $mud_id = $_REQUEST['mud_id'];

if (isset($mud_id)) {
  $my_url = sprintf( 'http%s://%s%s',
                     (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == TRUE ? 's': ''),
                     $_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI'] );

  $dbconn = pg_connect("host=localhost dbname=mudlist user=quixadhal password=tardis69")
      or die('Could not connect: ' . pg_last_error());

  $query = "SELECT name, site, port, to_char(last_verified, 'YYYY.MM.DD') AS last_verified FROM mudlist WHERE mud_id = $mud_id";
  $result = pg_query($query) or die('Query failed: ' . pg_last_error());
  $row = pg_fetch_object($result) or die('Fetch failed: ' . pg_last_error());
  pg_free_result($result);
  pg_close($dbconn);

} else {
  header("Content-type: text");
  echo "You must give me some data!\n";
  exit(1);
}
?>
<html>
  <head>
    <title> <?php echo $row->name; ?> </title>
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

    <table class="mud" border="1" cellspacing="0" cellpadding="1" width="100%">
      <tr height="20">
        <td align="left" width="25%"> &nbsp;ID&nbsp;#&nbsp;<?php printf( "%04d %s", $mud_id, $row->name ); ?></td>
        <td align="left" rowspan="4"> <img border="0" src="<?php echo "png_login.php?mud_id=$mud_id"; ?>"> </td>
      </tr>
      <tr height="20">
        <td align="center">
             &nbsp; <a href="telnet://<?php echo $row->site; ?>:<?php echo $row->port; ?>">PLAY</a>
             &nbsp; <a href="<?php echo mudconnectURL($row->name); ?>">LOOKUP</a>
             &nbsp;
        </td>
      </tr>
      <tr height="20">
        <td align="center">&nbsp;Last Verified: <?php echo $row->last_verified; ?></td>
      </tr>
      <tr>
        <td> &nbsp; </td>
      </tr>
    </table>
  </td>
  </tr>
  </table>
  </body>
</html>

