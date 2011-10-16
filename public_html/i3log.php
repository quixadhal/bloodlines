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

function load_logs() {
    global $chan_filter;
    global $speaker_filter;
    global $search_filter;
    global $page_size;
    global $page_number;
    global $max_page;
    global $links_only;

    if(! isset($skip_lines)) {
        $skip_lines = 0;
    }
    $limit = $page_size;
    $offset = $page_number * $page_size;

    $query = "SELECT to_char(msg_date, 'MM/DD') AS the_date, to_char(msg_date, 'HH:MI') AS the_time, to_char(msg_date, 'HH') AS the_hour, channel, speaker, mud, message FROM chanlogs";

    $dbconn = pg_connect("host=localhost dbname=i3logs user=quixadhal password=tardis69")
        or die('Could not connect: ' . pg_last_error());

    $links_term = "";
    if(isset($links_only)) {
        $links_term = " AND is_url";
        //$lines = preg_grep('/((?:http|https|ftp)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?::[a-zA-Z0-9]*)?\/?(?:[a-zA-Z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*)+/', $lines);
        // Need to mark as links in the log insertion thing...
    }

    if(isset($search_filter)) {
        if(isset($chan_filter)) {
            if(isset($speaker_filter)) {
                $query = $query .  " WHERE message ILIKE $1 AND channel ILIKE $2 AND speaker ILIKE $3 $links_term ORDER BY msg_date DESC OFFSET $4 LIMIT $5";
                $result = pg_query_params($query, array($search_filter, $chan_filter, $speaker_filter, $offset, $limit)) or die('Query failed: ' . pg_last_error());
            } else {
                $query = $query .  " WHERE message ILIKE $1 AND channel ILIKE $2 $links_term ORDER BY msg_date DESC OFFSET $3 LIMIT $4";
                $result = pg_query_params($query, array($search_filter, $chan_filter, $offset, $limit)) or die('Query failed: ' . pg_last_error());
            }
        } else {
            if(isset($speaker_filter)) {
                $query = $query .  " WHERE message ILIKE $1 AND speaker ILIKE $2 $links_term ORDER BY msg_date DESC OFFSET $3 LIMIT $4";
                $result = pg_query_params($query, array($search_filter, $speaker_filter, $offset, $limit)) or die('Query failed: ' . pg_last_error());
            } else {
                $query = $query .  " WHERE message ILIKE $1 $links_term ORDER BY msg_date DESC OFFSET $2 LIMIT $3";
                $result = pg_query_params($query, array($search_filter, $offset, $limit)) or die('Query failed: ' . pg_last_error());
            }
        }
    } else {
        if(isset($chan_filter)) {
            if(isset($speaker_filter)) {
                $query = $query .  " WHERE channel ILIKE $1 AND speaker ILIKE $2 $links_term ORDER BY msg_date DESC OFFSET $3 LIMIT $4";
                $result = pg_query_params($query, array($chan_filter, $speaker_filter, $offset, $limit)) or die('Query failed: ' . pg_last_error());
            } else {
                $query = $query .  " WHERE channel ILIKE $1 $links_term ORDER BY msg_date DESC OFFSET $2 LIMIT $3";
                $result = pg_query_params($query, array($chan_filter, $offset, $limit)) or die('Query failed: ' . pg_last_error());
            }
        } else {
            if(isset($speaker_filter)) {
                $query = $query .  " WHERE speaker ILIKE $1 $links_term ORDER BY msg_date DESC OFFSET $2 LIMIT $3";
                $result = pg_query_params($query, array($speaker_filter, $offset, $limit)) or die('Query failed: ' . pg_last_error());
            } else {
                if(isset($links_only)) {
                    $query = $query .  " WHERE is_url ORDER BY msg_date DESC OFFSET $1 LIMIT $2";
                } else {
                    $query = $query .  " ORDER BY msg_date DESC OFFSET $1 LIMIT $2";
                }
                $result = pg_query_params($query, array($offset, $limit)) or die('Query failed: ' . pg_last_error());
            }
        }
    }

    $rows = array();
    while( $row = pg_fetch_object($result) ) {
        array_push( $rows, $row ); 
    }
    pg_free_result($result);
    return $rows;
}

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

if( isset($_REQUEST) && isset($_REQUEST["sr"]) && $_REQUEST["sr"] != "" && preg_match('/[^\*]/', $_REQUEST["sr"] ) > 0 ) {
    $search_filter = $_REQUEST["sr"];
    $search_filter = preg_replace('/,/', ' ', $search_filter);
    $search_filter = preg_replace('/ +/', ' ', $search_filter);
    $search_filter = preg_replace('/[^0-9A-Za-z \*]/', '', $search_filter);
    $search_filter = preg_replace('/\*/', '%', $search_filter);
    $search_filter = trim($search_filter);
    if(substr($search_filter, 0, 1) != '%')
        $search_filter = "%" . $search_filter;
    if(substr($search_filter, -1, 1) != '%')
        $search_filter = $search_filter . "%";
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
 
$rows = array_reverse(load_logs());

$output = array();
$bg = 0;
$count = 0;

foreach ($rows as $row) {
    $bgcolor = ($bg % 2) ? "#000000" : "#1F1F1F";
    $bold_bgcolor = ($bg % 2) ? "#202040" : "#3F3F6F";

    $datestamp_raw = $row->the_date;
    $timestamp_raw = $row->the_time;
    $hour = $row->the_hour;
    $channel_raw = $row->channel;
    $speaker_raw = $row->speaker . "@" . $row->mud;
    $message_raw = $row->message;

    $datestamp = $datestamp_raw;
    $timestamp = $hour_colors[$hour] . $timestamp_raw . '</SPAN>';

    if( array_key_exists( $channel_raw, $channel_colors )) {
        $channel_color = $channel_colors[$channel_raw];
    } else {
        $channel_color = $channel_colors['default'];
    }
    $channel = "$channel_color$channel_raw</SPAN>";
    $channel_url = $_SERVER['PHP_SELF'] .
        "?cf=" . urlencode($channel_raw) .
        (isset($speaker_filter) ? "&sf=" . urlencode($row->speaker) : "") .
        (isset($search_filter) ? "&sr=" . urlencode(preg_replace('/%/', '*', $search_filter)) : "") .
        ((isset($page_size) && $page_size != 25 ) ? "&ps=" . urlencode($page_size) : "") .
        ((isset($page_number) && $page_number != 0 ) ? "&pn=" . urlencode($page_number) : "") .
        ((isset($format) && $format != 'html' ) ? "&fm=" . urlencode($format) : "");
    $channel = "<a href=\"" . $channel_url . "\">" . $channel . "</a>";

    $speaker_name = strtolower($row->speaker);
    $speaker_color = $colormap[$speaker_name];
    $speaker = $speaker_color . $speaker_raw . "</SPAN>";
    $speaker_url = $_SERVER['PHP_SELF'] .
        "?sf=" . urlencode($row->speaker) .
        (isset($chan_filter) ? "&cf=" . urlencode($channel_raw) : "") .
        (isset($search_filter) ? "&sr=" . urlencode(preg_replace('/%/', '*', $search_filter)) : "") .
        ((isset($page_size) && $page_size != 25 ) ? "&ps=" . urlencode($page_size) : "") .
        ((isset($page_number) && $page_number != 0 ) ? "&pn=" . urlencode($page_number) : "") .
        ((isset($format) && $format != 'html' ) ? "&fm=" . urlencode($format) : "");
    $speaker = "<a href=\"" . $speaker_url . "\">" . $speaker . "</a>";

    $message = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '_', $message_raw);
    $message = htmlentities($message);
    $message = preg_replace( '/((?:http|https|ftp)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?::[a-zA-Z0-9]*)?\/?(?:[a-zA-Z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*)/', '<a href="$1" target="I3-link">$1</a>', $message);

    $rss_title = "$datestamp_raw $timestamp_raw ($channel_raw) $speaker_raw: " . substr($message_raw, 0, 120);
    $rss_desc = "$message_raw";
    $md5 = md5( $channel_raw . $speaker_raw . $message_raw );
    $rss_link = RSS_URL;
    //$rss_guid = RSS_URL . "&md5=" . $md5;
    $rss_guid = $md5;

    $output[$count]['bgcolor'] = $bgcolor;
    $output[$count]['bold_bgcolor'] = $bold_bgcolor;
    $output[$count]['datestamp'] = $datestamp;
    $output[$count]['timestamp'] = $timestamp;
    $output[$count]['channel'] = $channel;
    $output[$count]['channel_url'] = $channel_url;
    $output[$count]['speaker'] = $speaker;
    $output[$count]['speaker_url'] = $speaker_url;
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
        (isset($chan_filter) ? "&cf=" . urlencode($chan_filter) : "") .
        (isset($search_filter) ? "&sr=" . urlencode(preg_replace('/%/', '*', $search_filter)) : "");
$nolinks_url = $_SERVER['PHP_SELF'] .
        "?pn=" . urlencode($page_number) .
        ((isset($page_size) && $page_size != 25 ) ? "&ps=" . urlencode($page_size) : "") .
        ((isset($format) && $format != 'html' ) ? "&fm=" . urlencode($format) : "") .
        (isset($speaker_filter) ? "&sf=" . urlencode($speaker_filter) : "") .
        (isset($chan_filter) ? "&cf=" . urlencode($chan_filter) : "") .
        (isset($search_filter) ? "&sr=" . urlencode(preg_replace('/%/', '*', $search_filter)) : "");
$links_url = $_SERVER['PHP_SELF'] .
        "?pn=" . urlencode($page_number) .
        ((isset($page_size) && $page_size != 25 ) ? "&ps=" . urlencode($page_size) : "") .
        ((isset($format) && $format != 'html' ) ? "&fm=" . urlencode($format) : "") .
        ("&lo") .
        (isset($speaker_filter) ? "&sf=" . urlencode($speaker_filter) : "") .
        (isset($chan_filter) ? "&cf=" . urlencode($chan_filter) : "") .
        (isset($search_filter) ? "&sr=" . urlencode(preg_replace('/%/', '*', $search_filter)) : "");
$next_url = $_SERVER['PHP_SELF'] .
        "?pn=" . urlencode($page_number + 1) .
        ((isset($page_size) && $page_size != 25 ) ? "&ps=" . urlencode($page_size) : "") .
        ((isset($format) && $format != 'html' ) ? "&fm=" . urlencode($format) : "") .
        (isset($links_only) ? "&lo" : "") .
        (isset($speaker_filter) ? "&sf=" . urlencode($speaker_filter) : "") .
        (isset($chan_filter) ? "&cf=" . urlencode($chan_filter) : "") .
        (isset($search_filter) ? "&sr=" . urlencode(preg_replace('/%/', '*', $search_filter)) : "");

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
        <form action="" method="get">
            <table border=0 cellspacing=0 cellpadding=0 width=90% align="center">
                <tr>
                    <td align="right" valign="top">
                        <span style="color: #1F1F1F;">
                            <label id="srlabel" for="sr"
                                onmouseover="srinput.style.color='#FFFF00'; srinput.style.backgroundColor='#1F1F1F'; srlabel.style.color='#FFFFFF';"
                                onmouseout="srinput.style.color='#4F4F00'; srinput.style.backgroundColor='#000000'; srlabel.style.color='#1F1F1F';"
                                onfocus="srinput.focus();"
                                onclick="srinput.focus();"
                            > Search:&nbsp; </label>
                        </span>
                    </td>
                    <td bgcolor="#1F1F1F" width="200" align="left" valign="top">
                            <input id="srinput" type="text" style="background-color: #000000; color: #3F3F00; border: 1px; border-color: #000000; border-style: solid; width: 200px;"
                                onmouseover="this.style.color='#FFFF00'; this.style.backgroundColor='#1F1F1F'; srlabel.style.color='#FFFFFF';"
                                onfocus="this.style.color='#FFFF00'; this.style.backgroundColor='#1F1F1F'; srlabel.style.color='#FFFFFF'; if(!this._haschanged){this.value=''};this._haschanged=true;"
                                onblur="this.style.color='#4F4F00'; this.style.backgroundColor='#000000'; srlabel.style.color='#1F1F1F';"
                                onmouseout="this.style.color='#4F4F00'; this.style.backgroundColor='#000000'; srlabel.style.color='#1F1F1F';"
                                maxlength="30" name="sr" value="<?php if(isset($search_filter)) echo preg_replace('/%/', '*', $search_filter); ?>" />
                            <?php if(isset($page_number)) { ?>
                                <input type="hidden" name="pn" value="<?php echo $page_number; ?>">
                            <?php } ?>
                            <?php if(isset($page_size)) { ?>
                                <input type="hidden" name="ps" value="<?php echo $page_size; ?>">
                            <?php } ?>
                            <?php if(isset($format)) { ?>
                                <input type="hidden" name="fm" value="<?php echo $format; ?>">
                            <?php } ?>
                            <?php if(isset($links_only)) { ?>
                                <input type="hidden" name="lo" value="<?php echo $links_only; ?>">
                            <?php } ?>
                            <?php if(isset($speaker_filter)) { ?>
                                <input type="hidden" name="sf" value="<?php echo $speaker_filter; ?>">
                            <?php } ?>
                            <?php if(isset($chan_filter)) { ?>
                                <input type="hidden" name="cf" value="<?php echo $chan_filter; ?>">
                            <?php } ?>
                    </td>
                </tr>
            </table>
        </form>
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
                        <a href="<? echo $links_url; ?>">Links&nbsp;Only</a>
                    <? } else { ?>
                        <a href="<? echo $nolinks_url; ?>">No&nbsp;Links</a>
                    <? } ?>
                </span></td>
                <? } else { ?>
                <td align="center" width="10%"><span style="color: #555555">
                    <? if( ! isset($links_only) ) { ?>
                        <a href="<? echo $links_url; ?>">Links&nbsp;Only</a>
                    <? } else { ?>
                        <a href="<? echo $nolinks_url; ?>">No&nbsp;Links</a>
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
                    <td onmouseover="this.style.backgroundColor = '<?php echo $output[$k]['bold_bgcolor']; ?>';"
                        onmouseout="this.style.backgroundColor = '<?php echo $output[$k]['bgcolor']; ?>';"
                        onclick="document.location.href='<?php echo $output[$k]['channel_url']; ?>';" bgcolor="<? echo $output[$k]['bgcolor']; ?>"><? echo $output[$k]['channel']; ?></td>
                    <td onmouseover="this.style.backgroundColor = '<?php echo $output[$k]['bold_bgcolor']; ?>';"
                        onmouseout="this.style.backgroundColor = '<?php echo $output[$k]['bgcolor']; ?>';"
                        onclick="document.location.href='<?php echo $output[$k]['speaker_url']; ?>';" bgcolor="<? echo $output[$k]['bgcolor']; ?>"><? echo $output[$k]['speaker']; ?></td>
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
