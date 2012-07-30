<?php

require 'ingest_include.php';
require includedir . 'ingest_convert2path.php';
list($bookdir, $filename, $extension) = bfe_parameters();

if (is_file(sprintf($done_file_format, $bookdir, $filename)))
  print 1;
else
  print 0;
