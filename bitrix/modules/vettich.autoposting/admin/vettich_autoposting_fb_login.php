<?
// ����� ������ ��������� ����������
if(!session_id()) {
	session_start();
}
require_once dirname(__FILE__).'/../classes/Facebook/autoload.php';

// App ID � App Secret �� �������� ����������
$app_id = "";
$app_secret = "";

// ������ �� �������� �������� ����� �����������
// ����� ������ ��������� � ��������� � ���������� ����������
$callback = $_SERVER['SERVER_NAME']."/bitrix/admin/vettich_autoposting_fb_callback.php";
if($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off')
	$callback = 'https://'.$callback;
else
	$callback = 'http://'.$callback;

if(isset($_GET['app_id']))
{
	$app_id = trim($_GET['app_id']);
}
if(isset($_GET['app_secret']))
{
	$app_secret = trim($_GET['app_secret']);
}
if(isset($_GET['scope']))
{
	$scope = trim($_GET['scope']);
	$scope = explode(',', $scope);
}
if(isset($_GET['callback']))
{
	if(strpos($callback, '?') === false)
	{
		$callback .= '?';
	}
	else
	{
		$callback .= '&';
	}
	$callback .= 'app_id='.$app_id.'&app_secret='.$app_secret;
}

$fb = new Facebook\Facebook([
    'app_id'  => $app_id,
    'app_secret' => $app_secret,
    'default_graph_version' => 'v2.5',
]);

$helper = $fb->getRedirectLoginHelper();

// $scope = ['publish_actions','manage_pages','publish_pages','email'];
$loginUrl = $helper->getLoginUrl($callback, $scope);

header('Location: '.($loginUrl));
session_write_close();
