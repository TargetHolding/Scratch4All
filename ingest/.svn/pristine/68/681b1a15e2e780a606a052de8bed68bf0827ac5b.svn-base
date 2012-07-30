<?php
require 'ingest_include.php';
require includedir . 'ingest_convert2path.php';
list($bookdir, $filename, $extension) = bfe_parameters();

$bashfile = sprintf($bashfile_format ,$bookdir);
$apath = jpegpath($bookdir, $filename);
if (!is_file($apath))
  exit('ERROR: Path not valid');

$imagesize = getimagesize($apath);
$resx = $imagesize[0];
$resy = $imagesize[1];

// prepare default values
$left_softleft = 0;
$left_softright = 0;
$left_softtop = 0;
$left_softbottom = 0;

if (is_file(ingest_params))
{
  $ingest_params = file_get_contents(ingest_params);
  $ingest_params_lines = explode("\n", $ingest_params);
  foreach ($ingest_params_lines as $line)
  {
    $lineparts = explode("\t", $line);
    if (isset($lineparts[0]) && isset($lineparts[1]))
    {
      if ($lineparts[0] == 'left_softleft')
        $left_softleft = $lineparts[1];
      if ($lineparts[0] == 'left_softright')
        $left_softright = $lineparts[1];
      if ($lineparts[0] == 'left_softtop')
        $left_softtop = $lineparts[1];
      if ($lineparts[0] == 'left_softbottom')
        $left_softbottom = $lineparts[1];
    }
  }
}

if (is_file($bashfile))
{
  $bash = file_get_contents($bashfile);
  $lines = explode("\n", $bash);
  foreach ($lines as $line)
  {
    $lineparts = explode("=", $line);
    if (isset($lineparts[0]) && isset($lineparts[1]))
    {
      if ($lineparts[0] == 'left_softleft')
        $left_softleft = $lineparts[1];
      if ($lineparts[0] == 'left_softright')
        $left_softright = $lineparts[1];
      if ($lineparts[0] == 'left_softtop')
        $left_softtop = $lineparts[1];
      if ($lineparts[0] == 'left_softbottom')
        $left_softbottom = $lineparts[1];
    }
  }
}

// Boundaries
if ($left_softleft + $left_softright > $resx)
{
  $left_softleft = 0;
  $left_softright = 0;
}
if ($left_softtop + $left_softbottom > $resx)
{
  $left_softtop = 0;
  $left_softbottom = 0;
}

// Left, right, top, bottom to x, y, w, h.
$x = $left_softleft;
$y = $left_softtop;
$w = $resx - ($left_softleft + $left_softright);
$h = $resy - ($left_softtop + $left_softbottom);
?>

<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN'
  'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>

<html xmlns='http://www.w3.org/1999/xhtml'>
  <head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
    <title>:: Monk.Ingest ::</title>
    <link rel='stylesheet' type='text/css' href='css/yui.2.8.1.css' />
    <link rel='stylesheet' type='text/css' href='css/jquery.jcrop.css' />
    <link rel='stylesheet' type='text/css' href='css/monk.ingest.css' />
    <script type='text/javascript' src='js/lib/jquery-1.4.4.min.js'></script>
    <!--<script type='text/javascript' src='js/lib/jquery-ui-1.8.9.custom.min.js'></script>-->
    <script type='text/javascript' src='js/lib/jquery.jcrop.min.js'></script>
    <script type='text/javascript' src='js/lib/of.common.js'></script>
    <script type='text/javascript' src='js/lib/of.event.js'></script>
    <script type='text/javascript' src='js/monk.ingest.js'></script>
    <script type='text/javascript'>
      (function ($) 
      {
        OF.config = 
          {
          log: false
        };
        $(window).load(function () 
        {
          $('.image-holder img').ingest(
          {
            x: <?php print (int) $x ?>,
            y: <?php print (int) $y ?>,
            w: <?php print (int) $w ?>,
            h: <?php print (int) $h ?>
          });
        });
      }(jQuery));
    </script>
  </head>
  <body id='ingest'>

    <noscript>
      <p>javascript required</p>
    </noscript>

    <div class='header'></div>

    <div class='container'>

      <div class='image-holder radius'>
        <div class='shadow'>
          <img src='<?php print sprintf($orginal_page_format, $bookdir, $filename); ?>' alt='' title='' />
        </div>
        <div class='text-holder radius-small'>
          <form action="ingest_fields.php" method="get">
            <label>X</label><input type='text' name='dimX' value='0' />
            <label>Y</label><input type='text' name='dimY' value='0' />
            <label>W</label><input type='text' name='dimW' value='0' />
            <label>H</label><input type='text' name='dimH' value='0' />
            <input style="margin-left:15px;" checked type="radio" name="page" value="left" /><label>Left page</label>
            <input style="margin-left:15px;" type="radio" name="page" value="right" ><label>Right page</label>
            <input type="hidden" name="resX" value="<?php print $resx; ?>">
            <input type="hidden" name="resY" value="<?php print $resy; ?>">
            <input type="hidden" name="bookdir" value="<?php print $bookdir; ?>">
            <input type="hidden" name="filename" value="<?php print $filename; ?>">
            <input type="hidden" name="extension" value="<?php print $extension; ?>">
            <input style="margin-left:15px;" type="submit" value="Submit" />
          </form>
        </div>
      </div>

    </div>

    <div id='footer'>
      <img src='css/images/logo/nationaal-archief.jpg' alt='Nationaal Archief' />
      <img src='css/images/logo/targetlogo.png' alt='Target' />
      <img src='css/images/logo/rug.png' alt='RUG' />
      <img src='css/images/logo/ontwikkel-logo.png' alt='Ontwikkelfabriek' />
      <img src='css/images/logo/logo-NWO.jpg' alt='NWO' />
    </div>

  </body>
</html>