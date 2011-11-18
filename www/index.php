<?php
/* set up base dir */
$basePath = realpath(dirname(__FILE__) . '/../');
set_include_path(get_include_path() . PATH_SEPARATOR . $basePath);

/* load dependencies */
require_once('src/dependency.php');
date_default_timezone_set('America/Los_Angeles');

/* support multiple feeds */
if (! isset($_GET['feed'])) {
    header('Missing feed', true, 400);
    exit;
}

$url    = '';
$feed   = $_GET['feed'];
switch ($feed) {
    case 'wd':
        $url = 'http://www.wdcdn.net/rss/presentation/library/client/iowa/id/128b053b916ea1f7f20233e8a26bc45d'; 
        break;
}
if (empty($url)) {
    header('Missing feed', true, 400);
    exit;
}


$config = array(
    'feedUrl'   => $url,
);
$feedManager = new Feed_Manager($config);
echo $feedManager->process();
