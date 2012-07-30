<?php

$appid = '4dd09267';

// Use application01 while Minne still uses that, this makes it possible to
// re-use cookies.
//$distributed_login_url = 'http://monk.target.rug.nl/cgi-bin/monkwebj';
$distributed_login_url = 'http://application01.target.rug.nl/cgi-bin/monkwebj';

$ingest = 'ingest_file.php';
session_start();
if (isset($_SESSION['username']))
  exit(header('Location:' . $ingest));
if (!isset($_GET['nonce']))
  exit(header('Location:' . $distributed_login_url . '?cmd=login-api&subcmd=login&appid=' . $appid));
$token = file_get_contents($distributed_login_url . '?cmd=login-api&subcmd=request-token&appid=' . $appid . '&nonce=' . $_GET['nonce']);
if (substr_count($token, 'User not found.') == 1)
  exit(header('Location:index.php'));
$user_permissions_str = file_get_contents($distributed_login_url . '?cmd=login-api&subcmd=user-info&token=' . $token . '&appid=' . $appid);
$user_permissions = explode("\n", $user_permissions_str);
$_SESSION['username'] = $user_permissions[0];
$_SESSION['global_role'] = $user_permissions[1];
$_SESSION['token'] = $token;
exit(header('Location:' . $ingest));
?>
