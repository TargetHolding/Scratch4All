<?php

function compute_request($bookdir, $filename, $extension)
{
  if ($extension === 'tif')
  {
    if (!is_file(tiffpath($bookdir, $filename)))
      return;
  }
  else if ($extension === 'ppm')
  {
    if (!is_file(ppmpath($bookdir, $filename)))
      return;
  }
  else if ($extension === 'jpg')
  {
    if (!is_file(jpegpath($bookdir, $filename)))
      return;
  }
  $compute_requests_dir = "/target/gpfs2/monk/Locks+Logs/Workers/Requests";
  $timestr = strftime("%a-%b-%d-%H:%M:%S-CEST-%Y");
  if (substr_count($_SERVER['HTTP_USER_AGENT'], 'Windows'))
    $timestr = strftime("%a-%b-%d-%H.%M.%S-CEST-%Y");
  $uname = "ingest-user";
  $mkrandhex = exec(mkrandhexdir . '/mkrandhex 20');
  if (strlen($mkrandhex) != 20)
    $mkrandhex = substr(md5(rand()), 0, 20);
  $req_filename = $compute_requests_dir . '/' . $uname . '-at-' . $timestr . '-jobid-' . $mkrandhex;
  $bookroot = "/target/gpfs2/monk/Ingest/testbooks";
  $page = intval(substr($filename, -4)) - 1;
  $page_scan = $page + 1;
  $path_info = pathinfo($filename);
  $contents = "Ingest -root $bookroot -book $bookdir -type $extension -side right -firstscan $page_scan -firstpage $page -prepro KdK-1898 -user $uname -time $timestr";
  if (!is_dir($compute_requests_dir))
  {
    if (!@mkdir($compute_requests_dir, 0666, true))
      exit('ERROR: Make directory: "' . $compute_requests_dir . '" failed');
    chmod($compute_requests_dir, 0660);
  }
  if (!$fp = @fopen($req_filename, "w"))
    exit('ERROR: Make/open file: "' . $req_filename . '" failed');
  fwrite($fp, $contents . "\n");
  fclose($fp);
  chmod($req_filename, 0660);
}

?>
