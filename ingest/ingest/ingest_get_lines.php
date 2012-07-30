<?php

require 'ingest_include.php';
require includedir . 'ingest_convert2path.php';
list($bookdir, $filename, $extension) = bfe_parameters();

$version = 'web';
if (isset($_GET['version']))
{
  $version = $_GET['version'];
  if (!in_array($version, array('web', 'web-grey')))
    die("ERROR: version mismatch: $version.");
}

$lines_dir = sprintf($lines_dir_format, $bookdir, $filename);
$line_url_base = sprintf($line_url_base_format, $bookdir, $filename, $version);

if (!is_dir($lines_dir))
{
  echo "No lines found for this page ($filename).<br>";
  echo "Maybe this page has not been ingested yet. Press commit to start the ingest procedure on this page.<br><br>";
  echo "<small>Error was: directory not found: $lines_dir</small><br>";
  die();
}

foreach (scandir($lines_dir) as $line)
{
  if (!preg_match("/\.jpg$/", $line))
    continue;
  $url = $line_url_base . '/' . $line;
  print '<a style="border: 0; display: block; margin-bottom: 5px;" href="' . $url . '" target="_blank"><img style="width: 95%; border: 1px solid #000;" src="' . $url . '" /></a>';
}
?>
