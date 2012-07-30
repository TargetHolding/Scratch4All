<?php

function char_check($str)
{
  if (preg_match('/[\\\\\/;*<>]/', $str))
    exit('Parameter bookdir, filename or extension contains ilegal characters.');
}

function jpegpath($bookdir, $filename)
{
  char_check($filename);
  $jpegdir = jpegdir($bookdir);
  $jpegpath = $jpegdir . '/' . $filename . '.jpg';
  return $jpegpath;
}

function tiffpath($bookdir, $filename)
{
  char_check($filename);
  $tiffdir = tiffdir($bookdir);
  $tiffpath = $tiffdir . '/' . $filename . '.tif';
  return $tiffpath;
}

function ppmpath($bookdir, $filename)
{
  char_check($filename);
  $ppmdir = ppmdir($bookdir);
  $ppmpath = $ppmdir . '/' . $filename . '.ppm';
  return $ppmpath;
}

function jpegdir($bookdir)
{
  char_check($bookdir);
  return GLOBAL_ROOT . $bookdir . '/Jpeg';
}

function tiffdir($bookdir)
{
  char_check($bookdir);
  return GLOBAL_ROOT . $bookdir . '/Tiff';
}

function ppmdir($bookdir)
{
  char_check($bookdir);
  return GLOBAL_ROOT . $bookdir . '/Ppm';
}

?>
