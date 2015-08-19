<?
$time_start = microtime(true);
$now = date('g:ia \o\n l, \t\h\e jS \o\f F, Y');
$mini_now = date('Y-m-d H:i:s');

require_once "i3config.php";

function numbered_source($filename)
{
    $lines = implode(range(1, count(file($filename))), '<br />');
    $content = highlight_file($filename, true);
    $style = '
    <style type="text/css"> 
        .num { 
        float: left; 
        color: gray; 
        font-size: 13px;    
        font-family: monospace; 
        text-align: right; 
        margin-right: 6pt; 
        padding-right: 6pt; 
        border-right: 1px solid gray;} 

        body {margin: 0px; margin-left: 5px;} 
        td {vertical-align: top;} 
        code {white-space: nowrap;} 
    </style>
    '; 
    return "$style\n<table><tr><td class=\"num\">\n$lines\n</td><td>\n$content\n</td></tr></table>"; 
}

function get_pinkfish_map() {
    $colors = array(
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

        // Cheating...
        '%^FLASH%^%^LIGHTGREEN%^'   => '<SPAN style="color: #55ff55">',

        // In this case, our web page background is black, so pretend there is none.
        // That's because on a terminal, it just resets the character cells and looks fine, but
        // in HTML, it makes the background stick out if the table cell isn't also black.
        //'%^B_BLACK%^%^GREY%^'       => '<SPAN style="background-color: #000000; color: #bbbbbb">',
        //'%^B_BLACK%^%^WHITE%^'      => '<SPAN style="background-color: #000000; color: #ffffff">',
        '%^B_BLACK%^%^GREY%^'       => '<SPAN style="color: #bbbbbb">',
        '%^B_BLACK%^%^WHITE%^'      => '<SPAN style="color: #ffffff">',

        '%^B_RED%^%^GREEN%^'        => '<SPAN style="background-color: #bb0000; color: #00bb00">',
        '%^B_RED%^%^LIGHTGREEN%^'   => '<SPAN style="background-color: #bb0000; color: #00ff00">'
    );
    return $colors;
}

function get_hour_colors(array $pinkfish) {
    $colors = array(
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

    foreach ($colors as $k => $v) {
        if( array_key_exists( $v, $pinkfish )) {
            $colors[$k] = $pinkfish[$v];
        }
    }
    return $colors;
}

function get_channel_colors(array $pinkfish) {
    $colors = array(
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
        "pyom"        => "%^FLASH%^%^LIGHTGREEN%^",
        "free_speech" => "%^PINK%^",
        "url"         => "%^WHITE%^",

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

    foreach ($colors as $k => $v) {
        if( array_key_exists( $v, $pinkfish )) {
            $colors[$k] = $pinkfish[$v];
        }
    }
    return $colors;
}

function get_chatter_colors($pinkfish, $chatFileName) {
    $colormap = array ();

    $text = file_get_contents( $chatFileName );
    $lines = explode("\n", $text);
    $line = $lines[1]; // Stores all on one long line...
    $line = substr($line, 11, -3);
    $mapping = explode(",", $line);
    $colormap = array();
    //foreach ($pinkfish as $k => $v) {
    //    echo "pinkfish[$k] = ".htmlentities($v)."<br>";
    //}
    for($i = 0; $i < sizeof($mapping); $i++ ) {
        $map = explode(":", $mapping[$i]);
        if(sizeof($map) < 2) {
            //echo "WARNING: Invalid entry for color map \"";
            //print_r($map);
            //echo "\".<br>";
            continue;
        }
        $mapname = substr($map[0], 1, -1); // Strip quotes
        $mapcolor = substr($map[1], 1, -1); // Strip quotes
        $colormap[$mapname] = $pinkfish[$mapcolor];
        //echo "colormap[$mapname] = ".htmlentities($colormap[$mapname])."<br>";
    }
    return $colormap;
}

function list_name_ok($str) {
    if(preg_match('/[^0-9A-Za-z _\:-]/', $str)) {
        return 0;
    }
    return 1;
}

// For whatever reason, loading these seems to be strangly slow, so let's cache them to a file.
// Since we only want things that chat a bit, odds are it will take at least a few hours for a
// new mud to show up... so using cached results and only redoing it every hour seems fine.
function get_cache($fileName, $sql, $column) {
    $list = array ();

    if(file_exists($fileName)) {
        $fileModTime = filemtime($fileName);
        if($fileModTime >= time() - 3600) {
            // File data is fresh enough, use it.
            $fileData = file_get_contents($fileName);
            $list = explode("\n", $fileData);
        }
    }

    if(count($list) < 1) {
        // Connect to PoOstgreSQL database
        try {
            global $db_dsn, $db_user, $db_pwd;
            $dbh = new PDO( $db_dsn, $db_user, $db_pwd, array( PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }

        // Fetch list of MUDs that have had more than 100 lines of traffic, ever.
        try {
            $sth = $dbh->query($sql);
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }

        $sth->setFetchMode(PDO::FETCH_ASSOC);
        while($row = $sth->fetch()) {
            $list[] = $row[$column];
        }

        $list = array_filter($list, "list_name_ok");
        file_put_contents($fileName, implode("\n", $list));
    }

    return $list;
}

function is_local_ip() {
    $visitor_ip = $_SERVER['REMOTE_ADDR'];
    $varr = explode(".", $visitor_ip);
    if($varr[0] == "192" && $varr[1] == "168")
        return 1;
    return 0;
}

$isLocal = is_local_ip();

function get_video_list($dbh) {
    $list = array();

    $vSql = "SELECT video_id, video_len, description, plays from videos order by plays asc, description";
    try {
        $sth = $dbh->query($vSql);
    }
    catch(PDOException $e) {
        echo $e->getMessage();
    }

    $sth->setFetchMode(PDO::FETCH_OBJ);
    while($row = $sth->fetch()) {
        $list[$row->video_id] = $row;
    }

    return $list;
}

function get_play_list($dbh) {
    $list = array();

    $vSql = "SELECT v.video_id, v.video_len, v.description, v.plays from videos v inner join ( select min(plays) as plays from videos ) q on v.plays = q.plays";
    try {
        $sth = $dbh->query($vSql);
    }
    catch(PDOException $e) {
        echo $e->getMessage();
    }

    $sth->setFetchMode(PDO::FETCH_OBJ);
    while($row = $sth->fetch()) {
        $list[$row->video_id] = $row;
    }

    return $list;
}

function get_quote($dbh) {
    global $colormap;

    $list = array();

    $qSql = "SELECT speaker, mud, message
         FROM chanlogs
        WHERE msg_date >= now() - '3 days'::interval
          AND is_url IS NOT TRUE
          AND is_emote IS NOT TRUE
          AND is_bot IS NOT TRUE
          AND channel IN ('intergossip', 'dchat', 'intercre', 'discworld-chat')
       OFFSET random() * (
            SELECT COUNT(*)
             FROM chanlogs
            WHERE msg_date >= now() - '3 days'::interval
              AND is_url IS NOT TRUE
              AND is_emote IS NOT TRUE
              AND is_bot IS NOT TRUE
              AND channel IN ('intergossip', 'dchat', 'intercre', 'discworld-chat')
          )
       LIMIT  1";

    try {
        $sth = $dbh->query($qSql);
    }
    catch(PDOException $e) {
        echo $e->getMessage();
    }

    $sth->setFetchMode(PDO::FETCH_OBJ);
    while($row = $sth->fetch()) {
        $speakerColor = $colormap[strtolower($row->speaker)];
        $row->who = "$speakerColor" . $row->speaker . "@" . $row->mud . "</SPAN>";

        $tmp_msg = preg_replace("/\x1b\[[0-9]+(;[0-9]+)*m/", "", $row->message);
        $message = htmlentities($tmp_msg,0,'UTF-8');
        $row->text = preg_replace('/ /', '&#x2004;', $message); // replace spaces with unicode THREE-PER-EM SPACE

        $list[] = $row;
    }

    return $list[0];
}

$graphics = array();

$graphics['background']         = $isLocal ? "gfx/dark_wood.jpg"            : "https://lh5.googleusercontent.com/-zvnNrcuqbco/UdooZZelxoI/AAAAAAAAALA/9u5S92UySEA/s800/dark_wood.jpg";
$graphics['bloodlines']         = $isLocal ? "gfx/bloodlines.png"           : "https://lh4.googleusercontent.com/-fWWe4X6fzVE/UdooZQ98rGI/AAAAAAAAAK4/vjYmeQdoaXc/s800/bloodlines.png";
$graphics['wileymud4']          = $isLocal ? "gfx/wileymud4.png"            : "https://lh6.googleusercontent.com/-DdOSH9sMalA/UdoolEmvWMI/AAAAAAAAAP8/_wWNhacagcg/s800/wileymud4.png";
$graphics['navbegin']           = $isLocal ? "gfx/navbegin.png"             : "https://lh5.googleusercontent.com/-1h6kwuaFuP8/UdooehtATBI/AAAAAAAAANM/Qptp3P8AQvM/s800/navbegin.png";
$graphics['navback']            = $isLocal ? "gfx/navback.png"              : "https://lh4.googleusercontent.com/-LCtG9tmiZok/UdooeFflpWI/AAAAAAAAANI/6qNEPhnIwYo/s800/navback.png";
$graphics['navprev']            = $isLocal ? "gfx/navprev.png"              : "https://lh3.googleusercontent.com/-HCRGUDMFsZ0/UdooghsCZII/AAAAAAAAAOQ/3Hr8wwn5gZg/s800/navprev.png";
$graphics['navconfig']          = $isLocal ? "gfx/navconfig.png"            : "https://lh5.googleusercontent.com/-mieme8LUBjY/UdooesN4lxI/AAAAAAAAANg/37pQuLUTVf4/s800/navconfig.png";
$graphics['navlinks']           = $isLocal ? "gfx/navlinks.png"             : "https://lh6.googleusercontent.com/-tNTnYR-bXkw/Udoofz3I5cI/AAAAAAAAAN0/JkLKX1kqxIk/s800/navlinks.png";
$graphics['navhome']            = $isLocal ? "gfx/navhome.png"              : "https://lh6.googleusercontent.com/-cv1gkbDAJuY/Udoofg9ZBLI/AAAAAAAAANw/qVVwwP-jLpo/s800/navhome.png";
$graphics['pie_chart']          = $isLocal ? "gfx/pie_chart.png"            : "https://lh3.googleusercontent.com/-Lp66FAfPJck/UdoohD5QKHI/AAAAAAAAAOc/jeKYr9LATL0/s800/pie_chart.png";
$graphics['bar_chart']          = $isLocal ? "gfx/bar_chart.png"            : "https://lh3.googleusercontent.com/-WFOjntvVWso/UdooZX0LCxI/AAAAAAAAALE/9NLRnXWh6vg/s800/bar_chart.png";
$graphics['navnext']            = $isLocal ? "gfx/navnext.png"              : "https://lh6.googleusercontent.com/-cet8lgFmDMc/Udoof9-QcxI/AAAAAAAAAOA/Mrt4Z220G3w/s800/navnext.png";
$graphics['navforward']         = $isLocal ? "gfx/navforward.png"           : "https://lh4.googleusercontent.com/-oVjgOR3_l-M/UdoofCg9rpI/AAAAAAAAANc/OyUY0sk5XGE/s800/navforward.png";
$graphics['navend']             = $isLocal ? "gfx/navend.png"               : "https://lh6.googleusercontent.com/-spjoGlWIF_8/UdoofESvmOI/AAAAAAAAANk/FVSQ2278e7Q/s800/navend.png";
$graphics['rss']                = $isLocal ? "gfx/rss.png"                  : "https://lh5.googleusercontent.com/-YgG7UYtdhXw/Udooh0FTa5I/AAAAAAAAAOs/yfQ58RgBuVM/s800/rss.png";
$graphics['rssMouseOver']       = $isLocal ? "gfx/rssMouseOver.png"         : "https://lh4.googleusercontent.com/-eFFD6FqOdfk/UdooiU3QgKI/AAAAAAAAAO8/VeuqdxMr__A/s800/rssMouseOver.png";
$graphics['json']               = $isLocal ? "gfx/json.png"                 : "https://lh5.googleusercontent.com/-HDs8dCfnHHA/Udoob2ibVtI/AAAAAAAAAMY/1G98kLfpaSg/s800/json.png";
$graphics['jsonMouseOver']      = $isLocal ? "gfx/jsonMouseOver.png"        : "https://lh6.googleusercontent.com/-_0ZCiX6Ogow/UdoobwZEI9I/AAAAAAAAAMM/tNHqAWjhJm8/s800/jsonMouseOver.png";
$graphics['text']               = $isLocal ? "gfx/text.png"                 : "https://lh4.googleusercontent.com/-4ha3X9MshKA/UdoojSqEwUI/AAAAAAAAAPg/IJ7jMHEcLqE/s800/text.png";
$graphics['textMouseOver']      = $isLocal ? "gfx/textMouseOver.png"        : "https://lh6.googleusercontent.com/-E1nAZZA-iLg/UdoojWYO0-I/AAAAAAAAAPw/jyqADyzswe4/s800/textMouseOver.png";
$graphics['server_icon']        = $isLocal ? "gfx/server_icon.png"          : "https://lh4.googleusercontent.com/-LZ9ek46iToA/UdoojFEhuOI/AAAAAAAAAPQ/y_rRyL_1tR8/s800/server_icon.png";
$graphics['help_icon']          = $isLocal ? "gfx/help.png"                 : "https://lh6.googleusercontent.com/-t_GKXvLrh7g/UdooayFUZKI/AAAAAAAAALg/TdVjBKVeluQ/s800/help.png";
$graphics['sql_icon']           = $isLocal ? "gfx/sql.png"                  : "https://lh6.googleusercontent.com/-Ms6hgsVLGac/UkQPgYis_YI/AAAAAAAAAjE/6nn2j-DIg6I/s144/sql.png";

$pinkfish_map = get_pinkfish_map();
$hourColors = get_hour_colors($pinkfish_map);
$channelColors = get_channel_colors($pinkfish_map);
$colormap = get_chatter_colors($pinkfish_map, $CHAT_COLOR_FILE);

$mudList = get_cache( $MUD_CACHE_FILE, "SELECT DISTINCT mud, COUNT(*) FROM chanlogs GROUP BY mud HAVING COUNT(*) > 100 ORDER BY mud ASC, COUNT(*) ASC", "mud" );
$speakerList = get_cache( $SPEAKER_CACHE_FILE, "SELECT DISTINCT speaker, COUNT(*) FROM chanlogs GROUP BY speaker HAVING COUNT(*) > 100 ORDER BY speaker ASC, COUNT(*) ASC", "speaker" );

$defaultPageSize = 20;
$defaultPageNumber = 0;
$defaultFormat = "html";

$pageSize = $defaultPageSize;
$pageNumber = $defaultPageNumber;
$startDate = null;
$linksOnly = null;
$showBots = null;
$format = $defaultFormat;
$channelFilter = null;
$speakerFilter = null;
$mudFilter = null;
$searchFilter = null;

$anchorID = null;
$oldPageNumber = null;
$showSQL = null;
$youtube_visible = null;
$yestube = null;

// Connect to PoOstgreSQL database
try {
    $dbh = new PDO( $db_dsn, $db_user, $db_pwd, array( PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));
}
catch(PDOException $e) {
    echo $e->getMessage();
}

if( isset($_REQUEST) && isset($_REQUEST["yestube"]) ) {
    $yestube = 1;
}

if( isset($_REQUEST) && isset($_REQUEST["showtube"]) ) {
    $youtube_visible = 1;
}

if( isset($_REQUEST) && isset($_REQUEST["showsql"]) ) {
    $showSQL = 1;
}

// Page size (IE: LIMIT clause)

if( isset($_REQUEST) && isset($_REQUEST["ps"]) ) {
    $pageSize = $_REQUEST["ps"];
    if(!is_numeric($pageSize)) {
        $pageSize = $defaultPageSize;
    }
    if( $pageSize < 1 ) {
        $pageSize = 1;
    }
}

// Page number (IE: OFFSET clause)
// Once we know the result set size, we need to limit this.
// 0 means the END of the data set, 1..N means totalpages - x
// This is so that page 0 will always be "current"

if( isset($_REQUEST) && isset($_REQUEST["pn"]) ) {
    $pageNumber = $_REQUEST["pn"];
    if(!is_numeric($pageNumber)) {
        $pageNumber = 0;
    }
    if($pageNumber < 0) {
        $pageNumber = 0;
    }
}

if( isset($_REQUEST) && isset($_REQUEST["pd"]) ) {
    $pageNumberEntered = $_REQUEST["pd"];
    if(!is_numeric($pageNumberEntered)) {
        $pageNumberEntered = 0;
    }
}

// This specifies a start date for the query, it's mostly
// used to allow one to click on a date and see stuff
// from there forwards.  Unix timestamp.

$startDateSql = '';
if( isset($_REQUEST) && isset($_REQUEST["sd"]) ) {
    $startDate = $_REQUEST["sd"];
    if(!is_numeric($startDate)) {
        $startDate = null;
    }
    if(isset($startDate)) {
        //$startDateSql = "AND date_part('epoch', msg_date)::integer >= $startDate";
        $startDateSql = "AND msg_date >= to_timestamp($startDate)";
    }
}

// Anti-Channel Filter (do NOT display these channels)
// Accept a comma-seperated list of names

$antichanSql = '';
if( isset($_REQUEST) && isset($_REQUEST["af"]) ) {
    $antichannelFilter = $_REQUEST["af"];
    $words = array();
    foreach (explode(",", $antichannelFilter) as $word) {
        $words[] = $dbh->quote(strtolower($word));
    }
    $antichanSql = "AND lower(channel) NOT IN ( " . implode(",", $words) . " )";
}
 
// Channel Filter (only display these channels)
// Accept a comma-seperated list of names

$chanSql = '';
if( isset($_REQUEST) && isset($_REQUEST["cf"]) ) {
    $channelFilter = $_REQUEST["cf"];
    $words = array();
    foreach (explode(",", $channelFilter) as $word) {
        $words[] = $dbh->quote(strtolower($word));
    }
    $chanSql = "AND lower(channel) IN ( " . implode(",", $words) . " )";
}
 
// Speaker filter (only display these speakers)
// Accept a comma-seperated list of names

$speakerSql = '';
if( isset($_REQUEST) && isset($_REQUEST["sf"]) ) {
    $speakerFilter = $_REQUEST["sf"];
    $words = array();
    foreach (explode(",", $speakerFilter) as $word) {
        $words[] = $dbh->quote(strtolower($word));
    }
    $speakerSql = "AND lower(speaker) IN ( " . implode(",", $words) . " )";
}

// MUD filter (only display lines from these MUDs)
// Accept a comma-seperated list of names

$mudSql = '';
if( isset($_REQUEST) && isset($_REQUEST["mf"]) ) {
    $mudFilter = $_REQUEST["mf"];
    $words = array();
    foreach (explode(",", $mudFilter) as $word) {
        $words[] = $dbh->quote(strtolower($word));
    }
    $mudSql = "AND lower(mud) IN ( " . implode(",", $words) . " )";
}

// Search filter (match against Message column)

$searchSql = '';
if( isset($_REQUEST) && isset($_REQUEST["sr"]) && $_REQUEST["sr"] != "" && preg_match('/[^\*]/', $_REQUEST["sr"] ) > 0 ) {
    $searchFilter = $_REQUEST["sr"];
    //$searchFilter = preg_replace('/+/', ',', $searchFilter);
    $searchFilter = preg_replace('/[^0-9A-Za-z ,\*]/', '', $searchFilter);
    $searchFilter = trim($searchFilter);
    $words = array();
    //echo "Search: $searchFilter<br>\n";
    foreach (explode(",", $searchFilter) as $word) {
        $word = trim($word);
        if(preg_match('/\*/', $word) > 0) {
            $words[] = preg_replace('/\*/', '%', $dbh->quote("$word"));
        } else {
            $words[] = preg_replace('/\*/', '%', $dbh->quote("* $word *"));
            $words[] = preg_replace('/\*/', '%', $dbh->quote("$word *"));
            $words[] = preg_replace('/\*/', '%', $dbh->quote("* $word"));
        }
    }
    $searchSql = "AND ( message ILIKE " . implode(" OR message ILIKE ", $words) . " )";
}

// Show only lines with URL's in them

$linkSql = '';
if( isset($_REQUEST) && isset($_REQUEST["lo"]) ) {
    $linksOnly = 1;
    $linkSql = "AND is_url";
}
 
// Iinclude lines from known bots

$botSql = "AND NOT is_bot";
if( isset($_REQUEST) && isset($_REQUEST["sb"]) ) {
    $showBots = 1;
    $botSql = '';
}

// Output format requested

if( isset($_REQUEST) && isset($_REQUEST["fm"]) ) {
    if( $_REQUEST["fm"] == 'rss' ) {
        $format = 'rss';
    } elseif( $_REQUEST["fm"] == 'json' ) {
        $format = 'json';
    } elseif( $_REQUEST["fm"] == 'text' ) {
        $format = 'text';
    }
}

// Sort order
// Accept a comma-seperated list of column names with direction
// IE: msg_date asc, channel desc

/*
if( isset($_REQUEST) && isset($_REQUEST["so"]) ) {
    $sortOrder = $_REQUEST["so"];
    $words = array();
    foreach (explode(",", $sortOrder) as $word) {
        $words[] = $dbh->quote($word);
    }
    $sortSql = "ORDER BY " . implode(",", $words);
}
 */

// Data about last position, for recalculating offsets if the criteria changed

$anchorSql = '';
if( isset($_REQUEST) && isset($_REQUEST["an"]) ) {
    $anchorID = $_REQUEST["an"];
    if(!is_numeric($anchorID)) {
        $anchorID = null;
    }
    if( $anchorID < 0 ) {
        $anchorID = null;
    }
}
if( isset($anchorID) ) {
    //$anchorID = $data['rows'][0]->id;
    $anchorSql = "AND id <= $anchorID";
}

$totalRows = 0;
$countSql = "SELECT COUNT(*) FROM chanlogs $botSql $linkSql $chanSql $antichanSql $mudSql $speakerSql $searchSql $startDateSql $anchorSql";
$countSql = preg_replace('/chanlogs\s+AND/', 'chanlogs WHERE', $countSql);
//echo "SQL: $countSql\n";

try {
    $sth = $dbh->query($countSql);
}
catch(PDOException $e) {
    echo $e->getMessage();
}

$totalRows = $sth->fetchColumn();
$totalPages = ceil($totalRows / $pageSize);

if( isset($pageNumberEntered) ) {
    $pageNumber = $totalPages - $pageNumberEntered;
    if($pageNumber < 0) {
        $pageNumber = 0;
    }
    if($pageNumber > $totalPages) {
        $pageNumber = $totalPages;
    }
}

if( $pageNumber < ($totalPages / 2) ) {
    $offset = min($totalRows, $pageSize * $pageNumber);
    $sortSql = "ORDER BY id DESC";
    $reverseSort = 1;
} else {
    $offset = max(0, $totalRows - ($pageSize * ($pageNumber + 1)));
    $sortSql = "ORDER BY id ASC";
    $reverseSort = 0;
}

$limitSql = "LIMIT $pageSize";
$offsetSql = "OFFSET $offset";

$pageSql = "SELECT id, msg_date, date_part('epoch', msg_date) AS unix_date, to_char(msg_date, 'YYYY-MM-DD') AS the_date, to_char(msg_date, 'HH24:MI') AS the_time, to_char(msg_date, 'HH24') AS the_hour, channel, speaker, mud, message FROM chanlogs $botSql $linkSql $chanSql $antichanSql $mudSql $speakerSql $searchSql $startDateSql $anchorSql $sortSql $offsetSql $limitSql";
$pageSql = preg_replace('/chanlogs\s+AND/', 'chanlogs WHERE', $pageSql);

try {
    $sth = $dbh->query($pageSql);
}
catch(PDOException $e) {
    echo $e->getMessage();
}

/*
$data = array();
$data['page'] = $pageNumber;
$data['total'] = $totalRows;
$data['rows'] = array();

$sth->setFetchMode(PDO::FETCH_ASSOC);
while($row = $sth->fetch()) {
    $data['rows'][] = array(
        'id' => $row['id'],
        'cell' => array_values($row)
    );
}
 */

$data = array();
$data['page'] = $pageNumber;
$data['total'] = $totalRows;
$data['rows'] = array();
//$sth->setFetchMode(PDO::FETCH_ASSOC);
$sth->setFetchMode(PDO::FETCH_OBJ);
while($row = $sth->fetch()) {
    $data['rows'][] = $row;
}

$refresh_secs = 120;

if($yestube) {
    $video_list = get_play_list($dbh);
    $video_pick = array_rand($video_list, 1);
    //$refresh_secs = $video_list[$video_pick];
    $refresh_secs = $video_list[$video_pick]->video_len;
    $video_desc = $video_list[$video_pick]->description;
    $play_count =  $video_list[$video_pick]->plays;
    try {
        $upSql = "UPDATE videos SET plays = plays + 1 WHERE video_id = ?";
        $upQ = $dbh->prepare($upSql);
        $upQ->execute(array($video_pick));
    }
    catch(PDOException $e) {
        echo $e->getMessage();
    }

    //echo "len == $refresh_secs ---- desc == $video_desc";
    if(!$video_desc) {
        $video_desc = "&nbsp;video";
    } else {
        //$video_desc = '&nbsp;-&nbsp;' . preg_replace('/\s/', '&nbsp;', $video_desc);
        $video_desc = '&nbsp;-&nbsp;' . $video_desc;
    }
}

$i3quote = get_quote($dbh);

$dbh = null;

if( !isset($anchorID) ) {
    $anchorID = $data['rows'][0]->id;
}

if($reverseSort) {
    $data['rows'] = array_reverse($data['rows']);
}

//$anchorID = $data['rows'][0]->id;

$fewPages = 10;
$manyPages = 100;

$beginningPage = $totalPages;
$backManyPage = $pageNumber + $manyPages;
$backFewPage = $pageNumber + $fewPages;
$backOnePage = $pageNumber + 1;

$forwardOnePage = $pageNumber - 1;
$forwardFewPage = $pageNumber - $fewPages;
$forwardManyPage = $pageNumber - $manyPages;
$endPage = 0;

$beginningPageDisplay = $totalPages - $beginningPage;
$backManyPageDisplay = $totalPages - $backManyPage;
$backFewPageDisplay = $totalPages - $backFewPage;
$backOnePageDisplay = $totalPages - $backOnePage;
$pageNumberDisplay = $totalPages - $pageNumber;
$forwardOnePageDisplay = $totalPages - $forwardOnePage;
$forwardFewPageDisplay = $totalPages - $forwardFewPage;
$forwardManyPageDisplay = $totalPages - $forwardManyPage;
$endPageDisplay = $totalPages - $endPage;

function build_url() {
    global $pageNumber;
    global $defaultPageSize;
    global $pageSize;
    global $linksOnly;
    global $showBots;
    global $format;
    global $defaultFormat;
    global $channelFilter;
    global $antichannelFilter;
    global $speakerFilter;
    global $mudFilter;
    global $sortOrder;
    global $searchFilter;
    global $anchorID;
    global $urlParams;
    global $startDate;
    global $showSQL;
    global $yestube;
    global $youtube_visible;

    $urlParams = ((isset($pageNumber) && $pageNumber != 0) ? "&pn=" . urlencode($pageNumber) : "")
        . ($pageSize != $defaultPageSize ? "&ps=" . urlencode($pageSize) : "")
        . (isset($linksOnly) ? "&lo" : "")
        . (isset($showBots) ? "&sb" : "")
        . ($format != $defaultFormat ? "&fm=" . urlencode($format) : "")
        . (isset($channelFilter) ? "&cf=" . urlencode($channelFilter) : "")
        . (isset($antichannelFilter) ? "&af=" . urlencode($antichannelFilter) : "")
        . (isset($speakerFilter) ? "&sf=" . urlencode($speakerFilter) : "")
        . (isset($mudFilter) ? "&mf=" . urlencode($mudFilter) : "")
        . (isset($startDate) ? "&sd=" . urlencode($startDate) : "")
        . (isset($sortOrder) ? "&so=" . urlencode($sortOrder) : "")
        . (isset($searchFilter) ? "&sr=" . urlencode($searchFilter) : "")
        . ((isset($anchorID) && isset($pageNumber) && $pageNumber != 0) ? "&an=" . urlencode($anchorID) : "")
        . (isset($showSQL) ? "&showsql" : "")
        . (isset($youtube_visible) ? "&showtube" : "")
        . (isset($yestube) ? "&yestube" : "")
        ;

    $urlParams = preg_replace('/&/', '?', $urlParams, 1);
    return $_SERVER["PHP_SELF"] . $urlParams;
}


$old = $pageNumber; $pageNumber = $beginningPage; $beginningUrl = build_url(); $pageNumber = $old;
$old = $pageNumber; $pageNumber = $backManyPage; $backManyUrl = build_url(); $pageNumber = $old;
$old = $pageNumber; $pageNumber = $backFewPage; $backFewUrl = build_url(); $pageNumber = $old;
$old = $pageNumber; $pageNumber = $backOnePage; $backOneUrl = build_url(); $pageNumber = $old;
$pageUrl = build_url();
$old = $pageNumber; $pageNumber = $forwardOnePage; $forwardOneUrl = build_url(); $pageNumber = $old;
$old = $pageNumber; $pageNumber = $forwardFewPage; $forwardFewUrl = build_url(); $pageNumber = $old;
$old = $pageNumber; $pageNumber = $forwardManyPage; $forwardManyUrl = build_url(); $pageNumber = $old;
$old = $pageNumber; $pageNumber = $endPage; $endUrl = build_url(); $pageNumber = $old;

$old = $format; $format = "text"; $textUrl = build_url(); $format = $old; build_url();
$old = $format; $format = "rss"; $rssUrl = build_url(); $format = $old; build_url();
$old = $format; $format = "json"; $jsonUrl = build_url(); $format = $old; build_url();

$old = $yestube; $yestube = null; $notubeUrl = build_url(); $yestube = $old; build_url();
$old = $youtube_visible; $old2 = $yestube; $youtube_visible = 1; $yestube = 1; $showtubeUrl = build_url(); $youtube_visible = $old; $yestube = $old2; build_url();
$old = $youtube_visible; $old2 = $yestube; $youtube_visible = Null; $yestube = 1; $hidetubeUrl = build_url(); $youtube_visible = $old; $yestube = $old2; build_url();

/*
echo "<br>yestube: $youtube_visible $yestubeUrl<br>\n";
echo "<br>showtube: $youtube_visible $showtubeUrl<br>\n";
echo "<br>hidetube: $youtube_visible $hidetubeUrl<br>\n";
 */

/*
echo "<br>SQL: $pageSql<br>\n";
echo "anchorID == $anchorID<br>\n";
echo "beginning == $beginningPage, $beginningPageDisplay, $beginningUrl<br>\n";
echo "backMany == $backManyPage, $backManyPageDisplay, $backManyUrl<br>\n";
echo "backFew == $backFewPage, $backFewPageDisplay, $backFewUrl<br>\n";
echo "backOne == $backOnePage, $backOnePageDisplay, $backOneUrl<br>\n";
echo "current == $pageNumber, $pageNumberDisplay, $pageUrl<br>\n";
echo "forwardOne == $forwardOnePage, $forwardOnePageDisplay, $forwardOneUrl<br>\n";
echo "forwardFew == $forwardFewPage, $forwardFewPageDisplay, $forwardFewUrl<br>\n";
echo "forwardMany == $forwardManyPage, $forwardManyPageDisplay, $forwardManyUrl<br>\n";
echo "end == $endPage, $endPageDisplay, $endUrl<br>\n";
 */

$bg = 0;
$html = array();
$text = array();
$rss = array();
$json = array();
foreach ($data['rows'] as $row) {
    $bgColor = ($bg % 2) ? "#000000" : "#1F1F1F";
    $bgBold = ($bg % 2) ? "#202040" : "#3F3F6F";

    $datestamp = $row->the_date;
    $hourColor = $hourColors[$row->the_hour];
    $timestamp = $hourColor . $row->the_time . "</SPAN>";

    $channelColor = $channelColors["default"];
    if( array_key_exists( $row->channel, $channelColors )) {
        $channelColor = $channelColors[strtolower($row->channel)];
    }
    $channel = "$channelColor" . $row->channel . "</SPAN>";

    $speakerColor = $colormap[strtolower($row->speaker)];
    $speaker = "$speakerColor" . $row->speaker . "@" . $row->mud . "</SPAN>";

    $filtered_message = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '_', $row->message);
    //$filtered_message = $row->message;
    $tmp_msg = preg_replace("/\x1b\[[0-9]+(;[0-9]+)*m/", "", $row->message);
    $message = htmlentities($tmp_msg,0,'UTF-8');
    //$message = preg_replace('/ /', '&#x2004;', $message); // replace spaces with unicode THREE-PER-EM SPACE
    //$message = preg_replace('/-/', '&#x8209;', $filtered_message); // replace hyphen with unicode NON-BREAKING HYPHEN
    $message = preg_replace( '/((?:http|https|ftp)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?::[a-zA-Z0-9]*)?\/?(?:[a-zA-Z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*)/', '<a href="$1" target="I3-link">$1</a>', $message);

    //$b64 = $row->b64;
    //if(!is_null($b64)) {
    //    $b64 = utf8_decode(base64_decode($b64));
    //}

    $html[] = array(
        "row"           => $row,
        "bgcolor"       => $bgColor,
        "bgbold"        => $bgBold,
        "datestamp"     => $datestamp,
        "timestamp"     => $timestamp,
        "channel"       => $channel,
        "speaker"       => $speaker,
        "message"       => $message,
        "unix_date"     => $row->unix_date,
        "raw_channel"   => $row->channel,
        "raw_speaker"   => $row->speaker,
        "raw_mud"       => $row->mud,
        //"b64"           => $b64,
    );
    $text[] = array(
        "row"           => $row,
        "datestamp"     => $row->the_date,
        "timestamp"     => $row->the_time,
        "channel"       => $row->channel,
        "speaker"       => $row->speaker,
        "mud"           => $row->mud,
        "message"       => $filtered_message,
    );
    $rss[] = array(
        "row"           => $row,
        "title"         => $row->the_date . " " . $row->the_time . " (" . $row->channel . ") " . $row->speaker . "@" . $row->mud . ": " . substr($filtered_message, 0, 120),
        "description"   => $filtered_message,
        "link"          => $RSS_FEED_URL,
        "guid"          => md5( $row->channel . $row->speaker . "@" . $row->mud . $row->message ),
    );
    $json[] = array(
        "bgcolor"       => $bgColor,
        "bgbold"        => $bgBold,
        "hourcolor"     => $hourColor,
        "channelcolor"  => $channelColor,
        "speakercolor"  => $speakerColor,
        "mudcolor"      => $speakerColor,
        "guid"          => md5( $row->channel . $row->speaker . "@" . $row->mud . $row->message ),
        "id"            => $row->id,
        "datestamp"     => $row->the_date,
        "timestamp"     => $row->the_time,
        "channel"       => $row->channel,
        "speaker"       => $row->speaker,
        "mud"           => $row->mud,
        "message"       => $row->message,
    );
    $bg++;
}

/*
echo "pageSize: $pageSize\n";
echo "pageNumber: $pageNumber\n";
echo "totalRows: $totalRows\n";
echo "totalPages: $totalPages\n";

echo json_encode($data);
 */

if($format == 'html') {
    header('Content-type: text/html; charset=utf-8')
?>
<html>
    <head>
        <title> Intermud-3 network traffic, as seen by <? echo $MUD_NAME; ?>. </title>
        <meta http-equiv="refresh" content="<? echo $refresh_secs; ?>">
        <style>
            a { text-decoration:none; }
            a:hover { text-decoration:underline; }
        </style>
        <!-- <script src="jq/js/jquery-1.9.1.js"></script>
        <script src="jq/js/jquery-ui-1.10.1.custom.js"></script> -->
        <script type="text/javascript" src="popup.js"></script>
        <script language="javascript">
            function toggleDiv(divID) {
                if(document.getElementById(divID).style.display == 'none') {
                    document.getElementById(divID).style.display = 'block';
                } else {
                    document.getElementById(divID).style.display = 'none';
                }
            }
        </script>
        <script language="javascript">
            function beginsWith(needle, haystack) {
                return (haystack.substr(0, needle.length) == needle);
            }
        </script>
        <script language="javascript">
            function toggleShow(urlId, divID) {
                if(document.getElementById(divID).style.display == 'none') {
                    document.getElementById(divID).style.display = 'block';
                } else {
                    document.getElementById(divID).style.display = 'none';
                }
                if(beginsWith('Show', document.getElementById(urlID).innerHTML)) {
                    document.getElementById(urlID).innerHTML.replace('Show', 'Hide');
                } else {
                    document.getElementById(urlID).innerHTML.replace('Hide', 'Show');
                }
            }
        </script>
    </head>
    <body bgcolor="black" text="#d0d0d0" link="#ffffbf" vlink="#ffa040">
<? if($yestube) { ?>
        <div id="youtube" style="display: none; position: fixed; z-index: -99; width: 100%; height: 100%">
            <iframe frameborder="0" height="100%" width="100%"
                    src="https://youtube.com/embed/<? echo $video_pick; ?>?autoplay=1&controls=0&showinfo=0&autohide=1">
                    <!-- version=3&enablejsapi=1 -->
                    <!-- playlist=fCPzLNqYe1U,P1Bf_fRNq9g, -->
                    <!-- Uvl3ef7D5rg -->
                    <!-- KGG0t-psqNo -->
            </iframe>
        </div>
<? } ?>
        <table id="header" border=0 cellspacing=0 cellpadding=0 width=80% align="center">
            <tr>
                <!-- Header logos -->
                <td align="right" valign="bottom"
                    style="vertical-align: bottom; opacity: 0.7; filter: alpha(opacity=70);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.7'; this.style.filter='alpha(opacity=70';"
                >
                    <table id="logo" border=0 cellspacing=0 cellpadding=0 width=100% align="center">
                        <tr>
                            <td align="right" valign="top" style="vertical-align: top">
                                <!-- <a href="/anyterm/anyterm.shtml?rows=40&cols=100"> -->
                                <a href="/~bloodlines">
                                    <img src="<? echo $graphics['bloodlines']; ?>" border=0 width=234 height=80>
                                </a>
                            </td>
                            <td align="left" valign="bottom" style="vertical-align: bottom">
                                <!-- <a href="/anyterm/anyterm.shtml?rows=40&cols=100"> -->
                                <a href="/~bloodlines">
                                    <img src="<? echo $graphics['wileymud4']; ?>" border=0 width=177 height=40">
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
                <!-- Header logos END -->
                <!-- Search form -->
                <td align="left" valign="bottom" style="vertical-align: bottom">
                    <form action="" method="get">
                        <table id="searchform" border=0 cellspacing=0 cellpadding=0 width=100% align="center">
                            <tr>
                                <td align="right" valign="bottom" style="vertical-align: bottom">
                                    <span style="color: #1F1F1F;">
                                        <label id="srlabel" for="sr"
                                            onmouseover="srinput.style.color='#FFFF00'; srinput.style.backgroundColor='#1F1F1F'; srlabel.style.color='#FFFFFF';"
                                            onmouseout="srinput.style.color='#4F4F00'; srinput.style.backgroundColor='#000000'; srlabel.style.color='#1F1F1F';"
                                            onfocus="srinput.focus();"
                                            onclick="srinput.focus();"
                                        > Search:&nbsp; </label>
                                    </span>
                                </td>
                                <td bgcolor="#000000" width="200" align="left" valign="bottom" style="vertical-align: bottom">
                                    <input id="srinput" type="text" style="background-color: #000000; color: #4F4F00; border: 1px; border-color: #000000; border-style: solid; width: 200px;"
                                        onmouseover="this.style.color='#FFFF00'; this.style.backgroundColor='#1F1F1F'; srlabel.style.color='#FFFFFF';"
                                        onfocus="this.style.color='#FFFF00'; this.style.backgroundColor='#1F1F1F'; srlabel.style.color='#FFFFFF'; if(!this._haschanged){this.value=''};this._haschanged=true;"
                                        onblur="this.style.color='#4F4F00'; this.style.backgroundColor='#000000'; srlabel.style.color='#1F1F1F';"
                                        onmouseout="this.style.color='#4F4F00'; this.style.backgroundColor='#000000'; srlabel.style.color='#1F1F1F';"
                                        maxlength="30" name="sr" value="<? if(isset($searchFilter)) echo $searchFilter; ?>" />
                                    <? if(isset($pageSize)) { ?>
                                        <input type="hidden" name="ps" value="<? echo $pageSize; ?>">
                                    <? } ?>
                                    <? if(isset($pageNumber)) { ?>
                                        <input type="hidden" name="pn" value="<? echo $pageNumber; ?>">
                                    <? } ?>
                                    <? if(isset($linksOnly)) { ?>
                                        <input type="hidden" name="lo" value="<? echo $linksOnly; ?>">
                                    <? } ?>
                                    <? if(isset($showBots)) { ?>
                                        <input type="hidden" name="sb" value="<? echo $showBots; ?>">
                                    <? } ?>
                                    <? if(isset($format)) { ?>
                                        <input type="hidden" name="fm" value="<? echo $format; ?>">
                                    <? } ?>
                                    <? if(isset($channelFilter)) { ?>
                                        <input type="hidden" name="cf" value="<? echo $channelFilter; ?>">
                                    <? } ?>
                                    <? if(isset($antichannelFilter)) { ?>
                                        <input type="hidden" name="af" value="<? echo $antichannelFilter; ?>">
                                    <? } ?>
                                    <? if(isset($speakerFilter)) { ?>
                                        <input type="hidden" name="sf" value="<? echo $speakerFilter; ?>">
                                    <? } ?>
                                    <? if(isset($mudFilter)) { ?>
                                        <input type="hidden" name="mf" value="<? echo $mudFilter; ?>">
                                    <? } ?>
                                    <? if(isset($sortOrder)) { ?>
                                        <input type="hidden" name="so" value="<? echo $sortOrder; ?>">
                                    <? } ?>
                                    <? if(isset($startDate)) { ?>
                                        <input type="hidden" name="sd" value="<? echo $startDate; ?>">
                                    <? } ?>
                                    <? if(isset($anchorID)) { ?>
                                        <input type="hidden" name="an" value="<? echo $anchorID; ?>">
                                    <? } ?>
                                </td>
                            </tr>
                        </table>
                    </form>
                </td>
                <!-- Search form END -->
            </tr>
        </table>
        <table id="navbar" border=0 cellspacing=0 cellpadding=0 width=100% align="center">
            <tr>
                <td id="navbegin" align="left" valign="center" width="50"
                    <? if( $pageNumber < $beginningPage) { ?>
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="vertical-align: middle; opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <span style="color: #555555">
                    <? if( $pageNumber < $beginningPage) { ?>
                        <a href="<? echo $beginningUrl; ?>" title="The&nbsp;Beginning&nbsp;(<? echo $beginningPageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="<? echo $graphics['navbegin']; ?>" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="<? echo $graphics['navbegin']; ?>" border=0 width=48 height=48 />
                    <? } ?>
                    </span>
                </td>
                <td id="navbackmany" align="left" valign="center" width="50"
                    <? if( $pageNumber < $backManyPage && $backManyPage <= $beginningPage) { ?>
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="vertical-align: middle; opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <span style="color: #555555">
                    <? if( $pageNumber < $backManyPage && $backManyPage <= $beginningPage) { ?>
                        <a href="<? echo $backManyUrl; ?>" title="Back&nbsp;<? echo $manyPages; ?>&nbsp;(<? echo $backManyPageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="<? echo $graphics['navback']; ?>" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="<? echo $graphics['navback']; ?>" border=0 width=48 height=48 />
                    <? } ?>
                    </span>
                </td>
                <td id="navbackfew" align="left" valign="center" width="50"
                    <? if( $pageNumber < $backFewPage && $backFewPage <= $beginningPage) { ?>
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="vertical-align: middle; opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <span style="color: #555555">
                    <? if( $pageNumber < $backFewPage && $backFewPage <= $beginningPage) { ?>
                        <a href="<? echo $backFewUrl; ?>" title="Back&nbsp;<? echo $fewPages; ?>&nbsp;(<? echo $backFewPageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="<? echo $graphics['navback']; ?>" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="<? echo $graphics['navback']; ?>" border=0 width=48 height=48 />
                    <? } ?>
                    </span>
                </td>
                <td id="navprev" align="left" valign="center" width="50"
                    <? if( $pageNumber < $backOnePage && $backOnePage <= $beginningPage) { ?>
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="vertical-align: middle; opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <span style="color: #555555">
                    <? if( $pageNumber < $backOnePage && $backOnePage <= $beginningPage) { ?>
                        <a href="<? echo $backOneUrl; ?>" title="Previous&nbsp;Page&nbsp;(<? echo $backOnePageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="<? echo $graphics['navprev']; ?>" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="<? echo $graphics['navprev']; ?>" border=0 width=48 height=48 />
                    <? } ?>
                    </span>
                </td>
                <td>
                &nbsp;
                </td>
                <td id="fakepagenumber" align="center" valign="center" width="150" style="vertical-align: middle;">
                &nbsp;
                </td>
                <td>
                    &nbsp;
                </td>
                <td id="navhelp" align="center" valign="center" width="50"
                    style="vertical-align: middle; opacity: 0.2; filter: alpha(opacity=20);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.2'; this.style.filter='alpha(opacity=20';"
                >
                    <a title="HELP!" href="i3log_help.html" class="popup2">
                        <img src="<? echo $graphics['help_icon']; ?>" border=0 width=48 height=48 />
                    </a>
                </td>
                <td id="navbot" align="center" valign="center" width="50"
                    <? if( isset($showBots) ) { ?>
                    style="vertical-align: middle; opacity: 1.0; filter: alpha(opacity=100);"
                    onmouseover="this.style.opacity='0.2'; this.style.filter='alpha(opacity=20';"
                    onmouseout="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    <? } else { ?>
                    style="vertical-align: middle; opacity: 0.2; filter: alpha(opacity=20);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.2'; this.style.filter='alpha(opacity=20';"
                    <? } ?>
                >
                    <? if( !isset($showBots) ) { ?>
                    <a title='Include bot content' href="<? $showBots = 1; echo build_url(); $showBots = null; build_url(); ?>">
                        <img src="<? echo $graphics['navconfig']; ?>" border=0 width=48 height=48 />
                    </a>
                    <? } else { ?>
                    <a title='Block messages from known bots' href="<? $showBots = null; echo build_url(); $showBots = 1; build_url(); ?>">
                        <img src="<? echo $graphics['navconfig']; ?>" border=0 width=48 height=48 />
                    </a>
                    <? } ?>
                </td>
                <td id="navlinks" align="center" valign="center" width="50"
                    <? if( isset($linksOnly) ) { ?>
                    style="vertical-align: middle; opacity: 1.0; filter: alpha(opacity=100);"
                    onmouseover="this.style.opacity='0.2'; this.style.filter='alpha(opacity=20';"
                    onmouseout="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    <? } else { ?>
                    style="vertical-align: middle; opacity: 0.2; filter: alpha(opacity=20);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.2'; this.style.filter='alpha(opacity=20';"
                    <? } ?>
                >
                    <? if( isset($linksOnly) ) { ?>
                    <a title='Include all content' href="<? $linksOnly = null; echo build_url(); $linksOnly = 1; build_url(); ?>">
                        <img src="<? echo $graphics['navlinks']; ?>" border=0 width=48 height=48 />
                    </a>
                    <? } else { ?>
                    <a title='Include only messages with URLs' href="<? $linksOnly = 1; echo build_url(); $linksOnly = null; build_url(); ?>">
                        <img src="<? echo $graphics['navlinks']; ?>" border=0 width=48 height=48 />
                    </a>
                    <? } ?>
                </td>
                <td id="navhome" align="center" valign="center" width="50"
                    <? if( count( $_GET  ) > 0 ) { ?>
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="vertical-align: middle; opacity: 1.0; filter: alpha(opacity=100);"
                    <? } ?>
                >
                    <a title="HOME!" href="<? echo $_SERVER['PHP_SELF']; ?>">
                        <img src="<? echo $graphics['navhome']; ?>" border=0 width=48 height=48 />
                    </a>
                </td>
                <td>
                <td id="navpie" align="center" valign="center" width="50"
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                >
                    <a title="Everyone loves PIE!" href="i3pie.html">
                        <img src="<? echo $graphics['pie_chart']; ?>" border=0 width=48 height=48 />
                    </a>
                </td>
                <td id="navchart" align="center" valign="center" width="50"
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                >
                    <a title="BAR chat" href="i3bar.html">
                        <img src="<? echo $graphics['bar_chart']; ?>" border=0 width=48 height=48 />
                    </a>
                </td>
                <td>
                &nbsp;
                </td>
                <td id="pagenumber" align="center" valign="center" width="150" style="vertical-align: middle;">
<!-- &nbsp; Page <? echo $pageNumberDisplay; ?> of <? echo $totalPages; ?> &nbsp; -->
                    <form action="" method="get">
                        <table id="pageform" border=0 cellspacing=0 cellpadding=0 width=100% align="center" style="vertical-align: middle;">
                            <tr>
                                <td align="right" valign="bottom" width="40" style="vertical-align: bottom;">
                                    <span style="color: #1F1F1F;">
                                        <label id="pnlabel" for="pd"
                                            onmouseover="pninput.style.color='#FFFF00'; pninput.style.backgroundColor='#1F1F1F'; this.style.color='#FFFFFF'; pnoflabel.style.color='#FFFFFF';"
                                            onmouseout="pninput.style.color='#4F4F00'; pninput.style.backgroundColor='#000000'; this.style.color='#1F1F1F'; pnoflabel.style.color='#1F1F1F';"
                                            onfocus="pninput.focus();"
                                            onclick="pninput.focus();"
                                        > Page:&nbsp; </label>
                                    </span>
                                </td>
                                <td bgcolor="#000000" width="50" align="left" valign="bottom" style="vertical-align: bottom;">
                                    <input id="pninput" type="text" style="background-color: #000000; color: #4F4F00; border: 1px; border-color: #000000; border-style: solid; width: 50px;"
                                        onmouseover="this.style.color='#FFFF00'; this.style.backgroundColor='#1F1F1F'; pnlabel.style.color='#FFFFFF'; pnoflabel.style.color='#FFFFFF';"
                                        onfocus="this.style.color='#FFFF00'; this.style.backgroundColor='#1F1F1F'; pnlabel.style.color='#FFFFFF';  pnoflabel.style.color='#FFFFFF'; /* if(!this._haschanged){this.value=''};this._haschanged=true; */"
                                        onblur="this.style.color='#4F4F00'; this.style.backgroundColor='#000000'; pnlabel.style.color='#1F1F1F'; pnoflabel.style.color='#1F1F1F';"
                                        onmouseout="this.style.color='#4F4F00'; this.style.backgroundColor='#000000'; pnlabel.style.color='#1F1F1F'; pnoflabel.style.color='#1F1F1F';"
                                        maxlength="6" name="pd" value="<? if(isset($pageNumberDisplay)) echo $pageNumberDisplay; ?>" />
                                    <? if(isset($pageSize)) { ?>
                                        <input type="hidden" name="ps" value="<? echo $pageSize; ?>">
                                    <? } ?>
                                    <? if(isset($linksOnly)) { ?>
                                        <input type="hidden" name="lo" value="<? echo $linksOnly; ?>">
                                    <? } ?>
                                    <? if(isset($showBots)) { ?>
                                        <input type="hidden" name="sb" value="<? echo $showBots; ?>">
                                    <? } ?>
                                    <? if(isset($format)) { ?>
                                        <input type="hidden" name="fm" value="<? echo $format; ?>">
                                    <? } ?>
                                    <? if(isset($channelFilter)) { ?>
                                        <input type="hidden" name="cf" value="<? echo $channelFilter; ?>">
                                    <? } ?>
                                    <? if(isset($antichannelFilter)) { ?>
                                        <input type="hidden" name="af" value="<? echo $antichannelFilter; ?>">
                                    <? } ?>
                                    <? if(isset($speakerFilter)) { ?>
                                        <input type="hidden" name="sf" value="<? echo $speakerFilter; ?>">
                                    <? } ?>
                                    <? if(isset($mudFilter)) { ?>
                                        <input type="hidden" name="mf" value="<? echo $mudFilter; ?>">
                                    <? } ?>
                                    <? if(isset($searchFilter)) { ?>
                                        <input type="hidden" name="sr" value="<? echo $searchFilter; ?>">
                                    <? } ?>
                                    <? if(isset($sortOrder)) { ?>
                                        <input type="hidden" name="so" value="<? echo $sortOrder; ?>">
                                    <? } ?>
                                    <? if(isset($startDate)) { ?>
                                        <input type="hidden" name="sd" value="<? echo $startDate; ?>">
                                    <? } ?>
                                    <? if(isset($anchorID)) { ?>
                                        <input type="hidden" name="an" value="<? echo $anchorID; ?>">
                                    <? } ?>
                                </td>
                                <td align="right" valign="bottom" width="50" style="vertical-align: bottom;">
                                    <span style="color: #1F1F1F;">
                                        <label id="pnoflabel" for="pn"
                                            onmouseover="pninput.style.color='#FFFF00'; pninput.style.backgroundColor='#1F1F1F'; this.style.color='#FFFFFF'; pnlabel.style.color='#FFFFFF';"
                                            onmouseout="pninput.style.color='#4F4F00'; pninput.style.backgroundColor='#000000'; this.style.color='#1F1F1F'; pnlabel.style.color='#1F1F1F';"
                                            onfocus="pninput.focus();"
                                            onclick="pninput.focus();"
                                            > of&nbsp;<? echo $totalPages; ?> </label>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </form>
                </td>
                <td>
                &nbsp;
                </td>
                <td id="navnext" align="right" valign="center" width="50"
                    <? if( $pageNumber > $forwardOnePage && $forwardOnePage >= $endPage) { ?>
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="vertical-align: middle; opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <? if( $pageNumber > $forwardOnePage && $forwardOnePage >= $endPage) { ?>
                        <a href="<? echo $forwardOneUrl; ?>" title="Next&nbsp;Page&nbsp;(<? echo $forwardOnePageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="<? echo $graphics['navnext']; ?>" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="<? echo $graphics['navnext']; ?>" border=0 width=48 height=48 />
                    <? } ?>
                </td>
                <td id="navforwardfew" align="right" valign="center" width="50"
                    <? if( $pageNumber > $forwardFewPage && $forwardFewPage >= $endPage) { ?>
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="vertical-align: middle; opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <? if( $pageNumber > $forwardFewPage && $forwardFewPage >= $endPage) { ?>
                        <a href="<? echo $forwardFewUrl; ?>" title="Next&nbsp;<? echo $fewPages; ?>&nbsp;(<? echo $forwardFewPageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="<? echo $graphics['navforward']; ?>" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="<? echo $graphics['navforward']; ?>" border=0 width=48 height=48 />
                    <? } ?>
                </td>
                <td id="navforwardmany" align="right" valign="center" width="50"
                    <? if( $pageNumber > $forwardManyPage && $forwardManyPage >= $endPage) { ?>
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="vertical-align: middle; opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <? if( $pageNumber > $forwardManyPage && $forwardManyPage >= $endPage) { ?>
                        <a href="<? echo $forwardManyUrl; ?>" title="Next&nbsp;<? echo $manyPages; ?>&nbsp;(<? echo $forwardManyPageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="<? echo $graphics['navforward']; ?>" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="<? echo $graphics['navforward']; ?>" border=0 width=48 height=48 />
                    <? } ?>
                </td>
                <td id="navend" align="right" valign="center" width="50"
                    <? if( $pageNumber > $endPage) { ?>
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="vertical-align: middle; opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <? if( $pageNumber > $endPage) { ?>
                        <a href="<? echo $endUrl; ?>" title="Current&nbsp;Time&nbsp;(<? echo $endPageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="<? echo $graphics['navend']; ?>" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="<? echo $graphics['navend']; ?>" border=0 width=48 height=48 />
                    <? } ?>
                </td>
            </tr>
        </table>
        <table id="content" width="100%">
            <tr>
                <? if(isset($startDate)) { ?>
                <th id="dateheader" align="left" width="80px" style="color: #FFFF00; min-width: 80px;">
                    <a href="<? $old = $startDate; $startDate = null; echo build_url(); $startDate = $old; build_url(); ?>">Date</a>
                </th>
                <? } else { ?>
                <th id="dateheader" align="left" width="80px" style="color: #DDDDDD; min-width: 80px;">Date</th>
                <? } ?>
                <? if(isset($startDate)) { ?>
                <th id="timeheader" align="left" width="40px" style="color: #FFFF00; min-width: 40px;">
                    <a href="<? $old = $startDate; $startDate = null; echo build_url(); $startDate = $old; build_url(); ?>">Time</a>
                </th>
                <? } else { ?>
                <th id="timeheader" align="left" width="40px" style="color: #DDDDDD; min-width: 40px;">Time</th>
                <? } ?>
                <? if(isset($channelFilter)) { ?>
                <th id="channelheader" align="left" width="100px" style="color: #FFFF00; min-width: 100px;">
                    <a href="<? $old = $channelFilter; $channelFilter = null; echo build_url(); $channelFilter = $old; build_url(); ?>">Channel</a>
                </th>
                <? } else { ?>
                <th id="channelheader" align="left" width="100px" style="color: #DDDDDD; min-width: 100px;">Channel</th>
                <? } ?>
                <? if(isset($speakerFilter)) { ?>
                <th id="speakerheader" align="left" width="200px" style="color: #FFFF00; min-width: 200px;">
                    <a href="<? $old = $speakerFilter; $old2 = $mudFilter; $speakerFilter = null; $mudFilter = null; echo build_url(); $speakerFilter = $old; $mudFilter = $old2; build_url(); ?>">Speaker</a>
                </th>
                <? } else { ?>
                <th id="speakerheader" align="left" width="200px" style="color: #DDDDDD; min-width: 200px;">Speaker</th>
                <? } ?>
                <th align="left">&nbsp;</th>
            </tr>
            <?  foreach ($html as $row) {
                    if(isset($startDate)) {
                        $old = $startDate;
                        $startDate = null;
                        $dateUrl = build_url();
                        $startDate = $old;
                    } else {
                        $startDate = $row["unix_date"];
                        $dateUrl = build_url();
                        $startDate = null;
                    }
                    if(isset($channelFilter)) {
                        $channels = array_unique(array_merge( explode(",", $channelFilter), array($row["raw_channel"])));
                        $old = $channelFilter;
                        $channelFilter = implode(",", $channels);
                        $channelUrl = build_url();
                        $channelFilter = $old;
                    } else {
                        $channelFilter = $row["raw_channel"];
                        $channelUrl = build_url();
                        $channelFilter = null;
                    }
                    if(isset($speakerFilter)) {
                        $speakers = array_unique(array_merge( explode(",", $speakerFilter), array($row["raw_speaker"])));
                        $muds = array_unique(array_merge( explode(",", $mudFilter), array($row["raw_mud"])));
                        $old = $speakerFilter;
                        $old2 = $mudFilter;
                        $speakerFilter = implode(",", $speakers);
                        $mudFilter = implode(",", $muds);
                        $speakerUrl = build_url();
                        $speakerFilter = $old;
                        $mudFilter = $old2;
                    } else {
                        $speakerFilter = $row["raw_speaker"];
                        $mudFilter = $row["raw_mud"];
                        $speakerUrl = build_url();
                        $speakerFilter = null;
                        $mudFilter = null;
                    }
             ?>
            <tr>
                <? if(isset($startDate)) { ?>
                    <td bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['datestamp']; ?></td>
                    <td bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['timestamp']; ?></td>
                <? } else { ?>
                    <td onmouseover="this.style.backgroundColor = '<? echo $row['bgbold']; ?>';"
                        onmouseout="this.style.backgroundColor = '<? echo $row['bgcolor']; ?>';"
                        onclick="document.location.href='<? echo $dateUrl; ?>';" bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['datestamp']; ?></td>
                    <td onmouseover="this.style.backgroundColor = '<? echo $row['bgbold']; ?>';"
                        onmouseout="this.style.backgroundColor = '<? echo $row['bgcolor']; ?>';"
                        onclick="document.location.href='<? echo $dateUrl; ?>';" bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['timestamp']; ?></td>
                <? } ?>

                <? if(isset($channelFilter)) { ?>
                    <td bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['channel']; ?></td>
                <? } else { ?>
                    <td onmouseover="this.style.backgroundColor = '<? echo $row['bgbold']; ?>';"
                        onmouseout="this.style.backgroundColor = '<? echo $row['bgcolor']; ?>';"
                        onclick="document.location.href='<? echo $channelUrl; ?>';" bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['channel']; ?></td>
                <? } ?>

                <? if(isset($speakerFilter)) { ?>
                    <td bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['speaker']; ?></td>
                <? } else { ?>
                    <td onmouseover="this.style.backgroundColor = '<? echo $row['bgbold']; ?>';"
                        onmouseout="this.style.backgroundColor = '<? echo $row['bgcolor']; ?>';"
                        onclick="document.location.href='<? echo $speakerUrl; ?>';" bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['speaker']; ?></td>
                <? } ?>
                <?
                    $msg = $row['message'];
                    //if(!is_null($row['b64'])) {
                    //    $msg = $row['b64'];
                    //}
                    //$msg = preg_replace('/ /', '&#x2004;', $row['message']);
                ?>

                    <td bgcolor="<? echo $row['bgcolor']; ?>"><font face="monospace"><? echo $msg; ?></font></td>
            </tr>
            <? } ?>
        </table>
        <table width="100%">
            <tr>
                <td align="left" width="30%" onmouseover="lastrefresh.style.color='#FFFF00'; pagegen.style.color='#00FF00'; timespent.style.color='#FF0000';" onmouseout="lastrefresh.style.color='#1F1F1F'; pagegen.style.color='#1F1F1F'; timespent.style.color='#1F1F1F';">
                    <span id="lastrefresh" style="color: #1F1F1F">Last refreshed at <? echo $mini_now; ?>.<br /></span>
<? if($yestube) { ?>
                    <span style="color: #1F1F1F"><a href="https://www.youtube.com/watch?v=<? echo $video_pick; ?>">youtube<? echo $video_desc; ?></a></span>
                    <br />

                    <span id="visible_span" style="display: none; color: #1F1F1F">
                        <a href="javascript:;" onmousedown="toggleDiv('visible_span');toggleDiv('hide_span');toggleDiv('youtube');">Hide&nbsp;Youtube</a>
                    </span>
                    <span id="hide_span" style="display: block; color: #1F1F1F">
                        <a href="javascript:;" onmousedown="toggleDiv('visible_span');toggleDiv('hide_span');toggleDiv('youtube');">Show&nbsp;Youtube</a>
                    </span>

                    <br />
                    <span style="color: #1F1F1F"><a href="<? echo $notubeUrl; ?>">Disable&nbsp;Youtube</a></span>
<? } else { ?>
                    <span style="color: #1F1F1F"><a href="<? echo $hidetubeUrl; ?>">Enable&nbsp;Youtube</a></span>
<? } ?>
                </td>
                <td>&nbsp;</td>
                <td id="server" align="center" valign="center" width="80"
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                >
                    <span style="color: #1F1F1F"><a href="/~bloodlines/server.php" title="Server Stats">
                        <img src="<? echo $graphics['server_icon']; ?>" border=0 width=78 height=78 alt="(server)" />
                    </a></span>
                </td>
                <td id="sql" align="center" valign="center" width="80"
                    style="vertical-align: middle; opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                >
                    <span style="color: #1F1F1F"><a href="/~bloodlines/i3log_dump.sql.bz2" title="SQL Dump (LARGE)">
                        <img src="<? echo $graphics['sql_icon']; ?>" border=0 width=78 height=78 alt="(sql)" />
                    </a></span>
                </td>
                <td align="center" width="71" onmouseover="lastrefresh.style.color='#FFFF00'; pagegen.style.color='#00FF00'; timespent.style.color='#FF0000';" onmouseout="lastrefresh.style.color='#1F1F1F'; pagegen.style.color='#1F1F1F'; timespent.style.color='#1F1F1F';">
                    <span style="color: #1F1F1F"><a href="<? echo $rssUrl; ?>" title="RSS Feed">
                        <img onmouseover="this.src='<?echo $graphics['rssMouseOver'];?>';" onmouseout="this.src='<?echo $graphics['rss'];?>';" id="rssimg" src="<?echo $graphics['rss'];?>" border=0 width=71 height=55 alt="(RSS)" />
                    </a></span>
                </td>
                <td align="center" width="75" onmouseover="lastrefresh.style.color='#FFFF00'; pagegen.style.color='#00FF00'; timespent.style.color='#FF0000'; jsonimg.src='http://i302.photobucket.com/albums/nn96/quixadhal/json_zps34e3c065.png';" onmouseout="lastrefresh.style.color='#1F1F1F'; pagegen.style.color='#1F1F1F'; timespent.style.color='#1F1F1F'; jsonimg.src='http://i302.photobucket.com/albums/nn96/quixadhal/jsonMouseOver_zps46d5148d.png';">
                    <span style="color: #1F1F1F"><a href="<? echo $jsonUrl; ?>" title="JSON output">
                        <img onmouseover="this.src='<?echo $graphics['jsonMouseOver'];?>';" onmouseout="this.src='<?echo $graphics['json'];?>';" id="jsonimg" src="<?echo $graphics['json'];?>" border=0 width=75 height=51 alt="(JSON)" />
                    </a></span>
                </td>
                <td align="center" width="84" onmouseover="lastrefresh.style.color='#FFFF00'; pagegen.style.color='#00FF00'; timespent.style.color='#FF0000';" onmouseout="lastrefresh.style.color='#1F1F1F'; pagegen.style.color='#1F1F1F'; timespent.style.color='#1F1F1F';">
                    <span style="color: #1F1F1F"><a href="<? echo $textUrl; ?>" title="Plain Text output">
                        <img onmouseover="this.src='<?echo $graphics['textMouseOver'];?>';" onmouseout="this.src='<?echo $graphics['text'];?>';" id="textimg" src="<?echo $graphics['text'];?>" border=0 width=84 height=79 alt="(TEXT)" />
                    </a></span>
                </td>
                <td>&nbsp;</td>
                <td align="right" width="30%" onmouseover="lastrefresh.style.color='#FFFF00'; pagegen.style.color='#00FF00'; timespent.style.color='#FF0000';" onmouseout="lastrefresh.style.color='#1F1F1F'; pagegen.style.color='#1F1F1F'; timespent.style.color='#1F1F1F';">
                    <a href="javascript:;" onmousedown="toggleDiv('source');">
                    <span id="pagegen" style="color: #1F1F1F">&nbsp;Page generated in <span id="timespent" style="color: #1F1F1F"><? $time_end = microtime(true); $time_spent = $time_end - $time_start; printf( "%7.3f", $time_spent); ?></span> seconds.</span>
                    </a>
                </td>
            </tr>
        </table>
        <table id="quote" border=0 cellspacing=0 cellpadding=0 width=80% align="center">
            <tr align="center">
                <td align="center">
                    <h3><? echo $i3quote->who; ?>&nbsp;said &quot;<font face="monospace"><i><? echo $i3quote->text; ?></i></font>&quot;</h3>
                </td>
            </tr>
        </table>
<? if($showSQL) { ?>
        <span id="sql" style="color: #1F1F1F"><?echo $pageSql;?></span>
<? } ?>
        <div id="source" style="display: none; vertical-align: bottom; background-color: white;"> <? echo numbered_source(__FILE__); ?> </div>
    </body>
    </head>
</html>
<? } elseif ($format == 'rss') {
    header('Content-type: application/rss+xml');
    echo "<?xml version=\"1.0\" ?>\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>I3 Feed</title>
        <description>
            This is the Intermud-3 network traffic feed, as seen by <? echo $MUD_NAME; ?>.
        </description>
        <link><? echo $RSS_FEED_URL; ?></link>
        <atom:link href="<? echo $RSS_FEED_URL; ?>" rel="self" type="application/rss+xml" />
        <?  foreach ($rss as $row) { ?>
            <item>
                <title><? echo '<![CDATA[' . $row["title"] . ']]>'; ?></title>
                <description><? echo '<![CDATA[' . $row["description"] . ']]>'; ?></description>
                <link><? echo $row["link"]; ?></link>
                <guid isPermaLink="false"><? echo $row["guid"]; ?></guid>
            </item>
        <? } ?>
    </channel>
</rss>
<? } elseif ($format == 'json') {
    //header('Content-type: application/json');
    header('Content-type: text/plain');
    echo json_encode($json);
?>
<? } else {
    header('Content-type: text/plain');

    echo str_pad("--=)) This is the Intermud-3 network traffic feed, as seen by " . $MUD_NAME . ". ((=--", 120, " ", STR_PAD_BOTH) . "\n\n";
    echo str_pad("Date", 10) . " ";
    echo str_pad("Time", 5) . " ";
    echo str_pad("Channel", 16) . " ";
    echo str_pad("Speaker", 24) . " ";
    echo "Message\n";
    echo str_repeat("-", 10) . " ";
    echo str_repeat("-", 5) . " ";
    echo str_repeat("-", 16) . " ";
    echo str_repeat("-", 24) . " ";
    echo str_repeat("-", 65) . "\n";
    foreach ($text as $row) {
        echo $row["datestamp"] . " " . $row["timestamp"] . " ";
        echo substr(str_pad("(" . $row["channel"] . ")", 16), 0, 16) . " ";
        echo substr(str_pad($row["speaker"]."@".$row["mud"], 24), 0, 24) . " ";
        echo wordwrap($row['message'], 65, "\n" . str_repeat(" ", 59)) . "\n";
    }
    $time_end = microtime(true);
    $time_spent = $time_end - $time_start;
    echo "\n" . str_pad("Last refreshed at $mini_now", 79) . " " . str_pad( sprintf( "Page generated in %7.3f seconds.", $time_spent), 40, " ", STR_PAD_LEFT);
?>
<? } ?>

