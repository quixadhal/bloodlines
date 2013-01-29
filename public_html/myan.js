/*
Author: Robert Hashemian
http://www.hashemian.com/

You can use this code in any manner so long as the author's
name, Web address and this disclaimer is kept intact.
********************************************************
Usage Sample:

<script language="JavaScript">
Myan_TargetDate = "12/31/2020 5:00 AM";
Myan_BackColor = "palegreen";
Myan_ForeColor = "navy";
Myan_CountActive = true;
Myan_CountStepper = -1;
Myan_LeadingZero = true;
Myan_DisplayFormat = "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
Myan_FinishMessage = "It is finally here!";
</script>
<script language="JavaScript" src="http://scripts.hashemian.com/js/countdown.js"></script>
*/

function Myan_calcage(secs, num1, num2) {
  s = ((Math.floor(secs/num1))%num2).toString();
  if (Myan_LeadingZero && s.length < 2)
    s = "0" + s;
  return "<b>" + s + "</b>";
}

function Myan_CountBack(secs) {
  if (secs < 0) {
    document.getElementById("Myan_cntdwn").innerHTML = Myan_FinishMessage;
    return;
  }
  DisplayStr = Myan_DisplayFormat.replace(/%%D%%/g, Myan_calcage(secs,86400,100000));
  DisplayStr = DisplayStr.replace(/%%H%%/g, Myan_calcage(secs,3600,24));
  DisplayStr = DisplayStr.replace(/%%M%%/g, Myan_calcage(secs,60,60));
  DisplayStr = DisplayStr.replace(/%%S%%/g, Myan_calcage(secs,1,60));

  document.getElementById("Myan_cntdwn").innerHTML = DisplayStr;
  if (Myan_CountActive)
    setTimeout("Myan_CountBack(" + (secs+Myan_CountStepper) + ")", Myan_SetTimeOutPeriod);
}

function Myan_putspan(backcolor, forecolor) {
    if (backcolor == "transparent")
        document.write("<span id='Myan_cntdwn' style='color:" + forecolor + "'></span>");
    else
        document.write("<span id='Myan_cntdwn' style='background-color:" + backcolor + 
                       "; color:" + forecolor + "'></span>");
}

if (typeof(Myan_BackColor)=="undefined")
  Myan_BackColor = "white";
if (typeof(Myan_ForeColor)=="undefined")
  Myan_ForeColor= "black";
if (typeof(Myan_TargetDate)=="undefined")
  Myan_TargetDate = "12/31/2020 5:00 AM";
if (typeof(Myan_DisplayFormat)=="undefined")
  Myan_DisplayFormat = "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
if (typeof(Myan_CountActive)=="undefined")
  Myan_CountActive = true;
if (typeof(Myan_FinishMessage)=="undefined")
  Myan_FinishMessage = "";
if (typeof(Myan_CountStepper)!="number")
  Myan_CountStepper = -1;
if (typeof(Myan_LeadingZero)=="undefined")
  Myan_LeadingZero = true;


Myan_CountStepper = Math.ceil(Myan_CountStepper);
if (Myan_CountStepper == 0)
  Myan_CountActive = false;
var Myan_SetTimeOutPeriod = (Math.abs(Myan_CountStepper)-1)*1000 + 990;
Myan_putspan(Myan_BackColor, Myan_ForeColor);
var Myan_dthen = new Date(Myan_TargetDate);
var Myan_dnow = new Date();
if(Myan_CountStepper>0)
  Myan_ddiff = new Date(Myan_dnow-Myan_dthen);
else
  Myan_ddiff = new Date(Myan_dthen-Myan_dnow);
Myan_gsecs = Math.floor(Myan_ddiff.valueOf()/1000);
Myan_CountBack(Myan_gsecs);
