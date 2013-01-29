<?php
$time_start = microtime(true);

//require_once "PHPTelnet.php";

//$telnet = new PHPTelnet();
//$now = date('g:ia \o\n l, \t\h\e jS \o\f F, Y');

$stuff = array (
    "Suffer! I will make you all suffer!!!!!",
    "Suffer!!!!!! ALL will Suffer Quixadhal's WRATH!!!",
    "Any good weapons for sale?",
    "I wish there were more victims, err... I mean players!",
    "Anyone want a red ring??",
    "SAVE, and Ye Shall Be SAVED!",
    "Zar!  Where are you Zar?",
    "Muidnar!  Stop teasing the mortals!",
    "Dirk is idle again!",
    "Sedna, stop trying to code me to steal from players!",
    "Damnit Quixadhal!  Stop snooping me!",
    "He's dead Jim.",
    "What?  Nobody's DIED recently???",
    "Is it me, or does everyone hear an echo.. echo...  echo....",
    "EEK!  Someone's been animating the chicken fajitas again!",
    "I shall HEAL you.... No, on second thought I won't.",
    "You there!  Stop loitering!",
    "Move along scum!  Bloody peasants!",
    "Must KILL for Dread Quixadhal...",
    "What's all this then?"
);

function mud_time_passed($current, $previous) {
    $SPMH = 75;
    $SPMD = $SPMH * 24;
    $SPMM = $SPMD * 35;
    $SPMY = $SPMM * 17;

    $difference = $current - $previous;
    $hours = ($difference / $SPMH) % 24;
    $difference -= $hours * $SPMH;
    $days = ($difference / $SPMD) % 35;
    $difference -= $days * $SPMD;
    $months = ($difference / $SPMM) % 17;
    $difference -= $months * $SPMM;
    $years = round($difference / $SPMY);
    return array( $years, $months, $days, $hours );
}

$cyric_time = mud_time_passed(time(), 744566400);
$cyric_years = $cyric_time[0];
$quix_time = mud_time_passed(845054400, 801590402);
$quix_years = $quix_time[0];

/*
$result = $telnet->Connect('wiley.shadowlord.org',3000,'blarghy','tardis');
if ($result == 0) {
    $telnet->DoCommand('', $result);
    echo $result;
    $telnet->DoCommand('1', $result);
    echo $result;
    $telnet->DoCommand('who', $result);
    echo $result;
    $telnet->DoCommand('quit', $result);
    echo $result;
    $telnet->DoCommand('0', $result);
    $telnet->Disconnect();
}
*/

?>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta http-equiv="refresh" content="300">
        <script type='text/javascript' src='clock/jquery.js'></script>
        <link href="http://fonts.googleapis.com/css?family=Orbitron" rel="stylesheet" type="text/css">
        <link href="clock/clock.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="clock/clock.js"></script>

        <script type="text/javascript">
        function startClock()
        {
            var today=new Date();
            var p="am";
            var h=today.getHours();
            var m=today.getMinutes();
            var s=today.getSeconds();
            var d=today.getDate();
            var M=today.getMonth();
            var y=today.getFullYear();
            var month=new Array(12);
            month[0]="January";
            month[1]="February";
            month[2]="March";
            month[3]="April";
            month[4]="May";
            month[5]="June";
            month[6]="July";
            month[7]="August";
            month[8]="September";
            month[9]="October";
            month[10]="November";
            month[11]="December";
            if( h > 11 ) {
                p = "pm";
                h = h - 12;
                if( h == 0 ) {
                    h = 12;
                }
            }
            m=zeroPad(m);
            s=zeroPad(s);
            document.getElementById('oldclock').innerHTML=h+":"+m+":"+s+" "+p;
            document.getElementById('olddate').innerHTML=month[M]+" "+d+", "+y;
            t=setTimeout('startClock()',500);
        }
        function zeroPad(i)
        {
            if (i<10)
              {
                  i="0" + i;
              }
            return i;
        }
        </script>
        <script language="JavaScript">
            Xmas_TargetDate = "12/25/2012 12:00 AM";
            Xmas_BackColor = "transparent";
            Xmas_ForeColor = "#00FF00";
            Xmas_CountActive = true;
            Xmas_CountStepper = -1;
            Xmas_LeadingZero = true;
            Xmas_DisplayFormat = "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
            Xmas_FinishMessage = "Ho! Ho! Ho!";
        </script>
        <script language="JavaScript">
            Myan_TargetDate = "12/21/2012 6:11:00 AM";
            Myan_BackColor = "transparent";
            Myan_ForeColor = "#FF0000";
            Myan_CountActive = true;
            Myan_CountStepper = -1;
            Myan_LeadingZero = true;
            Myan_DisplayFormat = "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
            Myan_FinishMessage = "Apocalypse!";
        </script>
        <title>
            Bloodlines:  WileyMUD IV
        </title>
        <link rel="shortcut icon" href="gfx/fire.ico" />
        <link rev="made" href="mailto:quixadhal@shadowlord.org">
        <style>
            a { text-decoration:none; }
            a:hover { text-decoration:underline; }
        </style>
    </head>
    <body background="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/dark_wood.jpg" bgcolor="#505050" text="#d0d0d0" link="#ffffbf" vlink="#ffa040" onload="startClock()">
        <table border=0 cellspacing=0 cellpadding=0 width=80% align="center">
            <tr>
                <td align="right" valign="top">
                    <a href="/anyterm/anyterm.shtml?rows=40&cols=100">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/bloodlines.png"
                             border=0 width=469 height=160
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                </td>
                <td align="left" valign="bottom">
                    <a href="/anyterm/anyterm.shtml?rows=40&cols=100">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/wileymud4.png"
                             border=0 width=354 height=81
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table border=0 cellspacing=0 cellpadding=0 width=100% align="center">
                        <tr>
                            <td align="right" valign="top"
                                style="opacity: 0.6; filter: alpha(opacity=60);"
                                onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                                onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                            > 
                                <font size="-2" color="#FFFFBF"><a href="/~wiley/">Also visit WileyMUD III, for amusement!</a></font>
                            </td>
                            <td align="right" valign="bottom" width="5%">
                                &nbsp;
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table border=0 cellspacing=0 cellpadding=0 width=100% align="center">
            <tr>
                <td align="center" width="180" valign="bottom">
                    <div id="clock">
                        <div class="clockGlass"></div>
                    </div>
                    <!-- <div id="olddate" align="center" style="color: #d0d000;"><?php echo date('F j, Y'); ?></div> -->
                    <a href="http://www.oldtimersguild.com/vb/forum.php">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/otg_logo_glow.png"
                             border=0 align="center" width=155 height=200
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                         />
                    </a>
                    <br />
                    <br />
                    <!-- <div id="oldclock" align="center" style="color: #d0d000;"><?php echo date('g:i:s a'); ?></div> -->
                    <span style="display: block !important; width: 120px; text-align: center; font-family: sans-serif; font-size: 12px;">
                        <a  href="http://www.wunderground.com/cgi-bin/findweather/getForecast?query=zmw:49004.2.99999&bannertypeclick=wu_blueglass">
                            <img    src="http://weathersticker.wunderground.com/weathersticker/cgi-bin/banner/ban/wxBanner?bannertype=wu_blueglass&airportcode=KAZO&ForcedCity=Kalamazoo&ForcedState=MI&zipcode=49004&language=EN"
                                    height="68" width="120" border="0"
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                            />
                        </a>
                    </span>
                </td>
                <td align="center">
                    <table border=0 cellspacing=5 cellpadding=5 width=100% align="center">
                        <tr>
                            <td align="left" valign="top">
                                <h2>
                                    The story so far...
                                </h2>
                                It's been <?php echo $cyric_years; ?> years since Cyric the Destroyer tried to
                                send the world into the void.  Mad Quixadhal saved us
                                from utter annihilation, only to plunge us into a
                                darkness that lasted for a terrible <?php echo $quix_years; ?> years.
                                <p>
                                Finally, there is a new dawn.
                                <p>
                                The ancient empire of Nesthar has once again been
                                swallowed by the jungle.  Gredth, ever a home to
                                pirates and ruffians, was overrun by terrors from the
                                depths of the ocean.  Mighty Highstaff has fallen, and
                                is now home to feral halflings.  The humble village of
                                Shylar remains, and it has become the only real bastion
                                of civilization left in this part of the world.
                                <p>
                                Blood runs deep here.  The sons and daughters of
                                nobility try to reclaim their birthright, while the
                                children of heroes assert their claims by force.
                                Wealthy merchants fight to buy what could not be
                                purchased in the old world.
                                <p>
                                Mages have siezed control of ancient places of power.
                                Temples rise to give the priests strongholds to counter
                                them.  The laws of the old empire are ignored, and the
                                rule of the sword has come.  Many choose to live in the
                                shadows of these places, quietly gathering what they
                                need to control them.
                                <p>
                                Do you have what it takes to walk between the lines?
                                To carve a place for yourself in history?  Can you
                                found a new empire here?  Or will you become fodder for
                                another?
                                <p>
                                Welcome!  May your stay here be... interesting.
                            </td>
                        </tr>
                    </table>
                </td>
                <td align="center" width="180" valign="bottom">
<!--
                    <a href="http://www.mudbytes.net/index.php?a=recent">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/koolaid.png" border=0 align="center" width=95 height=100 alt="(DramaBytes)">
                    </a>
                    <br />
                    <br />
-->
                    <a href="mudlist.php">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/mudlist_button.png"
                             border=0 align="center" width=121 height=92
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                    <br />
                    <br />
                    <a href="http://www.smaugmuds.org/">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/smaugmuds_button.png"
                             border=0 align="center" width=119 height=26
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                    <br />
                    <a href="http://lpmuds.net/forum/index.php">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/lpmudsnet_button.png"
                             border=0 align="center" width=119 height=26
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                    <br />
                    <a href="http://www.mudbytes.net/index.php?a=recent">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/mudbytes_button.png"
                             border=0 align="center" width=119 height=26
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                    <br />
                    <br />
                    <a href="i3log.php">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/intermud_logs.png"
                             border=0 align="center" width=154 height=200
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
<!--
                    <br />
                    <a href="/~quixadhal/rift/riftstatus.php">
                        <img src="http://i302.photobucket.com/albums/nn96/quixadhal/shadowlord/rift.png" border=0 align="center" width=100 height=67 alt="(Rift)">
                    </a>
-->
                </td>
            </tr>
        </table>
        <table border=0 cellspacing=0 cellpadding=0 width=100% align="center">
            <tr>
                <td align="center" colspan="3">
                    <span style="color: #FFFF00">
                        <?php echo "An astral traveller shouts, '" . $stuff[rand(0,sizeof($stuff)-1)] . "'"; ?>
                    </span>
                </td>
            </tr>
<!--
            <tr>
                <td align="center">&nbsp;</td>
                <td align="center">
                    <fieldset>
                        <legend>&raquo;&nbsp;Apocalypse!&nbsp;&laquo;</legend>
                        <table border=0 cellspacing=0 cellpadding=0 width=100% align="center">
                        <tr>
                        <td align="right" width="25%">Myan:&nbsp;&nbsp;</td>
                        <td align="left"><script language="JavaScript" src="myan.js"></script></td>
                        </tr>
                        <tr>
                        <td align="right" width="25%">Christmas:&nbsp;&nbsp;</td>
                        <td align="left"><script language="JavaScript" src="xmas.js"></script></td>
                        </tr>
                        </table>
                    </fieldset>
                </td>
                <td align="center">&nbsp;</td>
            </tr>
-->
            <tr>
                <td align="center" width="33%">
                    <span style="display: block !important; width: 120px; text-align: center; font-family: sans-serif; font-size: 12px;">
                        <a  href="http://www.wunderground.com/radar/radblast.asp?zoommode=pan&prevzoom=zoom&num=1&frame=0&delay=15&scale=1.000&noclutter=0&ID=GRR&type=N0R&showstorms=10&lat=42.29166794&lon=-85.58721924&label=Kalamazoo,%20MI&map.x=400&map.y=240&scale=1.000&centerx=400&centery=240&showlabels=1">
                            <img    src="gfx/radar.png"
                                    height="64" width="64" border="0"
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                            />
                        </a>
                    </span>
                </td>
                <td align="center">
                    <a href="https://github.com/quixadhal/bloodlines">
                        <img src="gfx/git-logo.png"
                             border="0" align="center" width=72 height=27
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                </td>
                <td align="center" width="33%">
                    <span style="display: block !important; width: 120px; text-align: center; font-family: sans-serif; font-size: 12px;">
                        <?php
                        $visitor_ip = $_SERVER['REMOTE_ADDR'];
                        $varr = explode(".", $visitor_ip);
                        //$visitor_net = inet_pton($visitor) & 0xFFFFFF00;
                        //$local_net = inet_pton('192.168.1.0') & 0xFFFFFF00;
                        //if($visitor_net == $local_net)
                        if($varr[0] == "192" && $varr[1] == "168")
                        {
                        ?>
                        <a href="http://192.168.1.1/">
                            <img src="gfx/tomato.png"
                                 border="0" align="center" width=64 height=64
                                 style="opacity: 0.6; filter: alpha(opacity=60);"
                                 onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                                 onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                            />
                        </a>
                        <?php } else { ?>
                        <a href="http://www.shadowandy.net/2012/03/asus-rt-n66u-tomatousb-firmware-flashing-guide.htm">
                            <img src="gfx/tomato.png"
                                 border="0" align="center" width=64 height=64
                                 style="opacity: 0.6; filter: alpha(opacity=60);"
                                 onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                                 onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                            />
                        </a>
                        <?php } ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td align="left"> &nbsp; </td>
                <td align="center">
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="RNV3EB7MLFRWA">
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif"
                           id="paypal"
                           border="0" name="submit"
                           alt="PayPal - The safer, easier way to pay online!"
                           style="opacity: 0.6; filter: alpha(opacity=60);"
                           onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                           onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                    >
                    <!-- <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1"> -->
                    </form>
                </td>
                <?php
                $time_end = microtime(true);
                $time_spent = $time_end - $time_start;
                ?>
                <td align="right"><span style="color: #333333">&nbsp;Page generated in <? printf( "%8.4f", $time_spent); ?> seconds.</span></td>
            </tr>
       </table>
    </body>
</html>
