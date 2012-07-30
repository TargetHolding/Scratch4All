<?php

require 'ingest_include.php';
require includedir . 'ingest_convert2path.php';
require 'ingest_compute_request.php';
list($bookdir, $filename, $extension) = bfe_parameters();
compute_request($bookdir, $filename, $extension);
exit($filename);

?>
