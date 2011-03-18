<?php
$time_start = microtime(true);
//$now = date('g:ia \o\n l, \t\h\e jS \o\f F, Y');
?>
<html>
    <head>
        <meta http-equiv="refresh" content="300">
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
            document.getElementById('clock').innerHTML=h+":"+m+":"+s+" "+p;
            document.getElementById('date').innerHTML=month[M]+" "+d+", "+y;
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
        <title>
            Bloodlines:  WileyMUD IV
        </title>
        <link rev="made" href="mailto:quixadhal@shadowlord.org">
    </head>
    <body background="gfx/dark_wood.jpg" bgcolor="#505050" text="#d0d0d0" link="#ffffbf" vlink="#ffa040" onload="startClock()">
        <table border=0 cellspacing=0 cellpadding=0 width=80% align="center">
            <tr>
                <td align="left" valign="top">
                    <a href="telnet://wiley.shadowlord.org:3000/">
                        <img src="gfx/bloodlines.png" border=0 width=469 height=160 alt="(Bloodlines:)">
                    </a>
                </td>
                <td align="right" valign="bottom">
                    <a href="telnet://wiley.shadowlord.org:3000/">
                        <img src="gfx/wileymud4.png" border=0 width=354 height=81 alt="(WileyMUD IV)">
                    </a>
                </td>
            </tr>
        </table>
        <table border=0 cellspacing=0 cellpadding=0 width=100% align="center">
            <tr>
                <td align="center" width="20%" valign="bottom">
                    <a href="gitweb.cgi?p=.git;a=summary">
                        <img src="/gitweb/git-logo.png" border=0 align="center" width=72 height=27 alt="(Git)">
                    </a>
                    <br />
                    <br />
                    <div id="clock" align="center" style="color: #d0d000;"><?php echo date('g:i:s a'); ?></div>
                    <div id="date" align="center" style="color: #d0d000;"><?php echo date('F j, Y'); ?></div>
                    <br />
                    <a href="http://www.wunderground.com/US/MI/Kalamazoo.html?bannertypeclick=wu_blueglass">
                        <img src="http://weathersticker.wunderground.com/cgi-bin/banner/ban/wxBanner?bannertype=wu_blueglass&airportcode=KAZO&ForcedCity=Kalamazoo&ForcedState=MI" alt="Click for Kalamazoo, Michigan Forecast" height="68" width="120" border=0 />
                    </a>
                </td>
                <td align="center" width="60%">
                    <table border=0 cellspacing=5 cellpadding=5 width=100% align="center">
                        <tr>
                            <td align="left" valign="top">
                                <h2>
                                    The story so far...
                                </h2>
                                It's been 371 years since Cyric the Destroyer tried to
                                send the world into the void.  Mad Quixadhal saved us
                                from utter annihilation, only to plunge us into a
                                darkness that lasted for decades.
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
                <td align="right" width="20%" valign="bottom">
                    <a href="i3log.php">
                        <img src="gfx/intermud_logs.png" border=0 align="right" width=154 height=200 alt="(Intermud)">
                    </a>
                </td>
            </tr>
        </table>
        <table border=0 cellspacing=0 cellpadding=0 width=100% align="center">
            <tr>
                <!-- <td align="left" width="50%"><span style="color: #333333">Last refreshed at <? echo $now; ?>.&nbsp;</span></td> -->
                <?php
                $time_end = microtime(true);
                $time_spent = $time_end - $time_start;
                ?>
                <td align="right" width="50%"><span style="color: #333333">&nbsp;Page generated in <? printf( "%8.4f", $time_spent); ?> seconds.</span></td>
            </tr>
       </table>
    </body>
</html>
