<?php

require_once "i3config.php";

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

try {
    $dbh = new PDO( $db_dsn, $db_user, $db_pwd, array( PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));
}
catch(PDOException $e) {
    echo $e->getMessage();
}

$video_list = get_video_list($dbh);
$play_list = get_play_list($dbh);
$video_pick = array_rand($play_list, 1);
$refresh_secs = $video_list[$video_pick]->video_len;

try {
    $upSql = "UPDATE videos SET plays = plays + 1 WHERE video_id = ?";
    $upQ = $dbh->prepare($upSql);
    $upQ->execute(array($video_pick));
}
catch(PDOException $e) {
    echo $e->getMessage();
}

$dbh = null;
$i = 0;

?>
<html>
    <head>
        <title> I3 Video playlist </title>
        <meta http-equiv="refresh" content="<? echo $refresh_secs; ?>">
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
            document.getElementById('len_<?echo $video_pick;?>').innerHTML=foo;
            counter--;
            if( counter >= <?echo $refresh_secs;?> - 5 ) {
                element_to_scroll_to = document.getElementById('<?echo $video_pick;?>');
                element_to_scroll_to.scrollIntoView(true);
            }
            t=setTimeout(function() { refreshBits(counter); }, 1 * 1000);
        }
        document.addEventListener('DOMContentLoaded', function() {
            element_to_scroll_to = document.getElementById('<?echo $video_pick;?>');
            element_to_scroll_to.scrollIntoView(true);
            refreshBits(<?echo $refresh_secs;?>);
        }, false);
        </script>
    </head>
    <body bgcolor="white" text="#303030" link="#00003f" vlink="#0080c0">
        <div id="youtube" style="display: none; position: fixed; z-index: -99; width: 100%; height: 100%">
            <iframe frameborder="0" height="100%" width="100%"
                    src="https://youtube.com/embed/<? echo $video_pick; ?>?autoplay=1&controls=0&showinfo=0&autohide=1">
            </iframe>
        </div>
        <table id="header" border=1 cellspacing=0 cellpadding=3 width=80% align="center">
            <tr>
                <th width="120px" align="left"> video_id </td>
                <th width="80px" align="center"> video_len </td>
                <th width="40px" align="center"> plays </td>
                <th align="left"> description </td>
            </tr>
<?  foreach ($video_list as $row) { ?>
<?      
        if($i % 2)  {
            if(isset($play_list[$row->video_id])) {
                $bg = "#ffffff";
            } else {
                $bg = "#aaaaaa";
            }
        } else {
            if(isset($play_list[$row->video_id])) {
                $bg = "#bbffbb";
            } else {
                $bg = "#66aa66";
            }
        }
        $i++;
        if($video_pick == $row->video_id) {
            $bg = "#ffbbbb";
        }
?>
            <tr id="<?echo $row->video_id;?>"bgcolor="<?echo $bg;?>">
                <td bgcolor="<?echo $bg;?>" width="120px" align="left"> <a href="https://www.youtube.com/watch?v=<?echo $row->video_id;?>"><?echo $row->video_id;?></a></td>
                <td id="len_<?echo $row->video_id;?>" bgcolor="<?echo $bg;?>" width="80px" align="center"> <?echo secs_to_hhmmss($row->video_len); ?> </td>
                <td bgcolor="<?echo $bg;?>" width="40px" align="center"> <?echo $row->plays; ?> </td>
                <td bgcolor="<?echo $bg;?>" align="left"> <?echo $row->description; ?> </td>
            </tr>
<? } ?>
        </table>
    </body>
</html>
