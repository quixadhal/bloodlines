<?php
$time_start = microtime(true);
$now = date('g:ia \o\n l, \t\h\e jS \o\f F, Y');

define("TEXT_FILE", "/home/bloodlines/lib/secure/log/allchan.log");
define("ARCHIVE",   "/home/bloodlines/lib/secure/log/archive/allchan.log-*");
define("CHATTER",   "/home/bloodlines/lib/secure/save/chat.o");
define("MUD_NAME",  "WileyMUD IV: Bloodlines");
define("RSS_URL",   "http://www.shadowlord.org:8088/~bloodlines/i3log.php?fm=rss");

//$speakers = array();
//$channels = array();
$colormap = array();
//$current_speaker = 0;
//$current_channel = 0;
$max_page = 0;

$colors = array(
    "<SPAN style=\"color: #bb0000\">",
    "<SPAN style=\"color: #00bb00\">",
    "<SPAN style=\"color: #bbbb00\">",
    "<SPAN style=\"color: #0000bb\">",
    "<SPAN style=\"color: #bb00bb\">",
    "<SPAN style=\"color: #00bbbb\">",
    "<SPAN style=\"color: #bbbbbb\">",
    "<SPAN style=\"color: #555555\">",
    "<SPAN style=\"color: #ff5555\">",
    "<SPAN style=\"color: #55ff55\">",
    "<SPAN style=\"color: #ffff55\">",
    "<SPAN style=\"color: #5555ff\">",
    "<SPAN style=\"color: #ff55ff\">",
    "<SPAN style=\"color: #55ffff\">",
    "<SPAN style=\"color: #ffffff\">",
);

$pinkfish_html = array(

'%^RESET%^'                 => '',

'%^BLACK%^'                 => '<SPAN style="color: #000000">',
'%^RED%^'                   => '<SPAN style="color: #bb0000">',
'%^GREEN%^'                 => '<SPAN style="color: #00bb00">',
'%^ORANGE%^'                => '<SPAN style="color: #bbbb00">',
'%^BLUE%^'                  => '<SPAN style="color: #0000bb">',
'%^MAGENTA%^'               => '<SPAN style="color: #bb00bb">',
'%^CYAN%^'                  => '<SPAN style="color: #00bbbb">',
'%^GREY%^'                  => '<SPAN style="color: #bbbbbb">',
'%^DARKGREY%^'              => '<SPAN style="color: #555555">',
'%^LIGHTRED%^'              => '<SPAN style="color: #ff5555">',
'%^LIGHTGREEN%^'            => '<SPAN style="color: #55ff55">',
'%^YELLOW%^'                => '<SPAN style="color: #ffff55">',
'%^LIGHTBLUE%^'             => '<SPAN style="color: #5555ff">',
'%^PINK%^'                  => '<SPAN style="color: #ff55ff">',
'%^LIGHTMAGENTA%^'          => '<SPAN style="color: #ff55ff">',
'%^LIGHTCYAN%^'             => '<SPAN style="color: #55ffff">',
'%^WHITE%^'                 => '<SPAN style="color: #ffffff">',

'%^B_RED%^%^WHITE%^'        => '<SPAN style="background-color: #bb0000; color: #ffffff">',
'%^B_GREEN%^%^WHITE%^'      => '<SPAN style="background-color: #00bb00; color: #ffffff">',
'%^B_BLUE%^%^WHITE%^'       => '<SPAN style="background-color: #0000bb; color: #ffffff">',
'%^B_MAGENTA%^%^WHITE%^'    => '<SPAN style="background-color: #bb00bb; color: #ffffff">',

'%^B_RED%^%^YELLOW%^'       => '<SPAN style="background-color: #bb0000; color: #ffff55">',
'%^B_GREEN%^%^YELLOW%^'     => '<SPAN style="background-color: #00bb00; color: #ffff55">',
'%^B_BLUE%^%^YELLOW%^'      => '<SPAN style="background-color: #0000bb; color: #ffff55">',
'%^B_MAGENTA%^%^YELLOW%^'   => '<SPAN style="background-color: #bb00bb; color: #ffff55">',

'%^B_RED%^%^BLACK%^'        => '<SPAN style="background-color: #bb0000; color: #000000">',
'%^B_GREEN%^%^BLACK%^'      => '<SPAN style="background-color: #00bb00; color: #000000">',
'%^B_MAGENTA%^%^BLACK%^'    => '<SPAN style="background-color: #bb00bb; color: #000000">',
'%^B_CYAN%^%^BLACK%^'       => '<SPAN style="background-color: #00bbbb; color: #000000">',
'%^B_YELLOW%^%^BLACK%^'     => '<SPAN style="background-color: #ffff55; color: #000000">',
'%^B_WHITE%^%^BLACK%^'      => '<SPAN style="background-color: #ffffff; color: #000000">',

'%^B_CYAN%^%^BLUE%^'        => '<SPAN style="background-color: #00bbbb; color: #0000bb">',
'%^B_YELLOW%^%^BLUE%^'      => '<SPAN style="background-color: #ffff55; color: #0000bb">',
'%^B_WHITE%^%^BLUE%^'       => '<SPAN style="background-color: #ffffff; color: #0000bb">',

'%^B_YELLOW%^%^GREEN%^'     => '<SPAN style="background-color: #ffff55; color: #00bb00">',
'%^B_WHITE%^%^GREEN%^'      => '<SPAN style="background-color: #ffffff; color: #00bb00">',

'%^B_BLACK%^%^GREY%^'       => '<SPAN style="background-color: #000000; color: #bbbbbb">',
'%^B_BLACK%^%^WHITE%^'      => '<SPAN style="background-color: #000000; color: #ffffff">',

'%^B_RED%^%^GREEN%^'        => '<SPAN style="background-color: #bb0000; color: #00bb00">',
'%^B_RED%^%^LIGHTGREEN%^'   => '<SPAN style="background-color: #bb0000; color: #00ff00">'
);

$hour_colors = array(
        "00" => "%^DARKGREY%^",
        "01" => "%^DARKGREY%^",
        "02" => "%^DARKGREY%^",
        "03" => "%^DARKGREY%^",
        "04" => "%^RED%^",
        "05" => "%^RED%^",
        "06" => "%^ORANGE%^",
        "07" => "%^ORANGE%^",
        "08" => "%^YELLOW%^",
        "09" => "%^YELLOW%^",
        "10" => "%^GREEN%^",
        "11" => "%^GREEN%^",
        "12" => "%^LIGHTGREEN%^",
        "13" => "%^LIGHTGREEN%^",
        "14" => "%^WHITE%^",
        "15" => "%^WHITE%^",
        "16" => "%^LIGHTCYAN%^",
        "17" => "%^LIGHTCYAN%^",
        "18" => "%^CYAN%^",
        "19" => "%^CYAN%^",
        "20" => "%^LIGHTBLUE%^",
        "21" => "%^LIGHTBLUE%^",
        "22" => "%^BLUE%^",
        "23" => "%^BLUE%^"
);

foreach ($hour_colors as $k => $v) {
    if( array_key_exists( $v, $pinkfish_html )) {
        $hour_colors[$k] = $pinkfish_html[$v];
    }
}

$channel_colors = array(
        "intermud"    => "%^B_BLACK%^%^GREY%^",
        "muds"        => "%^B_BLACK%^%^GREY%^",
        "connections" => "%^B_BLACK%^%^WHITE%^",
        "death"       => "%^LIGHTRED%^",
        "cre"         => "%^LIGHTGREEN%^",
        "admin"       => "%^LIGHTMAGENTA%^",
        "newbie"      => "%^B_YELLOW%^%^BLACK%^",
        "gossip"      => "%^B_BLUE%^%^YELLOW%^",

        "ds"          => "%^YELLOW%^",
        "dchat"	      => "%^CYAN%^",
        "intergossip" => "%^GREEN%^",
        "intercre"    => "%^ORANGE%^",

        "ibuild"      => "%^B_RED%^%^YELLOW%^",
        "ichat"       => "%^B_RED%^%^GREEN%^",
        "mbchat"      => "%^B_RED%^%^GREEN%^",
        "pchat"       => "%^B_RED%^%^LIGHTGREEN%^",
        "i2game"      => "%^B_BLUE%^",
        "i2chat"      => "%^B_GREEN%^",
        "i3chat"      => "%^B_RED%^",
        "i2code"      => "%^B_YELLOW%^%^RED%^",
        "i2news"      => "%^B_YELLOW%^%^BLUE%^",
        "imudnews"    => "%^B_YELLOW%^%^CYAN%^",
        "irc"         => "%^B_BLUE%^%^GREEN%^",
        "ifree"       => "%^B_BLUE%^%^GREEN%^",

        "default"      => "%^LIGHTBLUE%^",
        "default-IMC2" => "%^B_BLUE%^%^WHITE%^"
);

foreach ($channel_colors as $k => $v) {
    if( array_key_exists( $v, $pinkfish_html )) {
        $channel_colors[$k] = $pinkfish_html[$v];
    }
}

function get_chatter_colors() {
    global $colormap;
    global $pinkfish_html;

    $text = file_get_contents( CHATTER );
    $lines = explode("\n", $text);
    $line = $lines[1]; // Stores all on one long line...
    $line = substr($line, 11, -3);
    $mapping = explode(",", $line);
    $colormap = array();
    //foreach ($pinkfish_html as $k => $v) {
    //    echo "pinkfish_html[$k] = ".htmlentities($v)."<br>";
    //}
    for($i = 0; $i < sizeof($mapping); $i++ ) {
        $map = explode(":", $mapping[$i]);
        $mapname = substr($map[0], 1, -1); // Strip quotes
        $mapcolor = substr($map[1], 1, -1); // Strip quotes
        $colormap[$mapname] = $pinkfish_html[$mapcolor];
        //echo "colormap[$mapname] = ".htmlentities($colormap[$mapname])."<br>";
    }
    //return $colormap;
}

function read_file($filename, $lines_to_read) {
    $text = "";
    $pos = -1;
    $handle = fopen($filename, "r");

    while ($lines_to_read > 0) {
        --$pos;

        if(fseek($handle, $pos, SEEK_END) !== 0) {
            rewind($handle);
            $lines_to_read = 0;
        } elseif (fgetc($handle) === "\n") {
            --$lines_to_read;
        }

        $block_size = (-$pos) % 8192;
        if ($block_size === 0 || $lines_to_read === 0) {
            $text = fread($handle, ($block_size === 0 ? 8192 : $block_size)) . $text;
        }
    }

    fclose($handle);
    return explode("\n", $text);
}

function load_yesterday($filename) {
    $today = strftime("%m.%d", int($start_time));
    $yestertime = strftime("%H.%M", int($start_time - (60 * 60 * 24)));

    $text = file_get_contents( $filename );
    $lines = explode("\n", $text);
    $lines = preg_grep("/\t.*?\t.*?\t/", $lines);

    foreach ($lines as $line) {
        if( $line == "" ) {
            continue;
        }
        $parts = split("\t", $line);
        if( sizeof($parts) != 4 ) {
            continue;
        }
        $timestamp = substr($parts[0], 11, 5);
        $timestamp[2] = ':';
        $datestamp = substr($parts[0], 5, 5);
        $datestamp[2] = '/';

    }
}

function load_logs($filename) {
    global $chan_filter;
    global $speaker_filter;
    global $page_size;
    global $page_number;
    global $max_page;
    global $links_only;

    if(! isset($skip_lines)) {
        $skip_lines = 0;
    }
    $lines_needed = $page_size + ( $page_number * $page_size );
    $text = file_get_contents( $filename );
    $lines = explode("\n", $text);
    $lines = preg_grep("/\t.*?\t.*?\t/", $lines);
    if(isset($links_only)) {
        $lines = preg_grep('/((?:http|https|ftp)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?::[a-zA-Z0-9]*)?\/?(?:[a-zA-Z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*)+/', $lines);
        //$lines = preg_grep('/http/', $lines);
    }
    if(isset($chan_filter)) {
        $lines = preg_grep("/\t$chan_filter\t/", $lines);
    }
    if(isset($speaker_filter)) {
        $lines = preg_grep("/\t$speaker_filter\t/", $lines);
    }
    //echo "File: $filename<br>";
    //echo "Lines: " . sizeof($lines) . "<br>";
    //echo "Needed: $lines_needed<br>";
    if( sizeof($lines) < $lines_needed ) {
        // Grab stuff from the archives until done.
        //echo "Grabbing<br>";
        foreach (array_reverse(glob(ARCHIVE)) as $filename) {
            //echo "File: $filename<br>";
            $newtext = file_get_contents( $filename );
            $newlines = explode("\n", $newtext);
            $newlines = preg_grep("/\t.*?\t.*?\t/", $newlines);
            if(isset($links_only)) {
                $newlines = preg_grep('/((?:http|https|ftp)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?::[a-zA-Z0-9]*)?\/?(?:[a-zA-Z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*)+/', $newlines);
                //$newlines = preg_grep('/http/', $newlines);
            }
            if(isset($chan_filter)) {
                $newlines = preg_grep("/\t$chan_filter\t/", $newlines);
            }
            if(isset($speaker_filter)) {
                $newlines = preg_grep("/\t$speaker_filter\t/", $newlines);
            }
            $lines = array_merge($newlines, $lines);
            //echo "Lines: " . sizeof($lines) . "<br>";
            //echo "Needed: $lines_needed<br>";
            if( sizeof($lines) >= $lines_needed ) {
                break;
            }
        }
    }
    if( sizeof($lines) < $lines_needed ) {
        $max_page = 1;
    } else {
        $max_page = 0;
    }
    $lines = array_slice($lines, -$lines_needed);
    $lines = array_slice($lines, 0, $page_size);
    return $lines;
}

function get_recent_sql() {
    $query = "SELECT date_part('epoch', msg_date) AS msg_date FROM chanlogs ORDER BY msg_date DESC LIMIT 1";
    $result = pg_query($query) or die('Query failed: ' . pg_last_error());
    $row = pg_fetch_object($result) or die('Fetch failed: ' . pg_last_error());
    pg_free_result($result);
    return $row->msg_date;
}

//  function save_colormap() {
//      global $speakers;
//      global $channels;
//      global $current_speaker;
//      global $current_channel;
//      //global $page_size;
//      //global $page_count;
//  
//      //setcookie("page_size", $page_size);
//      //setcookie("page_count", $page_count);
//      setcookie("current_speaker", $current_speaker);
//      foreach ($speakers as $k => $v) {
//          $bk = urlencode($k);
//          $bv = $speakers[$k];
//          setcookie("speakers[$bk]", $bv);
//          //echo "Saving Speaker: $k - " . $speakers[$k] . "<br>";
//          //echo "Saving Speaker: $bk - $bv<br>";
//      }
//      setcookie("current_channel", $current_speaker);
//      foreach ($channels as $k => $v) {
//          $bk = urlencode($k);
//          $bv = $channels[$k];
//          setcookie("channels[$bk]", $bv);
//          //echo "Saving Channel: $k - " . $channels[$k] . "<br>";
//          //echo "Saving Channel: $bk - $bv<br>";
//      }
//  }

//  function restore_colormap() {
//      global $speakers;
//      global $channels;
//      global $current_speaker;
//      global $current_channel;
//      //global $page_size;
//      //global $page_count;
//  
//      /*
//      if(isset($_COOKIE['page_size'])) {
//          $page_size = $_COOKIE['page_size'];
//      }
//      if(isset($_COOKIE['page_count'])) {
//          $page_count = $_COOKIE['page_count'];
//      }
//       */
//  
//      if(isset($_COOKIE['current_speaker'])) {
//          $current_speaker = $_COOKIE['current_speaker'];
//      }
//      if(isset($_COOKIE['speakers'])) {
//          foreach ($_COOKIE['speakers'] as $bk => $bv) {
//              $k = urldecode($bk);
//              $v = $bv;
//              $speakers[$k] = $v;
//              //echo "Restoring Speaker: $k - " . $speakers[$k] . "<br>";
//          }
//      }
//      if(isset($_COOKIE['current_channel'])) {
//          $current_channel = $_COOKIE['current_channel'];
//      }
//      if(isset($_COOKIE['channels'])) {
//          foreach ($_COOKIE['channels'] as $bk => $bv) {
//              $k = urldecode($bk);
//              $v = $bv;
//              $channels[$k] = $v;
//              //echo "Restoring Channel: $k - " . $channels[$k] . "<br>";
//          }
//      }
//  }

//restore_colormap();

//$dbconn = pg_connect("host=localhost dbname=i3logs user=quixadhal password=tardis69")
//    or die('Could not connect: ' . pg_last_error());
//$most_recent_db = get_most_recent_sql();

get_chatter_colors();

if( isset($_REQUEST) && isset($_REQUEST["ps"]) ) {
    $page_size = $_REQUEST["ps"];
} else {
    $page_size = 20;
}
if( $page_size < 1 ) {
    $page_size = 1;
}

if( isset($_REQUEST) && isset($_REQUEST["pn"]) ) {
    $page_number = $_REQUEST["pn"];
} else {
    $page_number = 0;
}
if( $page_number < 0 ) {
    $page_number = 0;
}

if( isset($_REQUEST) && isset($_REQUEST["cf"]) ) {
    $chan_filter = $_REQUEST["cf"];
}
 
if( isset($_REQUEST) && isset($_REQUEST["sf"]) ) {
    $speaker_filter = $_REQUEST["sf"];
}

$format = 'html';
if( isset($_REQUEST) && isset($_REQUEST["fm"]) ) {
    if( $_REQUEST["fm"] == 'rss' ) {
        $format = 'rss';
    } elseif( $_REQUEST["fm"] == 'text' ) {
        $format = 'text';
    }
}

if( isset($_REQUEST) && isset($_REQUEST["lo"]) ) {
    $links_only = 1;
}
 
$file_size = round(filesize(TEXT_FILE)/1024/1024,2);
$lines = load_logs(TEXT_FILE, $page_size, $page_number, $links_only);
//arsort($lines);

$output = array();

$bg = 0;
$count = 0;
foreach ($lines as $line) {
    if( $line == "" ) {
        continue;
    }
    $parts = split("\t", $line);
    if( sizeof($parts) != 4 ) {
        continue;
    }
    $bgcolor = ($bg % 2) ? "#000000" : "#1F1F1F";

    $timestamp = substr($parts[0], 11, 5);
    $timestamp[2] = ':';
    $datestamp = substr($parts[0], 5, 5);
    $datestamp[2] = '/';

    $timestamp_raw = $timestamp;
    $hour = substr($timestamp, 0, 2);
    $timestamp = $hour_colors[$hour] . $timestamp_raw . '</SPAN>';

    $channel = $parts[1];
    //if( ! array_key_exists( $channel, $channels )) {
    //    $channels[$channel] = $current_channel;
    //    $current_channel++;
    //    $current_channel %= sizeof( $colors );
    //}

    $channel_raw = $parts[1];
    //echo "Channel = $channel<br>";
    //$channel_color = $colors[$channels[$channel]];
    if( array_key_exists( $channel, $channel_colors )) {
        $channel_color = $channel_colors[$channel];
    } else {
        $channel_color = $channel_colors['default'];
    }
    $channel = "$channel_color$channel</SPAN>";
    $channel = "<a href=\"" . $_SERVER['PHP_SELF'] .
        "?cf=" . urlencode($parts[1]) .
        (isset($speaker_filter) ? "&sf=" . urlencode($parts[2]) : "") .
        ((isset($page_size) && $page_size != 25 ) ? "&ps=" . urlencode($page_size) : "") .
        ((isset($page_number) && $page_number != 0 ) ? "&pn=" . urlencode($page_number) : "") .
        ((isset($format) && $format != 'html' ) ? "&fm=" . urlencode($format) : "") .
        "\">" . $channel . "</a>";

    $speaker_raw = $parts[2];
    $speaker = $parts[2];
    $bits = explode("@", $speaker);
    $bitname = strtolower($bits[0]);
//      if( array_key_exists( $bitname, $colormap ) ) {
        $speaker_color = $colormap[$bitname];
//      } else {
//          if( ! array_key_exists( $speaker, $speakers )) {
//              $speakers[$speaker] = $current_speaker;
//              $current_speaker++;
//              $current_speaker %= sizeof( $colors );
//          }
//          $speaker_color = $colors[$speakers[$speaker]];
//      }
    $speaker = $speaker_color . $parts[2] . "</SPAN>";
    $speaker = "<a href=\"" . $_SERVER['PHP_SELF'] .
        "?sf=" . urlencode($parts[2]) .
        (isset($chan_filter) ? "&cf=" . urlencode($parts[1]) : "") .
        ((isset($page_size) && $page_size != 25 ) ? "&ps=" . urlencode($page_size) : "") .
        ((isset($page_number) && $page_number != 0 ) ? "&pn=" . urlencode($page_number) : "") .
        ((isset($format) && $format != 'html' ) ? "&fm=" . urlencode($format) : "") .
        "\">" . $speaker . "</a>";

    $message_raw = $parts[3];
    $message = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '_', $parts[3]);
    $message = htmlentities($message);
    $message = preg_replace( '/((?:http|https|ftp)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?::[a-zA-Z0-9]*)?\/?(?:[a-zA-Z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*)/', '<a href="$1" target="I3-link">$1</a>', $message);

    $rss_title = "$datestamp $timestamp_raw ($channel_raw) $speaker_raw: " . substr($message_raw, 0, 120);
    $rss_desc = "$message_raw";
    $md5 = md5( $channel_raw . $speaker_raw . $message_raw );
    $rss_link = RSS_URL;
    //$rss_guid = RSS_URL . "&md5=" . $md5;
    $rss_guid = $md5;

    $output[$count]['bgcolor'] = $bgcolor;
    $output[$count]['datestamp'] = $datestamp;
    $output[$count]['timestamp'] = $timestamp;
    $output[$count]['channel'] = $channel;
    $output[$count]['speaker'] = $speaker;
    $output[$count]['message'] = $message;
    $output[$count]['rss_title'] = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '_', $rss_title);
    $output[$count]['rss_desc'] = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '_', $rss_desc);
    $output[$count]['rss_link'] = $rss_link;
    $output[$count]['rss_guid'] = $rss_guid;
    $output[$count]['md5'] = $md5;
    $output[$count]['timestamp_raw'] = $timestamp_raw;
    $output[$count]['channel_raw'] = $channel_raw;
    $output[$count]['speaker_raw'] = $speaker_raw;
    $output[$count]['message_raw'] = $message_raw;

    $bg++;
    $count++;
}

$prev_url = $_SERVER['PHP_SELF'] .
        "?pn=" . urlencode($page_number - 1) .
        ((isset($page_size) && $page_size != 25 ) ? "&ps=" . urlencode($page_size) : "") .
        ((isset($format) && $format != 'html' ) ? "&fm=" . urlencode($format) : "") .
        (isset($links_only) ? "&lo" : "") .
        (isset($speaker_filter) ? "&sf=" . urlencode($speaker_filter) : "") .
        (isset($chan_filter) ? "&cf=" . urlencode($chan_filter) : "");
$nolinks_url = $_SERVER['PHP_SELF'] .
        "?pn=" . urlencode($page_number) .
        ((isset($page_size) && $page_size != 25 ) ? "&ps=" . urlencode($page_size) : "") .
        ((isset($format) && $format != 'html' ) ? "&fm=" . urlencode($format) : "") .
        (isset($speaker_filter) ? "&sf=" . urlencode($speaker_filter) : "") .
        (isset($chan_filter) ? "&cf=" . urlencode($chan_filter) : "");
$links_url = $_SERVER['PHP_SELF'] .
        "?pn=" . urlencode($page_number) .
        ((isset($page_size) && $page_size != 25 ) ? "&ps=" . urlencode($page_size) : "") .
        ((isset($format) && $format != 'html' ) ? "&fm=" . urlencode($format) : "") .
        ("&lo") .
        (isset($speaker_filter) ? "&sf=" . urlencode($speaker_filter) : "") .
        (isset($chan_filter) ? "&cf=" . urlencode($chan_filter) : "");
$next_url = $_SERVER['PHP_SELF'] .
        "?pn=" . urlencode($page_number + 1) .
        ((isset($page_size) && $page_size != 25 ) ? "&ps=" . urlencode($page_size) : "") .
        ((isset($format) && $format != 'html' ) ? "&fm=" . urlencode($format) : "") .
        (isset($links_only) ? "&lo" : "") .
        (isset($speaker_filter) ? "&sf=" . urlencode($speaker_filter) : "") .
        (isset($chan_filter) ? "&cf=" . urlencode($chan_filter) : "");

//save_colormap();

if($format == 'rss') {
?>
<? header('Content-type: application/rss+xml'); ?>
<? echo "<?xml version=\"1.0\" ?>\n"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>I3 Feed</title>
        <description>
            This is the Intermud-3 network traffic feed, as seen by <? echo MUD_NAME; ?>.
        </description>
        <link><? echo RSS_URL; ?></link>
        <atom:link href="<? echo RSS_URL; ?>" rel="self" type="application/rss+xml" />
        <?  foreach ($output as $k => $v) { ?>
            <item>
                <title><? echo '<![CDATA[' . $output[$k]['rss_title'] . ']]>'; ?></title>
                <description><? echo '<![CDATA[' . $output[$k]['rss_desc'] . ']]>'; ?></description>
                <link><? echo $output[$k]['rss_link']; ?></link>
                <guid isPermaLink="false"><? echo $output[$k]['rss_guid']; ?></guid>
            </item>
        <? } ?>
    </channel>
</rss>
<? } elseif ($format == 'html') { ?>
<? header('Content-type: text/html'); ?>
<html>
    <head>
        <title> Intermud-3 network traffic, as seen by <? echo MUD_NAME; ?>. </title>
        <meta http-equiv="refresh" content="60">
    </head>
    <!-- <body bgcolor="black" text="#bbbbbb"> -->
    <!-- <body background="gfx/dark_wood.jpg" bgcolor="#505050" text="#d0d0d0" link="#ffffbf" vlink="#ffa040"> -->
    <body bgcolor="black" text="#d0d0d0" link="#ffffbf" vlink="#ffa040">
        <table border=0 cellspacing=0 cellpadding=0 width=80% align="center">
            <tr>
                <td align="right" valign="top">
                    <!-- <a href="/anyterm/anyterm.shtml?rows=40&cols=100"> -->
                    <a href="/~bloodlines">
                        <!-- <img src="gfx/bloodlines.png" border=0 width=469 height=160 alt="(Bloodlines:)"> -->
                        <img src="gfx/bloodlines.png" border=0 width=234 height=80 alt="(Bloodlines:)">
                    </a>
                </td>
                <td align="left" valign="bottom">
                    <!-- <a href="/anyterm/anyterm.shtml?rows=40&cols=100"> -->
                    <a href="/~bloodlines">
                        <!-- <img src="gfx/wileymud4.png" border=0 width=354 height=81 alt="(WileyMUD IV)"> -->
                        <img src="gfx/wileymud4.png" border=0 width=177 height=40 alt="(WileyMUD IV)">
                    </a>
                </td>
            </tr>
        </table>
        <table width="100%">
            <tr>
                <? if( $max_page == 0 ) { ?>
                <td align="left" width="45%"><span style="color: #555555">
                    <a href="<? echo $next_url; ?>">&nbsp;Previous&nbsp;Page&nbsp;(<? echo $page_number + 1; ?>)</a>
                </span></td>
                <? } else { ?>
                <td align="left" width="45%"><span style="color: #555555">&nbsp;</span></td>
                <? } ?>
                <? if( $page_number > 0 || isset($chan_filter) || isset($speaker_filter)) { ?>
                <td align="center" width="10%"><span style="color: #555555">
                    <a href="<? echo $_SERVER['PHP_SELF']; ?>">Home</a>
                    &nbsp;
                    <? if( ! isset($links_only) ) { ?>
                        <a href="<? echo $links_url; ?>">Links</a>
                    <? } else { ?>
                        <a href="<? echo $nolinks_url; ?>">No Links</a>
                    <? } ?>
                </span></td>
                <? } else { ?>
                <td align="center" width="10%"><span style="color: #555555">
                    <? if( ! isset($links_only) ) { ?>
                        <a href="<? echo $links_url; ?>">Links</a>
                    <? } else { ?>
                        <a href="<? echo $nolinks_url; ?>">No Links</a>
                    <? } ?>
                </span></td>
                <? } ?>
                <? if( $page_number > 0 ) { ?>
                <td align="right" width="45%"><span style="color: #555555">
                    <a href="<? echo $prev_url; ?>">(<? echo $page_number - 1; ?>)&nbsp;Next&nbsp;Page&nbsp;</a>
                </span></td>
                <? } else { ?>
                <td align="right" width="45%"><span style="color: #555555">&nbsp;</span></td>
                <? } ?>
            </tr>
        </table>
        <table width="100%">
            <tr>
                <th align="left" width="5%">Date</th>
                <th align="left" width="5%">Time</th>
                <th align="left" width="10%">Channel</th>
                <th align="left" width="20%">Speaker</th>
                <th align="left" width="60%">&nbsp;</th>
            </tr>
            <?
            foreach ($output as $k => $v) {
            ?>
                <tr>
                    <td bgcolor="<? echo $output[$k]['bgcolor']; ?>"><? echo $output[$k]['datestamp']; ?></td>
                    <td bgcolor="<? echo $output[$k]['bgcolor']; ?>"><? echo $output[$k]['timestamp']; ?></td>
                    <td bgcolor="<? echo $output[$k]['bgcolor']; ?>"><? echo $output[$k]['channel']; ?></td>
                    <td bgcolor="<? echo $output[$k]['bgcolor']; ?>"><? echo $output[$k]['speaker']; ?></td>
                    <td bgcolor="<? echo $output[$k]['bgcolor']; ?>"><? echo $output[$k]['message']; ?></td>
                </tr>
            <? } ?>
        </table>
        <?
        $time_end = microtime(true);
        $time_spent = $time_end - $time_start;
        //print_r($speakers);
        ?>
        <table width="100%">
            <tr>
                <td align="left" width="45%"><span style="color: #333333">Last refreshed at <? echo $now; ?>.&nbsp;</span></td>
                <td align="center" width="10%"><span style="color: #333333"><a href="i3log.php?fm=rss"><img src="gfx/valid-rss-rogers.png" border=0 width=88 height=31 alt="(RSS)" /></a></span></td>
                <td align="right" width="45%"><span style="color: #333333">&nbsp;Page generated in <? printf( "%7.3f", $time_spent); ?> seconds.</span></td>
            </tr>
        </table>
    </body>
</html>
<? } else { ?>
<? header('Content-type: text/plain'); ?>
<?
    echo str_pad("--=)) This is the Intermud-3 network traffic feed, as seen by " . MUD_NAME . ". ((=--", 120, " ", STR_PAD_BOTH) . "\n\n";
    echo "Date  Time  ";
    echo str_pad("Channel", 16) . " ";
    echo str_pad("Speaker", 24) . " ";
    echo "Message\n";
    echo "----- ----- ";
    echo str_repeat("-", 16) . " ";
    echo str_repeat("-", 24) . " ";
    echo str_repeat("-", 65) . "\n";
    foreach ($output as $k => $v) {
        echo $output[$k]['datestamp'] . " " . $output[$k]['timestamp_raw'] . " ";
        echo substr(str_pad("(" . $output[$k]['channel_raw'] . ")", 16), 0, 16) . " ";
        echo substr(str_pad($output[$k]['speaker_raw'], 24), 0, 24) . " ";
        echo wordwrap($output[$k]['message_raw'], 65, "\n" . str_repeat(" ", 54)) . "\n";
    }
    $time_end = microtime(true);
    $time_spent = $time_end - $time_start;
    echo "\n" . str_pad("Last refreshed at $now", 79) . " " . str_pad( sprintf( "Page generated in %7.3f seconds.", $time_spent), 40, " ", STR_PAD_LEFT);
?>
<? } ?>
