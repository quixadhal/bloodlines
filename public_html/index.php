<?php
$time_start = microtime(true);

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

function is_local_ip() {
    $visitor_ip = $_SERVER['REMOTE_ADDR'];
    $varr = explode(".", $visitor_ip);
    if($varr[0] == "192" && $varr[1] == "168")
        return 1;
    return 0;
}

$isLocal = is_local_ip();

$graphics = array();
$graphics['background']         = $isLocal ? "gfx/dark_wood.jpg"                : "https://lh5.googleusercontent.com/-zvnNrcuqbco/UdooZZelxoI/AAAAAAAAALA/9u5S92UySEA/s800/dark_wood.jpg";
$graphics['bloodlines']         = $isLocal ? "gfx/bloodlines.png"               : "https://lh4.googleusercontent.com/-fWWe4X6fzVE/UdooZQ98rGI/AAAAAAAAAK4/vjYmeQdoaXc/s800/bloodlines.png";
$graphics['wileymud4']          = $isLocal ? "gfx/wileymud4.png"                : "https://lh6.googleusercontent.com/-DdOSH9sMalA/UdoolEmvWMI/AAAAAAAAAP8/_wWNhacagcg/s800/wileymud4.png";
$graphics['otglogo']            = $isLocal ? "gfx/otg_logo_glow.png"            : "https://lh5.googleusercontent.com/-6XTGnh8-yEU/UdoogvR0EqI/AAAAAAAAAOE/krXrEnYb-EY/s800/otg_logo_glow.png";
$graphics['mudlist']            = $isLocal ? "gfx/mudlist_button.png"           : "https://lh4.googleusercontent.com/-iJt4sdOyHKE/UdooeCf9GzI/AAAAAAAAAM8/y83bOycnEIE/s800/mudlist_button.png";
$graphics['smaug']              = $isLocal ? "gfx/SmaugMUDs_Normal.png"         : "https://lh3.googleusercontent.com/-RRXmrG03k0w/UgrZr-NZEiI/AAAAAAAAAfI/baWPkJjmcAs/s800/SmaugMUDs_Normal.png";
$graphics['smaug_mo']           = $isLocal ? "gfx/SmaugMUDs_MouseOver.png"      : "https://lh3.googleusercontent.com/-g5LlPHfcziM/UgrZrJqwfVI/AAAAAAAAAek/PW4-bV2WVas/s800/SmaugMUDs_MouseOver.png";
$graphics['lpmuds']             = $isLocal ? "gfx/LpMUDs_Normal.png"            : "https://lh6.googleusercontent.com/--wFj6ovBJoM/UgrZr2oQWfI/AAAAAAAAAe4/0C9j-LvE9Bk/s800/LpMUDs_Normal.png";
$graphics['lpmuds_mo']          = $isLocal ? "gfx/LpMUDs_MouseOver.png"         : "https://lh6.googleusercontent.com/-hRcov-ajocY/UgrZqitNE8I/AAAAAAAAAeY/ymEt39Y2Ejg/s800/LpMUDs_MouseOver.png";
$graphics['mudbytes']           = $isLocal ? "gfx/DramaBytes_Normal.png"        : "https://lh4.googleusercontent.com/-uyRMefFA_J4/UgrZqv3rnhI/AAAAAAAAAeo/WT_7qjSOqOA/s800/DramaBytes_Normal.png";
$graphics['mudbytes_mo']        = $isLocal ? "gfx/DramaBytes_MouseOver.png"     : "https://lh4.googleusercontent.com/-qmvpbqz8PbY/UgrZqrPblUI/AAAAAAAAAeU/EqHHuCEYVs8/s800/DramaBytes_MouseOver.png";
$graphics['tmc']                = $isLocal ? "gfx/TMC_Normal.png"               : "https://lh4.googleusercontent.com/-fx7h-eIXZpw/UgrZsTtQOpI/AAAAAAAAAfE/Qgz5bRzFHQ0/s800/TMC_Normal.png";
$graphics['tmc_mo']             = $isLocal ? "gfx/TMC_MouseOver.png"            : "https://lh5.googleusercontent.com/-0wXWIZF_zjk/UgrZr_pPDdI/AAAAAAAAAe0/3Mu9V5QYXMg/s800/TMC_MouseOver.png";

$graphics['intermud']           = $isLocal ? "gfx/intermud_logs.png"            : "https://lh3.googleusercontent.com/-SqlTafszKbk/Udoobvh-9WI/AAAAAAAAAL4/syQZMXn6lTs/s800/intermud_logs.png";
$graphics['radar']              = $isLocal ? "gfx/radar.png"                    : "https://lh5.googleusercontent.com/-ilEzbVZUqYA/UdoohkfM4vI/AAAAAAAAAOo/Mh1_e-lT1kA/s96/radar.png";
$graphics['minecraft_icon']     = $isLocal ? "gfx/minecraft_icon.png"           : "https://lh4.googleusercontent.com/-uchsisM_vEU/UdoocweO5ZI/AAAAAAAAAMo/XfjYgO0DUjc/s96/minecraft_icon.png";
$graphics['minecraft_map_icon'] = $isLocal ? "gfx/minecraft_map_icon.png"       : "https://lh5.googleusercontent.com/-tE6-3NFzLrc/Udoods2AgYI/AAAAAAAAAMs/jx2ju7YMnQ0/s96/minecraft_map_icon.png";
$graphics['gitlogo']            = $isLocal ? "gfx/git-logo.png"                 : "https://lh5.googleusercontent.com/-p1ATsbhYRs8/Udooa9r72ZI/AAAAAAAAALk/1N3ARu3-n10/s800/git-logo.png";
$graphics['bitbucketlogo']      = $isLocal ? "gfx/bitbucket.png"                : "https://lh3.googleusercontent.com/-lWBZPV5urxs/U56w2tTH_SI/AAAAAAAAAxs/nCnJH4Htfxk/w115-h30-no/bitbucket.png";
$graphics['picasa_web']         = $isLocal ? "gfx/picasa.png"                   : "https://lh5.googleusercontent.com/-l_iPTf069TY/UdtZBzPahxI/AAAAAAAAAYk/yDqDQ5HoIKc/s96/picasa.png";
$graphics['tomato']             = $isLocal ? "gfx/tomato.png"                   : "https://lh6.googleusercontent.com/-9jxhHmu9jS0/Udooj0h4OXI/AAAAAAAAAPo/-EieTRD4RmQ/s96/tomato.png";
$graphics['server_icon']        = $isLocal ? "gfx/server_icon.png"              : "https://lh4.googleusercontent.com/-LZ9ek46iToA/UdoojFEhuOI/AAAAAAAAAPQ/y_rRyL_1tR8/s96/server_icon.png";
$graphics['paypal']             = $isLocal ? "gfx/paypal.gif"                   : "https://lh4.googleusercontent.com/-W88wG7HNrZM/UdoohGflgVI/AAAAAAAAAOY/MODtORBmSe0/s800/paypal.gif";

$graphics['email']              = $isLocal ? "gfx/email-icon.png"               : "https://lh6.googleusercontent.com/-2uAB3NjSm_c/U-Vrk09YcUI/AAAAAAAAA4E/V3gT57XH4Y8/s128-no/email-icon.png";
$graphics['facebook']           = $isLocal ? "gfx/facebook.png"                 : "https://lh6.googleusercontent.com/-3yq_pvhJoDw/U-YvfrV4qCI/AAAAAAAAA4g/eMSk8Dhz554/s128-no/facebook.png";
$graphics['tv']                 = $isLocal ? "gfx/tv.png"                       : "https://lh3.googleusercontent.com/-6pMSA2WuYZM/VVQKn3ILC9I/AAAAAAAABdg/JvEgoxqprBU/s2048/tv.png";

$urls = array();
$urls['email']                  = $isLocal ? "http://gmail.com"                 : "mailto:quixadhal@gmail.com";
$urls['facebook']               = $isLocal ? "http://facebook.com"              : "http://www.facebook.com/groups/1470783289801007/";
$urls['tomato']                 = $isLocal ? "http://192.168.1.1/"              : "http://www.shadowandy.net/2012/03/asus-rt-n66u-tomatousb-firmware-flashing-guide.htm";
$urls['photos']                 = $isLocal ? "https://picasaweb.google.com/home": "http://plus.google.com/photos/101991567866995168256/albums";

$weather_url   = "http://www.wunderground.com/cgi-bin/findweather/getForecast?query=zmw:49004.2.99999&bannertypeclick=wu_blueglass";
$weather_image = "http://weathersticker.wunderground.com/weathersticker/cgi-bin/banner/ban/wxBanner?bannertype=wu_blueglass&airportcode=KAZO&ForcedCity=Kalamazoo&ForcedState=MI&zipcode=49004&language=EN";

// Thumbnail    https://lh5.googleusercontent.com/-zvnNrcuqbco/UdooZZelxoI/AAAAAAAAALA/9u5S92UySEA/s144/dark_wood.jpg
// Small        https://lh5.googleusercontent.com/-zvnNrcuqbco/UdooZZelxoI/AAAAAAAAALA/9u5S92UySEA/s288/dark_wood.jpg
// Medium       https://lh5.googleusercontent.com/-zvnNrcuqbco/UdooZZelxoI/AAAAAAAAALA/9u5S92UySEA/s400/dark_wood.jpg
// Medium-Large https://lh5.googleusercontent.com/-zvnNrcuqbco/UdooZZelxoI/AAAAAAAAALA/9u5S92UySEA/s640/dark_wood.jpg
// Large        https://lh5.googleusercontent.com/-zvnNrcuqbco/UdooZZelxoI/AAAAAAAAALA/9u5S92UySEA/s800/dark_wood.jpg

header('Cache-control: max-age=0, must-revalidate');

?>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta http-equiv="refresh" content="600">
        <!-- <script type='text/javascript' src='clock/jquery.js'></script> -->
        <!-- <link href="http://fonts.googleapis.com/css?family=Orbitron" rel="stylesheet" type="text/css"> -->
        <!-- <link href="clock/clock.css" rel="stylesheet" type="text/css"> -->
        <!-- <script type="text/javascript" src="clock/strftime.js"></script> -->
        <!-- <script type="text/javascript" src="clock/clock.js"></script> -->

        <script type="text/javascript">
        function refreshBits(repeat, last_val)
        {
            var spmh = 75;
            var spmd = spmh * 24;
            var spmm = spmd * 35;
            var spmy = spmm * 17;

            d = new Date();
            var diff = Math.floor(d.getTime() / 1000) - 744566400;
            var hours = (diff / spmh) % 24;
            diff = diff - (hours * spmh);
            var days = (diff / spmd) % 35;
            diff = diff - (days * spmd);
            var months = (diff / spmm) % 17;
            diff = diff - (months * spmm);
            var years = Math.round(diff / spmy);

            var stuff = [
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
            ];
            var thing = stuff[Math.floor(Math.random()*stuff.length)];

            document.getElementById('cyric_bit').innerHTML=years;
            document.getElementById('quix_bit').innerHTML=40;
            document.getElementById('astral_bit').innerHTML="An astral traveller shouts, '" + thing + "'";

            var messages = [
                "New Minecraft server is up and running!",
                "SQL dumps of I3 log data are now available.",
                "Wildstar!  Because science without violence is... boring.",
                "Final Fantasy XIV: A Realm Reborn status widgets, because you might care.",
                "Grand Theft Auto IV is fun!  I'm looking forward to GTA V when I finish it.",
                "Nifty message banner, for my continued javascript amusement.",
                "Yay, X-COM expansion is out!",
                "X-Rebirth is here!"
            ];
            var msg_loop = Math.floor(Math.random() * messages.length);
            repeat--;
            if(repeat < 0) {
                repeat = Math.floor(Math.random() * 3) + 1;
                last_val = msg_loop;
            }

            $('div.news').text( messages[last_val] );
            //$('div.news').css({ visibility: visible});

            var image = document.getElementById('weather_bit');
            if (image.complete) {
                var new_image = new Image();
                new_image.id = "weather_bit";
                new_image.src = "<?php echo $weather_image; ?>" + "#" + new Date().getTime();
                new_image.className = image.className;
                image.parentNode.insertBefore(new_image, image);
                image.parentNode.removeChild(image);
            }
            t=setTimeout(function() { refreshBits(repeat, last_val); }, 20 * 1000);
        }
        </script>
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
            Countdown_TargetDate = "05/31/2014 03:00:00 AM";
            Countdown_BackColor = "transparent";
            Countdown_ForeColor = "#00FF00";
            Countdown_CountActive = true;
            Countdown_CountStepper = -1;
            Countdown_LeadingZero = true;
            Countdown_DisplayFormat = "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
            Countdown_FinishMessage = "PLAY!";
        </script>
        <!-- <script src="//www.archeagestatus.com/widget.js?server=Tahyang" id="aas" ></script> -->
        <title>
            Bloodlines:  WileyMUD IV
        </title>
        <link rel="shortcut icon" href="gfx/fire.ico" />
        <link rev="made" href="mailto:quixadhal@awasteoftime.net">
        <style>
            a { text-decoration:none; }
            a:hover { text-decoration:underline; }
        </style>
        <style type="text/css">
            .news {
                display: inline-block;
                border: none;
                padding: 0px;
                margin: 0px;
                position: absolute;
                white-space: nowrap;
                text-align: center;
                vertical-align: middle;
                min-height: 28px;
                height: 28px;
                line-height: 28px;
                font-size: 24px;
                background-color: rgba(0,0,0,0.1);
                color: #FF6000;
                /* right: -1000px; */
                /* width: 1000px; */
                width: 100%;
                /* visibility: hidden; */
            }
            .arr {
                display: inline-block;
                border: none;
                padding: 0px;
                margin: 0px;
                text-align: center;
                vertical-align: middle;
                min-height: 44px;
                height: 44px;
                line-height: 44px;
                font-size: 16px;
                background-color: rgba(0,0,0,0.1);
                color: #00FF00;
                float: left;
                margin-right: 20px;
            }
            .wow {
                display: inline-block;
                border: none;
                padding: 0px;
                margin: 0px;
                text-align: center;
                vertical-align: middle;
                min-height: 44px;
                height: 44px;
                line-height: 44px;
                font-size: 16px;
                background-color: rgba(0,0,0,0.1);
                color: #FFFFFF;
                float: left;
                margin-right: 20px;
            }
            .archeage {
                display: inline-block;
                border: none;
                padding: 0px;
                margin: 0px;
                text-align: center;
                vertical-align: middle;
                min-height: 44px;
                height: 44px;
                line-height: 44px;
                font-size: 16px;
                background-color: rgba(0,0,0,0.1);
                color: #FFFFFF;
                float: left;
                margin-right: 20px;
            }
            .wildstar {
                display: inline-block;
                border: none;
                padding: 0px;
                margin: 0px;
                text-align: center;
                vertical-align: middle;
                min-height: 44px;
                height: 44px;
                line-height: 44px;
                font-size: 16px;
                background-color: rgba(0,0,0,0.1);
                color: #FFFFFF;
                float: left;
                margin-right: 20px;
            }
            .countdown {
                padding: 0px;
                margin: 10px;
                text-align: center;
                vertical-align: middle;
                background-color: rgba(0,0,0,0.1);
                border: 0px solid #808080;
                font-family: sans-serif;
                font-size: 18px;
            }
            .weather-image {
                opacity: 0.6;
                filter: alpha(opacity=60);
                width: 120px;
                height: 68px;
                border: 0;
            }
         
            .weather-image:hover {
                opacity: 1.0;
                filter: alpha(opacity=100);
            }
        </style>
<!--
        <script>
            $(document).ready(function() {
                var messages = [
                    "New Minecraft server is up and running!",
                    "SQL dumps of I3 log data are now available.",
                    "Wildstar!  Because science without violence is... boring.",
                    "Final Fantasy XIV: A Realm Reborn status widgets, because you might care.",
                    "Grand Theft Auto IV is fun!  I'm looking forward to GTA V when I finish it.",
                    "Nifty message banner, for my continued javascript amusement.",
                    "Yay, X-COM expansion is out!",
                    "X-Rebirth is here!"
                ];
                var msg_loop = Math.floor(Math.random() * messages.length);
                function anim() {
                    msg_loop++;
                    if( msg_loop >= messages.length ) {
                        msg_loop = 0;
                    }
                    $('div.news').text( messages[msg_loop] );
                    $('div.news').css({ right: 0 - $('div.news').width() });
                    $('div.news').animate({ right: $(document).width() }, 60000, function() { anim(); } );
                }
                anim();
            });
        </script>
-->
        <script language="javascript">
            function toggleDiv(divID) {
                if(document.getElementById(divID).style.display == 'none') {
                    document.getElementById(divID).style.display = 'block';
                } else {
                    document.getElementById(divID).style.display = 'none';
                }
            }
        </script>
    </head>
    <!-- <body background="<?php echo $graphics['background']; ?>" bgcolor="#505050" text="#d0d0d0" link="#ffffbf" vlink="#ffa040" onload="startClock()"> -->
    <body background="<?php echo $graphics['background']; ?>" bgcolor="#505050" text="#d0d0d0" link="#ffffbf" vlink="#ffa040" style="overflow-x: hidden;" onload="refreshBits(0,0)">
<!--
        <div style="position: fixed; z-index: -99; width: 100%; height: 100%">
            <iframe frameborder="0" height="100%" width="100%"
                    src="https://youtube.com/embed/Uvl3ef7D5rg?autoplay=1&controls=0&showinfo=0&autohide=1"> -->
                    <!-- version=3&enablejsapi=1 -->
                    <!-- playlist=fCPzLNqYe1U,P1Bf_fRNq9g, -->
<!--            </iframe>
        </div>
-->
        <div id="newsdiv" class="news">
            &nbsp;
        </div>
        <br />
        <table border=0 cellspacing=0 cellpadding=0 width=80% align="center">
            <tr>
                <td align="right" valign="top" style="vertical-align: top">
                    <a href="/anyterm/anyterm.shtml?rows=40&cols=100" title="Play WileyMUD III">
                        <img src="<?php echo $graphics['bloodlines']; ?>"
                             border=0 width=469 height=160
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                </td>
                <td align="left" valign="bottom" style="vertical-align: bottom">
                    <a href="/anyterm/anyterm.shtml?rows=40&cols=100" title="Play WileyMUD III">
                        <img src="<?php echo $graphics['wileymud4']; ?>"
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
                                style="vertical-align: top; opacity: 0.6; filter: alpha(opacity=60);"
                                onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                                onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                            > 
                                <font size="-2" color="#FFFFBF"><a href="/~wiley/" title="OLD WileyMUD Homepage">Also visit WileyMUD III, for amusement!</a></font>
                            </td>
                            <td align="right" valign="bottom" width="5%" style="vertical-align: bottom">
                                &nbsp;
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table border=0 cellspacing=0 cellpadding=0 width=100% align="center">
            <tr>
                <td align="center" width="180" valign="bottom" style="vertical-align: bottom">
<!--
                    <div id="clock">
                        <div class="clockGlass"></div>
                    </div>
-->
                    <!-- <div id="olddate" align="center" style="color: #d0d000;"><?php echo date('F j, Y'); ?></div> -->
                    <span style="color: #1F1F1F"><a href="<?php echo $urls['photos']; ?>" title="Photos">
                        <img src="<?php echo $graphics['picasa_web']; ?>" 
                             border="0" width="64" height="64" alt="(dynmap)"
                             style="opacity: 0.4; filter: alpha(opacity=40);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                         />
                    </a></span>
                    <br />
                    <!-- <span style="color: #1F1F1F"><a href="https://picasaweb.google.com/home" title="Pictures"> -->
                    <span style="color: #1F1F1F"><a href="<?php echo $urls['email']; ?>" title="Email!">
                        <img src="<?php echo $graphics['email']; ?>"
                             border=0 align="center" width=64 height=64
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                         />
                    </a></span>
                    <span style="color: #1F1F1F"><a href="<?php echo $urls['facebook']; ?>" title="Facebutt">
                        <img src="<?php echo $graphics['facebook']; ?>"
                             border=0 align="center" width=64 height=64
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                         />
                    </a></span>
                    <br />
                    <br />
                    <a href="http://www.oldtimersguild.com/vb/forum.php" title="No Drama!">
                        <img src="<?php echo $graphics['otglogo']; ?>"
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
                        <a  href="<?php echo $weather_url; ?>">
                            <img    src="<?php echo $weather_image; ?>"
                                    id="weather_bit" class="weather-image"
                            />
                        </a>
                    </span>
                </td>
                <td align="center">
                    <table border=0 cellspacing=5 cellpadding=5 width=100% align="center">
                        <tr>
                            <td align="left" valign="top" style="vertical-align: top">
                                <h2>
                                    The story so far...
                                </h2>
                                It's been <span id="cyric_bit"><?php echo $cyric_years; ?></span> years
                                since Cyric the Destroyer tried to send the world into the void.  Mad
                                Quixadhal saved us from utter annihilation, only to plunge us into a
                                darkness that lasted for a terrible <span id="quix_bit"><?php echo $quix_years; ?></span> years.
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
                <td align="center" width="180" valign="bottom" style="vertical-align: bottom">
                    <a href="mudlist.php" title="MUD Listing">
                        <img src="<?php echo $graphics['mudlist']; ?>"
                             border=0 align="center" width=121 height=92
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                    <br />
                    <br />
                    <a href="http://www.mudbytes.net/forum/" title="ALL about the DRAMA!">
                        <img src="<?php echo $graphics['mudbytes']; ?>"
                             border=0 align="center" width=120 height=30
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                        <!-- 
                             onmouseover="this.src='<?php echo $graphics['mudbytes_mo']; ?>';" 
                             onmouseout="this.src='<?php echo $graphics['mudbytes']; ?>';" -->
                    </a>
                    <br />
                    <a href="http://www.mudconnect.com/SMF/index.php" title="The NEW MUD Connector forums">
                        <img src="<?php echo $graphics['tmc']; ?>"
                             border=0 align="center" width=120 height=30
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                    <br />
                    <a href="http://lpmuds.net/forum/index.php" title="LPMUD faithful">
                        <img src="<?php echo $graphics['lpmuds']; ?>"
                             border=0 align="center" width=120 height=30
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                    <br />
                    <a href="http://www.smaugmuds.org/" title="The best Dikurivative codebase">
                        <img src="<?php echo $graphics['smaug']; ?>"
                             border=0 align="center" width=120 height=30
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                    <br />
                    <br />
                    <a href="i3log.php" title="I3 Chat Logs">
                        <img src="<?php echo $graphics['intermud']; ?>"
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
                <td align="center" colspan="7" height="48" valign="top" style="vertical-align: top">
                    <span id="astral_bit" style="color: #FFFF00">
                        &nbsp;
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
                <td align="center" valign="center" width="12%" style="vertical-align: middle">
                    <!-- <span style="display: block !important; width: 120px; text-align: center; font-family: sans-serif; font-size: 12px;"> -->
                        <a  href="http://www.wunderground.com/radar/radblast.asp?zoommode=pan&prevzoom=zoom&num=1&frame=0&delay=15&scale=1.000&noclutter=0&ID=GRR&type=N0R&showstorms=10&lat=42.29166794&lon=-85.58721924&label=Kalamazoo,%20MI&map.x=400&map.y=240&scale=1.000&centerx=400&centery=240&showlabels=1">
                            <img    src="<?php echo $graphics['radar']; ?>"
                                    height="96" width="96" border="0"
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                            />
                        </a>
                    <!-- </span> -->
                </td>
                <td align="center" valign="bottom" width="12%" style="vertical-align: bottom">
                        <!-- <span style="color: #1F1F1F"><a href="/~minecraft/phpBB3/index.php" title="Minecraft!"> -->
                        <span style="color: #1F1F1F"><a href="/~minecraft/map.html" title="Local Dynmap">
                            <img src="<?php echo $graphics['minecraft_icon']; ?>" 
                                 border="0" width="96" height="96" alt="(minecraft)"
                                 style="opacity: 0.4; filter: alpha(opacity=40);"
                                 onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                                 onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                             />
                        </a></span>
                </td>
                <td align="center" valign="bottom" width="12%" style="vertical-align: bottom">
                        <span style="color: #1F1F1F"><a href="http://darkstar.pixelefx.net:8123/" title="Dynmap">
                            <img src="<?php echo $graphics['minecraft_map_icon']; ?>" 
                                 border="0" width="96" height="96" alt="(dynmap)"
                                 style="opacity: 0.4; filter: alpha(opacity=40);"
                                 onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                                 onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                             />
                        </a></span>
                </td>
                <td align="center" valign="bottom" style="vertical-align: bottom">
                    <a href="https://github.com/quixadhal">
                        <img src="<?php echo $graphics['gitlogo']; ?>" title="My GitHub repositories"
                             border="0" align="center" width="72" height="27"
                             style="opacity: 0.6; filter: alpha(opacity=60); padding-bottom: 20;"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                    &nbsp;
                    <a href="https://bitbucket.org/quixadhal">
                        <img src="<?php echo $graphics['bitbucketlogo']; ?>" title="My Bitbucket repositories"
                             border="0" align="center" width="115" height="30"
                             style="opacity: 0.6; filter: alpha(opacity=60); padding-bottom: 20;"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                    <br />
                    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="RNV3EB7MLFRWA">
                    <input type="image" src="<?php echo $graphics['paypal']; ?>"
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
                <td align="center" valign="bottom" width="12%" style="vertical-align: bottom">
                    <!-- <span style="display: block !important; width: 120px; text-align: center; font-family: sans-serif; font-size: 12px;"> -->
                    <a href="<?php echo $urls['tomato']; ?>">
                        <img src="<?php echo $graphics['tomato']; ?>" title="Router Page"
                             border="0" align="center" width="96" height="96"
                             style="opacity: 0.6; filter: alpha(opacity=60);"
                             onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                             onmouseout="this.style.opacity='0.6'; this.style.filter='alpha(opacity=60';"
                        />
                    </a>
                </td>
                <td align="center" valign="bottom" width="12%" style="vertical-align: bottom">
                        <span style="color: #1F1F1F"><a href="/~bloodlines/server.php" title="Server Stats">
                            <img src="<?php echo $graphics['server_icon']; ?>" 
                                 border="0" width="96" height="96" alt="(server)"
                                 style="opacity: 0.4; filter: alpha(opacity=40);"
                                 onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                                 onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                             />
                        </a></span>
                    <!-- </span> -->
                </td>
                <td align="center" valign="bottom" width="12%" style="vertical-align: bottom">
                        <span style="color: #1F1F1F"><a href="/~bloodlines/video_list.php" title="Server Stats">
                            <img src="<?php echo $graphics['tv']; ?>" 
                                 border="0" width="96" height="96" alt="(tv)"
                                 style="opacity: 0.4; filter: alpha(opacity=40);"
                                 onmouseover="this.style.opacity='1.0'; this.style.filter='alpha(opacity=100';"
                                 onmouseout="this.style.opacity='0.4'; this.style.filter='alpha(opacity=40';"
                             />
                        </a></span>
                    <!-- </span> -->
                </td>
            </tr>
            <tr>
                <td colspan="7" align="center">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td align="left" colspan="4"> &nbsp; </td>
                <?php
                $time_end = microtime(true);
                $time_spent = $time_end - $time_start;
                ?>
                <td align="right" colspan="3" style="vertical-align: bottom">
                    <a href="javascript:;" onmousedown="toggleDiv('source');">
                    <span style="color: #333333">&nbsp;Page generated in <?php printf( "%8.4f", $time_spent); ?> seconds.</span>
                    </a>
                </td>
            </tr>
       </table>
        <div id="source" style="display: none; vertical-align: bottom; background-color: white;"> <?php echo numbered_source(__FILE__); ?> </div>
    </body>
</html>
