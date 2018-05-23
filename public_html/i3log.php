<?php

function is_local_ip() {
    $visitor_ip = $_SERVER['REMOTE_ADDR'];
    $varr = explode(".", $visitor_ip);
    if($varr[0] == "192" && $varr[1] == "168")
        return 1;
    return 0;
}

?>

<html>
    <head>
        <script>
        function byte2Hex(n)
        {
            var nybHexString = "0123456789ABCDEF";
            return String(nybHexString.substr((n >> 4) & 0x0F,1)) + nybHexString.substr(n & 0x0F,1);
        }

        function RGB2Color(r,g,b) {
            return '#' + byte2Hex(r) + byte2Hex(g) + byte2Hex(b);
        }

        function cycleColor(rFreq, gFreq, bFreq, rPhase, gPhase, bPhase, center, width) {
            if (rFreq == undefined)
                rFreq = 0.3;
            if (gFreq == undefined)
                gFreq = 0.3;
            if (bFreq == undefined)
                bFreq = 0.3;
            if (rPhase == undefined)
                rPhase = 0;
            if (gPhase == undefined)
                gPhase = 2;
            if (bPhase == undefined)
                bPhase = 4;
            if (width == undefined)
                width = 127;
            if (center == undefined)
                center = 128;

            var d = new Date();
            var s = d.getSeconds();
            var ms = d.getMilliseconds();

            var time = (s + ms/1000.0);

            var red = Math.sin(rFreq * time + rPhase) * width + center;
            var grn = Math.sin(gFreq * time + gPhase) * width + center;
            var blu = Math.sin(bFreq * time + bPhase) * width + center;
            var color = RGB2Color(red,grn,blu);

            //document.write( 'time="' + time + '" </br>');
            //document.write( 'color="' + RGB2Color(red,grn,blu) + '" <br>');
            return color;
        }

        function doCycle() {
            //var color = cycleColor(.3,.3,.3,0,2,4,230,25); // Pastel
            var color = cycleColor(.3,.3,.3,0,2,4,127,128);

            document.body.style.background=color;
            t = setTimeout('doCycle()',100);
        }
        </script>

        <title>I3 Log</title>
    </head>
    <body bgcolor="white" onload="doCycle()" width="60%" align="center" valign="center">
<?php
    $filename = "visitor.data";
    $visitor = $_SERVER['REMOTE_ADDR'];
    $isLocal = is_local_ip();
    if($visitor) {
        $lines = file($filename, FILE_IGNORE_NEW_LINES);
        $found = array_search($visitor, $lines, true);
        if($found === false) {
            $fp = fopen($filename, "a");
            fprintf($fp, "%s\n", $visitor);
            fclose($fp);
        }
    }
?>
        <div style="position: absolute; top: 50%; left: 50%; transform: translateX(-50%) translateY(-50%);">
            <h1>You are not worthy<?php if($visitor && !$isLocal) echo ", $visitor";?>.</h1>
            <hr color="black" />
            <h3>The I3 Logs have been deemed unfit for human consumption.</h3>
            <hr color="black" />
            <p> By order of Dread Quixadhal, they have been removed to prevent further brain damage.
                Please seek help if you have been reading them, and find a hobby that is more healthy,
                such as trepanation, self-flaggelation, or running for political office.
            </p>
        </div>
    </body>
</html>
