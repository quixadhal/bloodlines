<?
$time_start = microtime(true);
$now = date('g:ia \o\n l, \t\h\e jS \o\f F, Y');

require_once "i3config_new.php";

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

function get_hour_colors($pinkfish) {
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

function get_channel_colors($pinkfish) {
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

function get_speaker_colors($pinkfish, $chatFileName) {
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
        try {
            global $DB_DSN, $DB_USER, $DB_PWD;
            //echo "SQL: $sql<br>\n";
            $dbh = new PDO( $DB_DSN, $DB_USER, $DB_PWD, array( PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));
            $sth = $dbh->query($sql);
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            while($row = $sth->fetch()) {
                $list[] = $row[$column];
            }
            $list = array_filter($list, "list_name_ok");
            file_put_contents($fileName, implode("\n", $list));
        }
        catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    return $list;
}

function quote_sql($str) {
    // Connect to PoOstgreSQL database
    try {
        global $DB_DSN, $DB_USER, $DB_PWD;
        $dbh = new PDO( $DB_DSN, $DB_USER, $DB_PWD, array( PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));
    }
    catch(PDOException $e) {
        echo $e->getMessage();
    }
    return $dbh->quote($str);
}

function parse_args() {
    // Input
    global $DEFAULT_PAGE_SIZE;
    global $DEFAULT_PAGE_NUMBER;
    global $DEFAULT_FORMAT;

    // Output
    global $pageSize, $pageNumber, $pageNumberEntered;
    global $channelFilter, $channelSql, $channelList;
    global $speakerFilter, $speakerSql, $speakerList;
    global $mudFilter, $mudSql, $mudList;
    global $searchFilter, $searchSql;
    global $linksOnly, $linkSql;
    global $showBots, $botSql;
    global $format;
    global $anchorID, $oldPageNumber;

    // Page size (IE: LIMIT clause)
    //
    // This is how many results per page we're using.
    // It may be adjustable later.
    if( isset($_REQUEST) && isset($_REQUEST["ps"]) ) {
        $pageSize = $_REQUEST["ps"];
        if(!is_numeric($pageSize)) {
            $pageSize = $DEFAULT_PAGE_SIZE;
        }
        if( $pageSize < 1 ) {
            $pageSize = 1;
        }
    }

    // Page number (IE: OFFSET clause)
    //
    // Once we know the result set size, we need to limit this.
    // 0 means the END of the data set, 1..N means totalpages - x
    // This is so that page 0 will always be "current"
    if( isset($_REQUEST) && isset($_REQUEST["pn"]) ) {
        $pageNumber = $_REQUEST["pn"];
        if(!is_numeric($pageNumber)) {
            $pageNumber = $DEFAULT_PAGE_NUMBER;
        }
    }

    // Page display
    //
    // This is the page  number the user entered in the goto page field.
    // It is effectively $totalPages - <entered value>, so the users
    // can think in terms of page 0 being the start of the data set.
    if( isset($_REQUEST) && isset($_REQUEST["pe"]) ) {
        $pageNumberEntered = $_REQUEST["pe"];
        if(!is_numeric($pageNumberEntered)) {
            $pageNumberEntered = $DEFAULT_PAGE_NUMBER;
        }
    }

    // Channel Filter (only display these channels)
    // Accept a comma-seperated list of names
    $channelSql = '';
    if( isset($_REQUEST) && isset($_REQUEST["cf"]) ) {
        $channelFilter = $_REQUEST["cf"];
        $words = array();
        foreach (explode(",", $channelFilter) as $word) {
            $word = trim($word);
            if( array_key_exists( $word, $channelList )) {
                $words[] = quote_sql(strtolower($word));
            }
        }
        $channelSql = "AND lower(channel) IN ( " . implode(",", $words) . " )";
    }
 
    // Speaker filter (only display these speakers)
    // Accept a comma-seperated list of names
    $speakerSql = '';
    if( isset($_REQUEST) && isset($_REQUEST["sf"]) ) {
        $speakerFilter = $_REQUEST["sf"];
        $words = array();
        foreach (explode(",", $speakerFilter) as $word) {
            $word = trim($word);
            if( array_key_exists( $word, $speakerList )) {
                $words[] = quote(strtolower($word));
            }
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
            $word = trim($word);
            if( array_key_exists( $word, $mudList )) {
                $words[] = quote(strtolower($word));
            }
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
                $words[] = preg_replace('/\*/', '%', quote("$word"));
            } else {
                $words[] = preg_replace('/\*/', '%', quote("* $word *"));
                $words[] = preg_replace('/\*/', '%', quote("$word *"));
                $words[] = preg_replace('/\*/', '%', quote("* $word"));
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

    // Data about last position, for recalculating offsets if the criteria changed
    if( isset($_REQUEST) && isset($_REQUEST["an"]) ) {
        $anchorID = $_REQUEST["an"];
        if(!is_numeric($anchorID)) {
            $anchorID = null;
        }
        if( $anchorID < 0 ) {
            $anchorID = null;
        }
    }
    if( isset($_REQUEST) && isset($_REQUEST["op"]) ) {
        $oldPageNumber = $_REQUEST["op"];
        if(!is_numeric($oldPageNumber)) {
            $oldPageNumber = $DEFAULT_PAGE_NUMBER;
        }
    }
}

function get_row_count($sql) {
    // Connect to PoOstgreSQL database
    try {
        global $DB_DSN, $DB_USER, $DB_PWD;
        $dbh = new PDO( $DB_DSN, $DB_USER, $DB_PWD, array( PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));
        $sth = $dbh->query($sql);
        return $sth->fetchColumn();
    }
    catch(PDOException $e) {
        echo $e->getMessage();
    }
    return 0;
}

function get_row_data($sql, $reverse) {
    $data = array ();

    // Connect to PoOstgreSQL database
    try {
        global $DB_DSN, $DB_USER, $DB_PWD;
        $dbh = new PDO( $DB_DSN, $DB_USER, $DB_PWD, array( PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));
        $sth = $dbh->query($sql);

        $data['page'] = $pageNumber;
        $data['total'] = $totalRows;
        $data['rows'] = array();
        $sth->setFetchMode(PDO::FETCH_OBJ);
        while($row = $sth->fetch()) {
            $data['rows'][] = $row;
        }
        if($reverse) {
            $data['rows'] = array_reverse($data['rows']);
        }
    }
    catch(PDOException $e) {
        echo $e->getMessage();
    }
    return $data;
}

function figure_page_offsets() {
    // Input
    global $FEW_PAGES, $MANY_PAGES;
    global $totalPages, $pageNumber;

    // Output
    global $beginningPage, $backManyPage, $backFewPage, $backOnePage;
    global $forwardOnePage, $forwardFewPage, $forwardManyPage, $endPage;
    global $beginningPageDisplay, $backManyPageDisplay, $backFewPageDisplay, $backOnePageDisplay;
    global $pageNumberDisplay;
    global $forwardOnePageDisplay, $forwardFewPageDisplay, $forwardManyPageDisplay, $endPageDisplay;

    $beginningPage = $totalPages;
    $backManyPage = $pageNumber + $MANY_PAGES;
    $backFewPage = $pageNumber + $FEW_PAGES;
    $backOnePage = $pageNumber + 1;

    $forwardOnePage = $pageNumber - 1;
    $forwardFewPage = $pageNumber - $FEW_PAGES;
    $forwardManyPage = $pageNumber - $MANY_PAGES;
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
}

function url_params( $fmt, $bots, $links ) {
    // Input
    global $DEFAULT_PAGE_SIZE;
    global $DEFAULT_FORMAT;
    global $pageSize, $pageNumber;
    global $channelFilter, $speakerFilter, $mudFilter, $searchFilter;
    global $anchorID, $oldPageNumber;

    $params = ($pageSize != $DEFAULT_PAGE_SIZE ? "&ps=" . urlencode($pageSize) : "")
        . (isset($links) ? "&lo" : "")
        . (isset($bots) ? "&sb" : "")
        . ($fmt != $DEFAULT_FORMAT ? "&fm=" . urlencode($fmt) : "")
        . (isset($channelFilter) ? "&cf=" . urlencode($channelFilter) : "")
        . (isset($speakerFilter) ? "&sf=" . urlencode($speakerFilter) : "")
        . (isset($mudFilter) ? "&mf=" . urlencode($mudFilter) : "")
        . (isset($searchFilter) ? "&sr=" . urlencode($searchFilter) : "")
        . (isset($anchorID) ? "&an=" . urlencode($anchorID) : "");

    return $params;
}

function build_url( $fmt, $bots, $links ) {
    // Input
    global $pageNumber;

    return $_SERVER["PHP_SELF"] . "?pn=" . urlencode($pageNumber) . url_params( $fmt, $bots, $links );
}

function construct_nav_urls() {
    // Input
    global $format;
    global $beginningPage, $backManyPage, $backFewPage, $backOnePage;
    global $forwardOnePage, $forwardFewPage, $forwardManyPage, $endPage;

    // Output
    global $beginningUrl, $backManyUrl, $backFewUrl, $backOneUrl;
    global $pageUrl;
    global $forwardOneUrl, $forwardFewUrl, $forwardManyUrl, $endUrl;
    global $textUrl, $rssUrl, $jsonUrl;

    $urlParams - url_params($format);
    $pageUrl = build_url($format);
    $beginningUrl = $_SERVER["PHP_SELF"] . "?pn=" . urlencode($beginningPage) . $urlParams;
    $backOneUrl = $_SERVER["PHP_SELF"] . "?pn=" . urlencode($backOnePage) . $urlParams;
    $backFewUrl = $_SERVER["PHP_SELF"] . "?pn=" . urlencode($backFewPage) . $urlParams;
    $backManyUrl = $_SERVER["PHP_SELF"] . "?pn=" . urlencode($backManyPage) . $urlParams;
    $forwardOneUrl = $_SERVER["PHP_SELF"] . "?pn=" . urlencode($forwardOnePage) . $urlParams;
    $forwardFewUrl = $_SERVER["PHP_SELF"] . "?pn=" . urlencode($forwardFewPage) . $urlParams;
    $forwardManyUrl = $_SERVER["PHP_SELF"] . "?pn=" . urlencode($forwardManyPage) . $urlParams;
    $endUrl = $_SERVER["PHP_SELF"] . "?pn=" . urlencode($endPage) . $urlParams;

    $textUrl = build_url("text");
    $rssUrl = build_url("rss");
    $jsonUrl = build_url("json");
}

function construct_output() {
    // Input
    global $RSS_FEED_URL;
    global $hourColors, $channelColors, $speakerColors;

    // Output
    global $data;
    global $html, $text, $rss, $json;

    $html = array();
    $text = array();
    $rss = array();
    $json = array();
    $bg = 0;

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

        $speakerColor = $speakerColors[strtolower($row->speaker)];
        $speaker = "$speakerColor" . $row->speaker . "@" . $row->mud . "</SPAN>";

        $filtered_message = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '_', $row->message);
        $message = htmlentities($filtered_message);
        $message = preg_replace( '/((?:http|https|ftp)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(?::[a-zA-Z0-9]*)?\/?(?:[a-zA-Z0-9\-\._\?\,\'\/\\\+&amp;%\$#\=~])*)/', '<a href="$1" target="I3-link">$1</a>', $message);

        $html[] = array(
            "row"           => $row,
            "bgcolor"       => $bgColor,
            "bgbold"        => $bgBold,
            "datestamp"     => $datestamp,
            "timestamp"     => $timestamp,
            "channel"       => $channel,
            "speaker"       => $speaker,
            "message"       => $message,
            "raw_channel"   => $row->channel,
            "raw_speaker"   => $row->speaker,
            "raw_mud"       => $row->mud,
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
}

function calculate_offset() {
    global $pageSize, $pageNumber, $pageNumberEntered;
    global $totalRows, $totalPages;
    global $sortSql, $limitSql, $offsetSql;
    global $reverseSort;

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
    $offsetSql = "OFFSET $offset";
    $limitSql = "LIMIT $pageSize";
}

$pageSize       = $DEFAULT_PAGE_SIZE;
$pageNumber     = $DEFAULT_PAGE_NUMBER;
$format         = $DEFAULT_FORMAT;

$pinkfish_map   = get_pinkfish_map();
$hourColors     = get_hour_colors($pinkfish_map);
$channelColors  = get_channel_colors($pinkfish_map);
$speakerColors  = get_speaker_colors($pinkfish_map, $CHAT_COLOR_FILE);

$channelList    = get_cache( $CHANNEL_CACHE_FILE, "SELECT DISTINCT channel, COUNT(*) FROM chanlogs GROUP BY channel HAVING COUNT(*) > 100 ORDER BY channel ASC, COUNT(*) ASC", "channel" );
$speakerList    = get_cache( $SPEAKER_CACHE_FILE, "SELECT DISTINCT speaker, COUNT(*) FROM chanlogs GROUP BY speaker HAVING COUNT(*) > 100 ORDER BY speaker ASC, COUNT(*) ASC", "speaker" );
$mudList        = get_cache( $MUD_CACHE_FILE, "SELECT DISTINCT mud, COUNT(*) FROM chanlogs GROUP BY mud HAVING COUNT(*) > 100 ORDER BY mud ASC, COUNT(*) ASC", "mud" );

parse_args();

$totalRows = get_row_count( preg_replace( '/chanlogs\s+AND/', 'chanlogs WHERE', "SELECT COUNT(*) FROM chanlogs $botSql $linkSql $channelSql $mudSql $speakerSql $searchSql" ));
$totalPages = ceil($totalRows / $pageSize);

calculate_offset();

$pageSql = preg_replace( '/chanlogs\s+AND/', 'chanlogs WHERE', "SELECT id, msg_date, to_char(msg_date, 'MM/DD') AS the_date, to_char(msg_date, 'HH24:MI') AS the_time, to_char(msg_date, 'HH24') AS the_hour, channel, speaker, mud, message FROM chanlogs $botSql $linkSql $channelSql $mudSql $speakerSql $searchSql $sortSql $offsetSql $limitSql" );
$data = get_row_data( $pageSql, $reverseSort );

$anchorID = $data['rows'][0]->id;

figure_page_offsets();
construct_nav_urls();
build_url($format);
construct_output();

if($format == 'html') {
    header('Content-type: text/html');
?>
<html>
    <head>
        <title> Intermud-3 network traffic, as seen by <? echo $MUD_NAME; ?>. </title>
        <meta http-equiv="refresh" content="60">
        <style>
            a { text-decoration:none; }
            a:hover { text-decoration:underline; }
        </style>
        <script src="jq/js/jquery-1.9.1.js"></script>
        <script src="jq/js/jquery-ui-1.10.1.custom.js"></script>
    </head>
    <body bgcolor="black" text="#d0d0d0" link="#ffffbf" vlink="#ffa040">
        <table id="header" border=0 cellspacing=0 cellpadding=0 width=80% align="center">
            <tr>
                <!-- Header logos -->
                <td align="right" valign="bottom"
                    style="opacity: 0.7; filter: alpha(opacity=70);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.7'; this.style.filter='alpha(opacity=70';"
                >
                    <table id="logo" border=0 cellspacing=0 cellpadding=0 width=100% align="center">
                        <tr>
                            <td align="right" valign="top">
                                <!-- <a href="/anyterm/anyterm.shtml?rows=40&cols=100"> -->
                                <a href="/~bloodlines">
                                    <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/bloodlines.png" border=0 width=234 height=80>
                                </a>
                            </td>
                            <td align="left" valign="bottom">
                                <!-- <a href="/anyterm/anyterm.shtml?rows=40&cols=100"> -->
                                <a href="/~bloodlines">
                                    <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/wileymud4.png" border=0 width=177 height=40">
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
                <!-- Header logos END -->
                <!-- Search form -->
                <td align="left" valign="bottom">
                    <form action="" method="get">
                        <table id="searchform" border=0 cellspacing=0 cellpadding=0 width=100% align="center">
                            <tr>
                                <td align="right" valign="bottom">
                                    <span style="color: #1F1F1F;">
                                        <label id="srlabel" for="sr"
                                            onmouseover="srinput.style.color='#FFFF00'; srinput.style.backgroundColor='#1F1F1F'; srlabel.style.color='#FFFFFF';"
                                            onmouseout="srinput.style.color='#4F4F00'; srinput.style.backgroundColor='#000000'; srlabel.style.color='#1F1F1F';"
                                            onfocus="srinput.focus();"
                                            onclick="srinput.focus();"
                                        > Search:&nbsp; </label>
                                    </span>
                                </td>
                                <td bgcolor="#000000" width="200" align="left" valign="bottom">
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
                                    <? if(isset($speakerFilter)) { ?>
                                        <input type="hidden" name="sf" value="<? echo $speakerFilter; ?>">
                                    <? } ?>
                                    <? if(isset($mudFilter)) { ?>
                                        <input type="hidden" name="mf" value="<? echo $mudFilter; ?>">
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
                    style="opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <span style="color: #555555">
                    <? if( $pageNumber < $beginningPage) { ?>
                        <a href="<? echo $beginningUrl; ?>" title="The&nbsp;Beginning&nbsp;(<? echo $beginningPageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navbegin.png" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navbegin.png" border=0 width=48 height=48 />
                    <? } ?>
                    </span>
                </td>
                <td id="navbackmany" align="left" valign="center" width="50"
                    <? if( $pageNumber < $backManyPage && $backManyPage <= $beginningPage) { ?>
                    style="opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <span style="color: #555555">
                    <? if( $pageNumber < $backManyPage && $backManyPage <= $beginningPage) { ?>
                        <a href="<? echo $backManyUrl; ?>" title="Back&nbsp;<? echo $MANY_PAGES; ?>&nbsp;(<? echo $backManyPageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navback.png" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navback.png" border=0 width=48 height=48 />
                    <? } ?>
                    </span>
                </td>
                <td id="navbackfew" align="left" valign="center" width="50"
                    <? if( $pageNumber < $backFewPage && $backFewPage <= $beginningPage) { ?>
                    style="opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <span style="color: #555555">
                    <? if( $pageNumber < $backFewPage && $backFewPage <= $beginningPage) { ?>
                        <a href="<? echo $backFewUrl; ?>" title="Back&nbsp;<? echo $FEW_PAGES; ?>&nbsp;(<? echo $backFewPageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navback.png" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navback.png" border=0 width=48 height=48 />
                    <? } ?>
                    </span>
                </td>
                <td id="navprev" align="left" valign="center" width="50"
                    <? if( $pageNumber < $backOnePage && $backOnePage <= $beginningPage) { ?>
                    style="opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <span style="color: #555555">
                    <? if( $pageNumber < $backOnePage && $backOnePage <= $beginningPage) { ?>
                        <a href="<? echo $backOneUrl; ?>" title="Previous&nbsp;Page&nbsp;(<? echo $backOnePageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navprev.png" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navprev.png" border=0 width=48 height=48 />
                    <? } ?>
                    </span>
                </td>
                <td>
                &nbsp;
                </td>
                <td id="fakepagenumber" align="center" valign="center" width="160">
                &nbsp;
                </td>
                <td>
                    &nbsp;
                </td>
                <td id="navbot" align="center" valign="center" width="50"
                    <? if( isset($showBots) ) { ?>
                    style="opacity: 1.0; filter: alpha(opacity=100);"
                    onmouseover="this.style.opacity='0.2'; this.style.filter='alpha(opacity=20';"
                    onmouseout="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    <? } else { ?>
                    style="opacity: 0.2; filter: alpha(opacity=20);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.2'; this.style.filter='alpha(opacity=20';"
                    <? } ?>
                >
                    <? if( !isset($showBots) ) { ?>
                    <a title='Include bot content' href="<? echo build_url($format, 1, $linksOnly); ?>">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navconfig.png" border=0 width=48 height=48 />
                    </a>
                    <? } else { ?>
                    <a title='Block messages from known bots' href="<? echo build_url($format, null, $linksOnly); ?>">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navconfig.png" border=0 width=48 height=48 />
                    </a>
                    <? } ?>
                </td>
                <td id="navlinks" align="center" valign="center" width="50"
                    <? if( isset($linksOnly) ) { ?>
                    style="opacity: 1.0; filter: alpha(opacity=100);"
                    onmouseover="this.style.opacity='0.2'; this.style.filter='alpha(opacity=20';"
                    onmouseout="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    <? } else { ?>
                    style="opacity: 0.2; filter: alpha(opacity=20);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.2'; this.style.filter='alpha(opacity=20';"
                    <? } ?>
                >
                    <? if( isset($linksOnly) ) { ?>
                    <a title='Include all content' href="<? echo build_url($format, $showBots, null); ?>">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navlinks.png" border=0 width=48 height=48 />
                    </a>
                    <? } else { ?>
                    <a title='Include only messages with URLs' href="<? echo build_url($format, $showBots, 1); ?>">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navlinks.png" border=0 width=48 height=48 />
                    </a>
                    <? } ?>
                </td>
                <td id="navhome" align="center" valign="center" width="50"
                    <? if( count( $_GET  ) > 0 ) { ?>
                    style="opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="opacity: 1.0; filter: alpha(opacity=100);"
                    <? } ?>
                >
                    <a title="HOME!" href="<? echo $_SERVER['PHP_SELF']; ?>">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navhome.png" border=0 width=48 height=48 />
                    </a>
                </td>
                <td>
                <td id="navpie" align="center" valign="center" width="50"
                    style="opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                >
                    <a title="Everyone loves PIE!" href="i3pie.html">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/pie_chart_zps670773a1.png" border=0 width=48 height=48 />
                    </a>
                </td>
                <td>
                &nbsp;
                </td>
                <td id="pagenumber" align="center" valign="center" width="150">
<!-- &nbsp; Page <? echo $pageNumberDisplay; ?> of <? echo $totalPages; ?> &nbsp; -->
                    <form action="" method="get">
                        <table id="pageform" border=0 cellspacing=0 cellpadding=0 width=100% align="center">
                            <tr>
                                <td align="right" valign="bottom" width="40">
                                    <span style="color: #1F1F1F;">
                                        <label id="pnlabel" for="pe"
                                            onmouseover="pninput.style.color='#FFFF00'; pninput.style.backgroundColor='#1F1F1F'; this.style.color='#FFFFFF'; pnoflabel.style.color='#FFFFFF';"
                                            onmouseout="pninput.style.color='#4F4F00'; pninput.style.backgroundColor='#000000'; this.style.color='#1F1F1F'; pnoflabel.style.color='#1F1F1F';"
                                            onfocus="pninput.focus();"
                                            onclick="pninput.focus();"
                                        > Page:&nbsp; </label>
                                    </span>
                                </td>
                                <td bgcolor="#000000" width="50" align="left" valign="bottom">
                                    <input id="pninput" type="text" style="background-color: #000000; color: #4F4F00; border: 1px; border-color: #000000; border-style: solid; width: 50px;"
                                        onmouseover="this.style.color='#FFFF00'; this.style.backgroundColor='#1F1F1F'; pnlabel.style.color='#FFFFFF'; pnoflabel.style.color='#FFFFFF';"
                                        onfocus="this.style.color='#FFFF00'; this.style.backgroundColor='#1F1F1F'; pnlabel.style.color='#FFFFFF';  pnoflabel.style.color='#FFFFFF'; /* if(!this._haschanged){this.value=''};this._haschanged=true; */"
                                        onblur="this.style.color='#4F4F00'; this.style.backgroundColor='#000000'; pnlabel.style.color='#1F1F1F'; pnoflabel.style.color='#1F1F1F';"
                                        onmouseout="this.style.color='#4F4F00'; this.style.backgroundColor='#000000'; pnlabel.style.color='#1F1F1F'; pnoflabel.style.color='#1F1F1F';"
                                        maxlength="6" name="pe" value="<? if(isset($pageNumberDisplay)) echo $pageNumberDisplay; ?>" />
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
                                    <? if(isset($speakerFilter)) { ?>
                                        <input type="hidden" name="sf" value="<? echo $speakerFilter; ?>">
                                    <? } ?>
                                    <? if(isset($mudFilter)) { ?>
                                        <input type="hidden" name="mf" value="<? echo $mudFilter; ?>">
                                    <? } ?>
                                    <? if(isset($searchFilter)) { ?>
                                        <input type="hidden" name="sr" value="<? echo $searchFilter; ?>">
                                    <? } ?>
                                    <? if(isset($anchorID)) { ?>
                                        <input type="hidden" name="an" value="<? echo $anchorID; ?>">
                                    <? } ?>
                                </td>
                                <td align="right" valign="bottom" width="50">
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
                    style="opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <? if( $pageNumber > $forwardOnePage && $forwardOnePage >= $endPage) { ?>
                        <a href="<? echo $forwardOneUrl; ?>" title="Next&nbsp;Page&nbsp;(<? echo $forwardOnePageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navnext.png" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navnext.png" border=0 width=48 height=48 />
                    <? } ?>
                </td>
                <td id="navforwardfew" align="right" valign="center" width="50"
                    <? if( $pageNumber > $forwardFewPage && $forwardFewPage >= $endPage) { ?>
                    style="opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <? if( $pageNumber > $forwardFewPage && $forwardFewPage >= $endPage) { ?>
                        <a href="<? echo $forwardFewUrl; ?>" title="Next&nbsp;<? echo $FEW_PAGES; ?>&nbsp;(<? echo $forwardFewPageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navforward.png" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navforward.png" border=0 width=48 height=48 />
                    <? } ?>
                </td>
                <td id="navforwardmany" align="right" valign="center" width="50"
                    <? if( $pageNumber > $forwardManyPage && $forwardManyPage >= $endPage) { ?>
                    style="opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <? if( $pageNumber > $forwardManyPage && $forwardManyPage >= $endPage) { ?>
                        <a href="<? echo $forwardManyUrl; ?>" title="Next&nbsp;<? echo $MANY_PAGES; ?>&nbsp;(<? echo $forwardManyPageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navforward.png" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navforward.png" border=0 width=48 height=48 />
                    <? } ?>
                </td>
                <td id="navend" align="right" valign="center" width="50"
                    <? if( $pageNumber > $endPage) { ?>
                    style="opacity: 0.4; filter: alpha(opacity=40);"
                    onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                    onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                    <? } else { ?>
                    style="opacity: 0.2; filter: alpha(opacity=20);"
                    <? } ?>
                >
                    <? if( $pageNumber > $endPage) { ?>
                        <a href="<? echo $endUrl; ?>" title="Current&nbsp;Time&nbsp;(<? echo $endPageDisplay; ?>&nbsp;of&nbsp;<? echo $totalPages; ?>)">
                            <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navend.png" border=0 width=48 height=48 />
                        </a>
                    <? } else { ?>
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/navend.png" border=0 width=48 height=48 />
                    <? } ?>
                </td>
            </tr>
        </table>
        <table id="content" width="100%">
            <tr>
                <th align="left" width="5%" style="color: #DDDDDD;">Date</th>
                <th align="left" width="5%" style="color: #DDDDDD;">Time</th>
                <? if(isset($channelFilter)) { ?>
                <th id="channelheader" align="left" width="10%" style="color: #FFFF00;">
                    <a href="<? $old = $channelFilter; $channelFilter = null; echo build_url($format, $showBots, $linksOnly); $channelFilter = $old; build_url($format, $showBots, $linksOnly); ?>">Channel</a>
                </th>
                <? } else { ?>
                <th id="channelheader" align="left" width="10%" style="color: #DDDDDD;">Channel</th>
                <? } ?>
                <? if(isset($speakerFilter)) { ?>
                <th id="speakerheader" align="left" width="20%" style="color: #FFFF00;">
                    <a href="<? $old = $speakerFilter; $speakerFilter = null; echo build_url($format, $showBots, $linksOnly); $speakerFilter = $old; build_url($format, $showBots, $linksOnly); ?>">Speaker</a>
                </th>
                <? } else { ?>
                <th id="speakerheader" align="left" width="20%" style="color: #DDDDDD;">Speaker</th>
                <? } ?>
                <th align="left" width="60%">&nbsp;</th>
            </tr>
            <?  foreach ($html as $row) {
                    if(isset($channelFilter)) {
                        $channels = array_unique(array_merge( explode(",", $channelFilter), array($row["raw_channel"])));
                        $old = $channelFilter;
                        $channelFilter = implode(",", $channels);
                        $channelUrl = build_url($format, $showBots, $linksOnly);
                        $channelFilter = $old;
                    } else {
                        $channelFilter = $row["raw_channel"];
                        $channelUrl = build_url($format, $showBots, $linksOnly);
                        $channelFilter = null;
                    }
                    if(isset($speakerFilter)) {
                        $speakers = array_unique(array_merge( explode(",", $speakerFilter), array($row["raw_speaker"])));
                        $muds = array_unique(array_merge( explode(",", $mudFilter), array($row["raw_mud"])));
                        $old = $speakerFilter;
                        $old2 = $mudFilter;
                        $speakerFilter = implode(",", $speakers);
                        $mudFilter = implode(",", $muds);
                        $speakerUrl = build_url($format, $showBots, $linksOnly);
                        $speakerFilter = $old;
                        $mudFilter = $old2;
                    } else {
                        $speakerFilter = $row["raw_speaker"];
                        $mudFilter = $row["raw_mud"];
                        $speakerUrl = build_url($format, $showBots, $linksOnly);
                        $speakerFilter = null;
                        $mudFilter = null;
                    }
             ?>
            <tr>
                <td bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['datestamp']; ?></td>
                <td bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['timestamp']; ?></td>
                <td onmouseover="this.style.backgroundColor = '<? echo $row['bgbold']; ?>';"
                    onmouseout="this.style.backgroundColor = '<? echo $row['bgcolor']; ?>';"
                    onclick="document.location.href='<? echo $channelUrl; ?>';" bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['channel']; ?></td>
                <td onmouseover="this.style.backgroundColor = '<? echo $row['bgbold']; ?>';"
                    onmouseout="this.style.backgroundColor = '<? echo $row['bgcolor']; ?>';"
                    onclick="document.location.href='<? echo $speakerUrl; ?>';" bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['speaker']; ?></td>
                <td bgcolor="<? echo $row['bgcolor']; ?>"><? echo $row['message']; ?></td>
            </tr>
            <? } ?>
        </table>
        <table width="100%">
            <tr>
                <td align="left" width="50%" onmouseover="lastrefresh.style.color='#FFFF00'; pagegen.style.color='#00FF00'; timespent.style.color='#FF0000';" onmouseout="lastrefresh.style.color='#1F1F1F'; pagegen.style.color='#1F1F1F'; timespent.style.color='#1F1F1F';">
                    <span id="lastrefresh" style="color: #1F1F1F">Last refreshed at <? echo $now; ?>.&nbsp;</span>
                </td>
                <td>&nbsp;</td>
                <td align="center" width="71" onmouseover="lastrefresh.style.color='#FFFF00'; pagegen.style.color='#00FF00'; timespent.style.color='#FF0000';" onmouseout="lastrefresh.style.color='#1F1F1F'; pagegen.style.color='#1F1F1F'; timespent.style.color='#1F1F1F';">
                    <span style="color: #1F1F1F"><a href="<? echo $rssUrl; ?>">
                        <img onmouseover="this.src='http://i302.photobucket.com/albums/nn96/quixadhal/rssMouseOver_zps52b86e27.png';" onmouseout="this.src='http://i302.photobucket.com/albums/nn96/quixadhal/rss_zps6b73d7e2.png';" id="rssimg" src="http://i302.photobucket.com/albums/nn96/quixadhal/rss_zps6b73d7e2.png" border=0 width=71 height=55 alt="(RSS)" />
                    </a></span>
                </td>
                <td align="center" width="75" onmouseover="lastrefresh.style.color='#FFFF00'; pagegen.style.color='#00FF00'; timespent.style.color='#FF0000'; jsonimg.src='gfx/json.png';" onmouseout="lastrefresh.style.color='#1F1F1F'; pagegen.style.color='#1F1F1F'; timespent.style.color='#1F1F1F'; jsonimg.src='gfx/jsonMouseOver.png';">
                    <span style="color: #1F1F1F"><a href="<? echo $jsonUrl; ?>">
                        <img onmouseover="this.src='http://i302.photobucket.com/albums/nn96/quixadhal/jsonMouseOver_zps46d5148d.png';" onmouseout="this.src='http://i302.photobucket.com/albums/nn96/quixadhal/json_zps34e3c065.png';" id="jsonimg" src="http://i302.photobucket.com/albums/nn96/quixadhal/json_zps34e3c065.png" border=0 width=75 height=51 alt="(JSON)" />
                    </a></span>
                </td>
                <td align="center" width="84" onmouseover="lastrefresh.style.color='#FFFF00'; pagegen.style.color='#00FF00'; timespent.style.color='#FF0000';" onmouseout="lastrefresh.style.color='#1F1F1F'; pagegen.style.color='#1F1F1F'; timespent.style.color='#1F1F1F';">
                    <span style="color: #1F1F1F"><a href="<? echo $textUrl; ?>">
                        <img onmouseover="this.src='http://i302.photobucket.com/albums/nn96/quixadhal/textMouseOver_zpsc7cbdd88.png';" onmouseout="this.src='http://i302.photobucket.com/albums/nn96/quixadhal/text_zps49ecc982.png';" id="textimg" src="http://i302.photobucket.com/albums/nn96/quixadhal/text_zps49ecc982.png" border=0 width=84 height=79 alt="(TEXT)" />
                    </a></span>
                </td>
                <td>&nbsp;</td>
                <td align="right" width="30%" onmouseover="lastrefresh.style.color='#FFFF00'; pagegen.style.color='#00FF00'; timespent.style.color='#FF0000';" onmouseout="lastrefresh.style.color='#1F1F1F'; pagegen.style.color='#1F1F1F'; timespent.style.color='#1F1F1F';">
                    <span id="pagegen" style="color: #1F1F1F">&nbsp;Page generated in <span id="timespent" style="color: #1F1F1F"><? $time_end = microtime(true); $time_spent = $time_end - $time_start; printf( "%7.3f", $time_spent); ?></span> seconds.</span>
                </td>
            </tr>
        </table>
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
    echo "Date  Time  ";
    echo str_pad("Channel", 16) . " ";
    echo str_pad("Speaker", 24) . " ";
    echo "Message\n";
    echo "----- ----- ";
    echo str_repeat("-", 16) . " ";
    echo str_repeat("-", 24) . " ";
    echo str_repeat("-", 65) . "\n";
    foreach ($text as $row) {
        echo $row["datestamp"] . " " . $row["timestamp"] . " ";
        echo substr(str_pad("(" . $row["channel"] . ")", 16), 0, 16) . " ";
        echo substr(str_pad($row["speaker"]."@".$row["mud"], 24), 0, 24) . " ";
        echo wordwrap($row['message'], 65, "\n" . str_repeat(" ", 54)) . "\n";
    }
    $time_end = microtime(true);
    $time_spent = $time_end - $time_start;
    echo "\n" . str_pad("Last refreshed at $now", 79) . " " . str_pad( sprintf( "Page generated in %7.3f seconds.", $time_spent), 40, " ", STR_PAD_LEFT);
?>
<? } ?>

