<?php

session_start();
if (!isset($_SESSION['username']))
  exit(header('Location:index.php'));

/* quick hack by JP to disallow others */
$allowed = array(
    '127.0.0.1', /* anco local */
    '129.125.178.175', /* anco ai */
    '129.125.178.49', /* JP ai */
    '82.73.236.101', /* JP at home */
    '129.125.178.72', /* Lambert at ai */
);
//  if (!in_array($_SERVER['REMOTE_ADDR'], $allowed))
//    exit('not allowed from ' . $_SERVER['REMOTE_ADDR']);

/* Adjust error reporting for debugging purpose */
error_reporting(-1);

define('GLOBAL_ROOT', '/srv/www/htdocs/ingest/testbooks/');
define('includedir', '../include/');
define('mkrandhexdir', '/target/gpfs2/monk/bin/');
define('bashpath', '/target/gpfs2/monk/Ingest/params/');
define('ingest_params', includedir . 'ingest_params.tsv');

$server_addr = 'http://' . $_SERVER['SERVER_ADDR'] . '/';
if ($server_addr == 'http://127.0.0.1/')
  $server_addr = 'http://monk.target.rug.nl/';

$booksdir = $server_addr . 'ingest/testbooks/';
$bashfile_format = bashpath . '%s.sh'; // $bookdir
$diagnostics_url_format = $booksdir . '%s/Diag/%s/'; //$bookdir, $filename
$orginal_page_format = $booksdir . '%s/Jpeg/%s.jpg'; //$bookdir, $filename
$done_file_format = GLOBAL_ROOT . '%s/Done/%s'; //$bookdir, $filename
$lines_dir_format = GLOBAL_ROOT . '%s/Pages/%s/Lines/web/'; //$bookdir, $filename
$line_url_base_format = $booksdir . '%s/Pages/%s/Lines/%s'; //$bookdir, $filename, $version
$filetypes = array('tif', 'jpg', 'ppm');
$foldertypes = array('tiff', 'jpeg', 'ppm');
if (!file_exists(ingest_params))
  exit('ERROR: Required file: "' . ingest_params . '" is missing.');

function bfe_parameters()
{
  if (isset($_GET['bookdir']) && strlen($_GET['bookdir']))
    $bookdir = $_GET['bookdir'];
  else
    exit('ERROR: Parameter: "bookdir" is not set.');
  if (isset($_GET['filename']) && strlen($_GET['filename']))
    $filename = $_GET['filename'];
  else
    exit('ERROR: Parameter: "filename" is not set.');
  if (isset($_GET['extension']) && strlen($_GET['extension']))
    $extension = $_GET['extension'];
  else
    exit('ERROR: Parameter: "extension" is not set.');
  return array($bookdir, $filename, $extension);
}

function get($str)
{
  if (isset($_GET[$str]) && strlen($_GET[$str]))
  {
    if (is_numeric($_GET[$str]))
      return (float)$_GET[$str];
    else if ($_GET[$str] === 'true')
      return true;
    else if ($_GET[$str] === 'false')
      return false;
    else
      return $_GET[$str];
  }
  return null;
}

function set($str)
{
  if (isset($_GET[$str]) && strlen($_GET[$str]))
    return true;
  return false;
}

?>
