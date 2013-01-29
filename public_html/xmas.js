/*
Author: Robert Hashemian
http://www.hashemian.com/

You can use this code in any manner so long as the author's
name, Web address and this disclaimer is kept intact.
********************************************************
Usage Sample:

<script language="JavaScript">
Xmas_TargetDate = "12/31/2020 5:00 AM";
Xmas_BackColor = "palegreen";
Xmas_ForeColor = "navy";
Xmas_CountActive = true;
Xmas_CountStepper = -1;
Xmas_LeadingZero = true;
Xmas_DisplayFormat = "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
Xmas_FinishMessage = "It is finally here!";
</script>
<script language="JavaScript" src="http://scripts.hashemian.com/js/countdown.js"></script>
*/

function Xmas_calcage(secs, num1, num2) {
  s = ((Math.floor(secs/num1))%num2).toString();
  if (Xmas_LeadingZero && s.length < 2)
    s = "0" + s;
  return "<b>" + s + "</b>";
}

function Xmas_CountBack(secs) {
  if (secs < 0) {
    document.getElementById("Xmas_cntdwn").innerHTML = Xmas_FinishMessage;
    return;
  }
  DisplayStr = Xmas_DisplayFormat.replace(/%%D%%/g, Xmas_calcage(secs,86400,100000));
  DisplayStr = DisplayStr.replace(/%%H%%/g, Xmas_calcage(secs,3600,24));
  DisplayStr = DisplayStr.replace(/%%M%%/g, Xmas_calcage(secs,60,60));
  DisplayStr = DisplayStr.replace(/%%S%%/g, Xmas_calcage(secs,1,60));

  document.getElementById("Xmas_cntdwn").innerHTML = DisplayStr;
  if (Xmas_CountActive)
    setTimeout("Xmas_CountBack(" + (secs+Xmas_CountStepper) + ")", Xmas_SetTimeOutPeriod);
}

function Xmas_putspan(backcolor, forecolor) {
    if (backcolor == "transparent")
        document.write("<span id='Xmas_cntdwn' style='color:" + forecolor + "'></span>");
    else
        document.write("<span id='Xmas_cntdwn' style='background-color:" + backcolor + 
                       "; color:" + forecolor + "'></span>");
}

if (typeof(Xmas_BackColor)=="undefined")
  Xmas_BackColor = "white";
if (typeof(Xmas_ForeColor)=="undefined")
  Xmas_ForeColor= "black";
if (typeof(Xmas_TargetDate)=="undefined")
  Xmas_TargetDate = "12/31/2020 5:00 AM";
if (typeof(Xmas_DisplayFormat)=="undefined")
  Xmas_DisplayFormat = "%%D%% Days, %%H%% Hours, %%M%% Minutes, %%S%% Seconds.";
if (typeof(Xmas_CountActive)=="undefined")
  Xmas_CountActive = true;
if (typeof(Xmas_FinishMessage)=="undefined")
  Xmas_FinishMessage = "";
if (typeof(Xmas_CountStepper)!="number")
  Xmas_CountStepper = -1;
if (typeof(Xmas_LeadingZero)=="undefined")
  Xmas_LeadingZero = true;


Xmas_CountStepper = Math.ceil(Xmas_CountStepper);
if (Xmas_CountStepper == 0)
  Xmas_CountActive = false;
var Xmas_SetTimeOutPeriod = (Math.abs(Xmas_CountStepper)-1)*1000 + 990;
Xmas_putspan(Xmas_BackColor, Xmas_ForeColor);
var Xmas_dthen = new Date(Xmas_TargetDate);
var Xmas_dnow = new Date();
if(Xmas_CountStepper>0)
  Xmas_ddiff = new Date(Xmas_dnow-Xmas_dthen);
else
  Xmas_ddiff = new Date(Xmas_dthen-Xmas_dnow);
Xmas_gsecs = Math.floor(Xmas_ddiff.valueOf()/1000);
Xmas_CountBack(Xmas_gsecs);
