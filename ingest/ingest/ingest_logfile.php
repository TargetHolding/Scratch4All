<?php

require 'ingest_include.php';
list($bookdir, $filename, $extension) = bfe_parameters();

define('log_file', bashpath . $bookdir . '.sh.log');

if (file_exists(log_file))
  print file_get_contents(log_file);
?>
