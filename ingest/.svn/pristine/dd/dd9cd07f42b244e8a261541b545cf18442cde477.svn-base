<?php

require 'ingest_include.php';
require includedir . 'ingest_convert2path.php';
require 'ingest_compute_request.php';
list($bookdir, $filename, $extension) = bfe_parameters();

if (!is_file(jpegpath($bookdir, $filename)) && !is_file(tiffpath($bookdir, $filename)) && !is_file(ppmpath($bookdir, $filename)))
  exit('ERROR: The bookdir: "' . $bookdir . '" and filename: "' . $filename . '" combination does not have a tiff, jpeg or ppm file');

if (set('compute_request'))
{
  if (set('new_random_sample') === false && is_file(sprintf($done_file_format, $bookdir, $filename)))
    if (!unlink(sprintf($done_file_format, $bookdir, $filename)))
      exit('ERROR: unable to remove Done-file [' . sprintf($done_file_format, $bookdir, $filename) . ']');
  compute_request($bookdir, $filename, $extension);
}

$bashfile = sprintf($bashfile_format, $bookdir);
$bash = bashfile2array();
$bash_initial = $bash;
$params = file_to_params(ingest_params, "\t", 0);
$warning = '';

make_bashpath();
process_random_sample();
process_fields($params);
write_log();
write_bash();

print file_get_contents($bashfile) . $warning;

function make_bashpath()
{
  if (!is_dir(bashpath))
  {
    if (!@mkdir(bashpath, 0777, true))
      exit('ERROR: Make directory: "' . bashpath . '" failed');
    chmod(bashpath, 0770);
  }
}

function bashfile2array()
{
  global $bashfile;
  $bash = array();
  $lines = file_to_lines($bashfile);
  foreach ($lines as $line)
  {
    $lineparts = explode('=', $line);
    if (isset($lineparts[0]) && isset($lineparts[1]))
      $bash[$lineparts[0]] = $lineparts[1];
  }
  return $bash;
}

function file_to_lines($file)
{
  $lines = array();
  if (file_exists($file))
    $lines = explode("\n", file_get_contents($file));
  return $lines;
}

function file_to_params($file, $delimiter, $itemno)
{
  $params = array();
  $lines = file_to_lines($file);
  foreach ($lines as $line)
  {
    $line_parts = explode($delimiter, $line);
    if ($line_parts[$itemno])
      $params[] = $line_parts[$itemno];
  }
  return $params;
}

function process_fields($params)
{
  global $bash;
  global $warning;
  foreach ($params as $param)
  {
    if (set($param))
    {
      if (preg_match('/[^a-zA-Z0-9_.\- ]/', get('param')))
        $warning .= 'WARNING: "' . $param . '" contains illegal characters' . "\n";
      else
        $bash[$param] = quote_text(get($param));
    }
  }
}

function process_random_sample()
{
  global $bash;
  global $bookdir;
  global $filename;
  global $extension;
  global $warning;
  global $filetypes;
  // reset when requested.
  if (set('new_random_sample'))
    unset($bash['RANDOM_SAMPLE']);
  // append existing RANDOM_SAMPLE with actual navisid.
  if (array_key_exists('RANDOM_SAMPLE', $bash))
  {
    if (substr_count($bash['RANDOM_SAMPLE'], $filename) === 0)
      $bash['RANDOM_SAMPLE'] = quote_text($bash['RANDOM_SAMPLE'] . ' ' . $filename);
  }
  //write actual RANDOM_SAMPLE + 8 random generated.
  else
  {
    if ($extension === 'tif')
    {
      if (is_readable(tiffdir($bookdir)))
        $scandir = scandir(tiffdir($bookdir));
    }
    else if ($extension === 'ppm')
    {
      if (is_readable(ppmdir($bookdir)))
        $scandir = scandir(ppmdir($bookdir));
    }
    else if ($extension === 'jpg')
    {
      if (is_readable(jpegdir($bookdir)))
        $scandir = scandir(jpegdir($bookdir));
    }
    else
    {
      $warning = 'WARNING: Creating random samples failed. Source directory is not available.' . "\n";
      $bash['RANDOM_SAMPLE'] = quote_text($filename);
      return;
    }
    $s = array();
    foreach ($scandir as $dirobj)
    {
      $pathinfo = pathinfo($dirobj);
      if (in_array(strtolower($pathinfo['extension']), $filetypes))
      {
        $e = explode('_', $pathinfo['filename']);
        $n = $e[count($e) - 1];
        if (ctype_digit($n))
          $s[] = $pathinfo['filename'];
      }
    }
    $samples_count = count($s);
    if ($samples_count < 9)
    {
      $warning = 'WARNING: Creating random samples failed. Source directory has less than 9 source files with valid pagenumber.' . "\n";
      $bash['RANDOM_SAMPLE'] = quote_text($filename);
    }
    else
    {
      $r = array(0 => array_search($filename, $s));
      while (count($r) < 9)
        if (!in_array($rand = rand(0, $samples_count), $r) && array_key_exists($rand, $s))
          $r[] = $rand;
      sort($r);
      $random_sample = $s[$r[0]] . ' ' . $s[$r[1]] . ' ' . $s[$r[2]] . ' ' . $s[$r[3]] . ' ' . $s[$r[4]] . ' ' . $s[$r[5]] . ' ' . $s[$r[6]] . ' ' . $s[$r[7]] . ' ' . $s[$r[8]];
      $bash['RANDOM_SAMPLE'] = quote_text($random_sample);
    }
  }
}

function quote_text($value)
{
  if (is_numeric($value))
    return str_replace('"', '', $value);
  return '"' . str_replace('"', '', $value) . '"';
}

function write_log()
{
  global $bashfile;
  if (file_exists($bashfile))
  {
    global $bash;
    global $bash_initial;
    $diff = array_diff_assoc($bash, $bash_initial);
    $changelog = '';
    foreach ($diff as $key => $value)
    {
      if (isset($bash_initial[$key]))
        $changelog .= date('d-m-Y H:i:s', time()) . "\t" . $key . "\t" . $bash_initial[$key] . "\t" . $value . "\n";
    }
    $logfile = $bashfile . '.log';
    $logfile_contents = file_exists($logfile) ? file_get_contents($logfile) : '';
    file_put_contents($logfile, $changelog . $logfile_contents);
    chmod($logfile, 0660);
  }
}

function write_bash()
{
  global $bashfile;
  global $bash;
  $sbash = '';
  foreach ($bash as $key => $value)
    $sbash .= $key . '=' . $value . "\n";
  if (@file_put_contents($bashfile, $sbash) === false)
    exit('ERROR: Writing: "' . $bashfile . '" failed.');
  chmod($bashfile, 0660);
}

?>
