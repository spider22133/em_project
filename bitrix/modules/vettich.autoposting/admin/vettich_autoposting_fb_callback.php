<?
if(!session_id()) {
  session_start();
}

require_once dirname(__FILE__).'/../classes/Facebook/autoload.php';

$app_id = "";
$app_secret = "";

if(isset($_GET['app_id']))
{
  $app_id = trim($_GET['app_id']);
}
if(isset($_GET['app_secret']))
{
  $app_secret = trim($_GET['app_secret']);
}

$fb = new Facebook\Facebook([
  'app_id'  => $app_id,
  'app_secret' => $app_secret,
  'default_graph_version' => 'v2.5',
]);

$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "<br/>\n";
    echo "Error Code: " . $helper->getErrorCode() . "<br/>\n";
    echo "Error Reason: " . $helper->getErrorReason() . "<br/>\n";
    echo "Error Description: " . $helper->getErrorDescription() . "<br/>\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}


?>
<script type="text/javascript">
	if(window.opener)
	{
		if(window.opener.vettich_fb_access_token('<?=$accessToken->getValue()?>'))
		{
			window.close();
		}
	}
</script>
<?
session_write_close();
