<HTML LANG="en">
<HEAD>
<TITLE>Simulador Guaran&iacute; I</TITLE>
<style type="text/css">
.ajaxDashboard {
    font-size: 10px;
        font-family: Verdana, Arial, Helvetica, sans-serif;
}
.ajaxDashboard .datahead {
        font-size: 10px;
        font-weight: bold;
        color:  white;
        background-color: #C4CC66;
        text-align: center;
}
.ajaxDashboard .datahead2 {
        font-size: 10px;
        font-weight: bold;
	border-bottom: 0px solid #C4CC66;
        color:  white;
        background-color: #3173B1;
        text-align: center;
}
.ajaxDashboard .data1 {
         color: black;
         font-size: 10px;
         border-bottom: 1px solid #CCCCCC;
         background-color: rgb(196, 204, 102);
         text-align: left;
}
.ajaxDashboard .data2 {
         color: black;
         font-size: 10px;
         background-color: rgb(196, 204, 102);
         text-align: left;
}
.ajaxDashboard td {
         border: none;
         background-color:rgb(196, 204, 102);
}
</style>
</HEAD>
<body>

<?php
$SITE = array();  // required for non-Saratoga template use
global $SITE;

// Customize this list with your nearby METARs by
// using http://saratoga-weather.org/wxtemplates/find-metar.php to create the list below

$MetarList = array( // set this list to your local METARs 
// Metar(ICAO) | Name of station | dist-mi | dist-km | direction |
  'SAAP|Parana, Entre Rios|9|14|N|' // lat=37.4000,long=-122.0500, elev=12, dated=28-FEB-12

// list generated Wed, 09-Jan-2013 4:39pm PST at http://saratoga-weather.org/wxtemplates/find-metar.php
);
$maxAge = 75*60; // max age for metar in seconds = 75 minutes
#
$SITE['cacheFileDir']   =  '../cache/';   // directory to use for scripts cache files .. use './' for doc.root.dir
$SITE['tz'] 			= 'America/Argentina/Buenos_Aires'; //NOTE: this *MUST* be set correctly to
// translate UTC times to your LOCAL time for the displays.
//  http://us.php.net/manual/en/timezones.php  has the list of timezone names
//  pick the one that is closest to your location and put in $SITE['tz'] like:
//    $SITE['tz'] = 'America/Los_Angeles';  // or
//    $SITE['tz'] = 'Europe/Brussels';
$SITE['timeFormat'] = 'D, d-M-Y g:ia T';  // Day, 31-Mar-2006 6:35pm Tz  (USA Style)
//$SITE['latitude']		= '37.27153397';    //North=positive, South=negative decimal degrees
//$SITE['longitude']		= '-122.02274323';  //East=positive, West=negative decimal degrees

$condIconDir = '../metar-images/';  // directory for metar-images with trailing slash
$SITE['fcsticonstype'] = '.jpg'; // default type='.jpg' 
#                                // use '.gif' for animated icons from # http://www.meteotreviglio.com/
$SITE['uomTemp'] = '&deg;F';  // ='&deg;C', ='&deg;F'
$SITE['uomBaro'] = ' inHg';   // =' hPa', =' inHg'
$SITE['uomWind'] = ' mph';    // =' km/h', =' mph'
$SITE['uomRain'] = ' in';     // =' mm', =' in'
$SITE['uomDistance'] = ' mi'; // =' mi' or =' km'
// end of customizations
#
# utility functions .. you don't need to change these
// Wind Rose graphic in ajaxwindiconwr as wrName . winddir . wrType
$wrName   = 'wr-';       // first part of the graphic filename (followed by winddir to complete it)
$wrType   = '.png';      // extension of the graphic filename
$wrHeight = '58';        // windrose graphic height=
$wrWidth  = '58';        // windrose graphic width=
$wrCalm   = 'wr-calm.png';  // set to full name of graphic for calm display ('wr-calm.gif')
$Lang = 'en'; // default language used (for Windrose display)
$SITE['lang'] = $Lang;
if (!function_exists('date_default_timezone_set')) {
   putenv("TZ=" . $SITE['tz']);
  } else {
   date_default_timezone_set($SITE['tz']);
 }
function langtrans ( $str ) { echo $str; return; }
function langtransstr ($str) { return($str); }
$time = date('H:i');
//$sunrise = date_sunrise(time(), SUNFUNCS_RET_STRING, $SITE['latitude'], $SITE['longitude']);
//$sunset  = date_sunset(time(), SUNFUNCS_RET_STRING, $SITE['latitude'], $SITE['longitude']);
$sun_info = date_sun_info(time(),$SITE['latitude'], $SITE['longitude']);
$sunrise = date('H:i',$sun_info['sunrise']);
$sunset  = date('H:i',$sun_info['sunset']);
print "<!-- time='$time' sunrise='$sunrise' sunset='$sunset' latitude='".$SITE['latitude']."' longitude='".$SITE['longitude']."' -->\n";
# end of utility functions
?>

<?php


  if(file_exists("include-metar-display.php")) {
	  include_once("include-metar-display.php");
      //print "<p><small>Metar display script from <a href=\"http://saratoga-weather.org/scripts-metar.php#metar\">Saratoga-Weather.org</a></small></p>\n";
  } else {
	  print "<p>Sorry.. include-metar-display.php not found</p>\n";
  }
?>
</body>
</HTML>