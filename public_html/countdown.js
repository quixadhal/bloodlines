/*
Author: Robert Hashemian
http://www.hashemian.com/

You can use this code in any manner so long as the author's
name, Web address and this disclaimer is kept intact.
********************************************************
Usage Sample:

<script language="JavaScript">
Countdown_TargetDate = "12/31/2020 5:00 AM";
Countdown_BackColor = "palegreen";
Countdown_ForeColor = "navy";
Countdown_CountActive = true;
Countdown_CountStepper = -1;
Countdown_LeadingZero = true;
Countdown_DisplayFormat = "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
Countdown_FinishMessage = "It is finally here!";
</script>
<script language="JavaScript" src="http://scripts.hashemian.com/js/countdown.js"></script>
*/

function Countdown_calcage(secs, num1, num2) {
  s = ((Math.floor(secs/num1))%num2).toString();
  if (Countdown_LeadingZero && s.length < 2)
    s = "0" + s;
  return "<b>" + s + "</b>";
}

function Countdown_CountBack(secs) {
  if (secs < 0) {
    document.getElementById("Countdown_cntdwn").innerHTML = Countdown_FinishMessage;
    return;
  }
  DisplayStr = Countdown_DisplayFormat.replace(/%%D%%/g, Countdown_calcage(secs,86400,100000));
  DisplayStr = DisplayStr.replace(/%%H%%/g, Countdown_calcage(secs,3600,24));
  DisplayStr = DisplayStr.replace(/%%M%%/g, Countdown_calcage(secs,60,60));
  DisplayStr = DisplayStr.replace(/%%S%%/g, Countdown_calcage(secs,1,60));

  document.getElementById("Countdown_cntdwn").innerHTML = DisplayStr;
  if (Countdown_CountActive)
    setTimeout("Countdown_CountBack(" + (secs+Countdown_CountStepper) + ")", Countdown_SetTimeOutPeriod);
}

function Countdown_putspan(backcolor, forecolor) {
    if (backcolor == "transparent")
        document.write("<span id='Countdown_cntdwn' style='color:" + forecolor + "'></span>");
    else
        document.write("<span id='Countdown_cntdwn' style='background-color:" + backcolor + 
                       "; color:" + forecolor + "'></span>");
}

if (typeof(Countdown_BackColor)=="undefined")
  Countdown_BackColor = "white";
if (typeof(Countdown_ForeColor)=="undefined")
  Countdown_ForeColor= "black";
if (typeof(Countdown_TargetDate)=="undefined")
  Countdown_TargetDate = "12/31/2020 5:00 AM";
if (typeof(Countdown_DisplayFormat)=="undefined")
  Countdown_DisplayFormat = "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
if (typeof(Countdown_CountActive)=="undefined")
  Countdown_CountActive = true;
if (typeof(Countdown_FinishMessage)=="undefined")
  Countdown_FinishMessage = "";
if (typeof(Countdown_CountStepper)!="number")
  Countdown_CountStepper = -1;
if (typeof(Countdown_LeadingZero)=="undefined")
  Countdown_LeadingZero = true;


Countdown_CountStepper = Math.ceil(Countdown_CountStepper);
if (Countdown_CountStepper == 0)
  Countdown_CountActive = false;
var Countdown_SetTimeOutPeriod = (Math.abs(Countdown_CountStepper)-1)*1000 + 990;
Countdown_putspan(Countdown_BackColor, Countdown_ForeColor);
var Countdown_dthen = new Date(Countdown_TargetDate);
var Countdown_dnow = new Date();
if(Countdown_CountStepper>0)
  Countdown_ddiff = new Date(Countdown_dnow-Countdown_dthen);
else
  Countdown_ddiff = new Date(Countdown_dthen-Countdown_dnow);
Countdown_gsecs = Math.floor(Countdown_ddiff.valueOf()/1000);
Countdown_CountBack(Countdown_gsecs);
