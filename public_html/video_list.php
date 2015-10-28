<?php

require_once "i3config.php";

function get_play_list($dbh) {
    $list = array();

    $vSql = "SELECT v.video_id, v.video_len, v.description, v.plays from videos v inner join ( select min(plays) as plays from videos ) q on v.plays = q.plays where not disabled";
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

function get_disabled_list($dbh) {
    $list = array();

    $vSql = "SELECT video_id, video_len, description, plays from videos where disabled order by plays asc, description";
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

function secs_to_hhmmss($secs) {
    $ss = $secs % 60;
    $secs -= $ss;
    $secs /= 60;
    $mm = $secs % 60;
    $secs -= $mm;
    $secs /= 60;
    $hh = $secs % 24;
    return sprintf("%02d:%02d:%02d", $hh, $mm, $ss);
}

function is_local_ip() {
    $visitor_ip = $_SERVER['REMOTE_ADDR'];
    $varr = explode(".", $visitor_ip);
    if($varr[0] == "192" && $varr[1] == "168")
        return 1;
    return 0;
}

try {
    $dbh = new PDO( $db_dsn, $db_user, $db_pwd, array( PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));
}
catch(PDOException $e) {
    echo $e->getMessage();
}

$video_list = get_video_list($dbh);
$play_list = get_play_list($dbh);
$disabled_list = get_disabled_list($dbh);
$video_pick = array_rand($play_list, 1);
$refresh_secs = $video_list[$video_pick]->video_len;
$video_count = count($video_list);
$play_count = count($play_list);

$isLocal = is_local_ip();

try {
    if( $isLocal ) {
        $upSql = "UPDATE videos SET plays = plays + 1 WHERE video_id = ?";
        $upQ = $dbh->prepare($upSql);
        $upQ->execute(array($video_pick));
    } else {
        $remote_ip = $_SERVER['REMOTE_ADDR'];
        $upSql = "UPDATE videos SET plays = plays + 1, last_viewer = ? WHERE video_id = ?";
        $upQ = $dbh->prepare($upSql);
        $upQ->execute(array($remote_ip, $video_pick));
    }
}
catch(PDOException $e) {
    echo $e->getMessage();
}

$dbh = null;
$i = 0;

$graphics['background']         = $isLocal ? "gfx/dark_wood.jpg"                : "https://lh5.googleusercontent.com/-zvnNrcuqbco/UdooZZelxoI/AAAAAAAAALA/9u5S92UySEA/s800/dark_wood.jpg";

header('Content-Type:text/html; charset=UTF-8');

?>
<html>
    <head>
        <title> I3 Video playlist </title>
        <meta http-equiv="refresh" content="<?php echo $refresh_secs; ?>">
        <script type="text/javascript">
        function zeroPad(num, places) {
            var zero = places - num.toString().length + 1;
            return Array(+(zero > 0 && zero)).join("0") + num;
        }
        function refreshBits(counter)
        {
            var secs = counter;
            var ss = secs % 60;
            secs -= ss;
            secs /= 60;
            var mm = secs % 60;
            secs -= mm;
            secs /= 60;
            var hh = secs % 24;
            var foo = zeroPad(hh,2) + ':' + zeroPad(mm,2) + ':' + zeroPad(ss,2);
            document.getElementById('len_<?php echo $video_pick;?>').innerHTML=foo;
            counter--;
            if( counter >= <?php echo $refresh_secs;?> - 5 ) {
                element_to_scroll_to = document.getElementById('<?php echo $video_pick;?>');
                element_to_scroll_to.scrollIntoView(true);
            }
            t=setTimeout(function() { refreshBits(counter); }, 1 * 1000);
        }
        document.addEventListener('DOMContentLoaded', function() {
            element_to_scroll_to = document.getElementById('<?php echo $video_pick;?>');
            element_to_scroll_to.scrollIntoView(true);
            refreshBits(<?php echo $refresh_secs;?>);
        }, false);
        </script>
    </head>
    <body background="<?php echo $graphics['background']; ?>" bgcolor="#000000" text="#d0d0d0" link="#ffffbf" vlink="#ffa040" style="overflow-x: hidden;">
<!--
    <body bgcolor="black" text="#d0d0d0" link="#ffffbf" vlink="#ffa040">
    <body bgcolor="white" text="#303030" link="#00003f" vlink="#0080c0">
        <div id="youtube" style="display: none; position: fixed; z-index: -99; width: 100%; height: 100%">
            <iframe frameborder="0" height="100%" width="100%"
                    src="https://youtube.com/embed/<?php echo $video_pick; ?>?autoplay=1&controls=0&showinfo=0&autohide=1">
            </iframe>
        </div>
-->
        <div align="center">
            <font color="#ffff00">
                <h3> Playing one of <?php echo $play_count; ?> picks from <?php echo $video_count; ?> total videos.</h3>
            </font>
        </div>
        <table id="content" border=1 cellspacing=0 cellpadding=3 width=80% align="center">
            <tr bgcolor="#222222">
                <th bgcolor="#222222" width="120px" align="left"> video_id </td>
                <th bgcolor="#222222" width="80px" align="center"> video_len </td>
                <th bgcolor="#222222" width="40px" align="center"> plays </td>
                <th bgcolor="#222222" align="left"> description </td>
            </tr>
<?php  foreach ($video_list as $row) { ?>
<?php      
        if($i % 2)  {
            if(isset($disabled_list[$row->video_id])) {
                $bg = "#FF0000";
            } elseif(isset($play_list[$row->video_id])) {
                $bg = "#222222";
                //$bg = "#ffffff";
            } else {
                $bg = "#000000";
                //$bg = "#aaaaaa";
            }
        } else {
            if(isset($disabled_list[$row->video_id])) {
                $bg = "#FF0000";
            } elseif(isset($play_list[$row->video_id])) {
                $bg = "#224422";
                //$bg = "#bbffbb";
            } else {
                $bg = "#002200";
                //$bg = "#66aa66";
            }
        }
        $i++;
        if($video_pick == $row->video_id) {
            $bg = "#220000";
        }
?>
            <tr id="<?php echo $row->video_id;?>"bgcolor="<?php echo $bg;?>">
                <td bgcolor="<?php echo $bg;?>" width="120px" valign="top" align="left"> <a href="https://www.youtube.com/watch?v=<?php echo $row->video_id;?>"><?php echo $row->video_id;?></a></td>
                <td id="len_<?php echo $row->video_id;?>" bgcolor="<?php echo $bg;?>" width="80px" valign="top" align="center"> <?php echo secs_to_hhmmss($row->video_len); ?> </td>
                <td bgcolor="<?php echo $bg;?>" width="40px" valign="top" align="center"> <?php echo $row->plays; ?> </td>
                <td bgcolor="<?php echo $bg;?>" valign="top" align="left">
                    <?php echo $row->description; ?>
<?php      if($video_pick == $row->video_id) { ?>
                    <br />
                    <div id="youtube" style="display: block; z-index: -99; overflow-x: hidden; min-width: 640px; max-width: 1280px; min-height: 360px; max-height: 720px">
                        <iframe frameborder="0" scrolling="no" style="min-width: 640px; max-width: 1280px; min-height: 360px; max-height: 720px"
                                src="https://youtube.com/embed/<?php echo $video_pick; ?>?autoplay=1&controls=0&showinfo=0&autohide=1">
                        </iframe>
                    </div>
<?php      } ?>
                </td>
            </tr>
<?php } ?>
        </table>
        <div align="center">
            <font color="#ffff00">
                <h3> Playing one of <?php echo $play_count; ?> picks from <?php echo $video_count; ?> total videos.</h3>
            </font>
        </div>
    </body>
</html>
