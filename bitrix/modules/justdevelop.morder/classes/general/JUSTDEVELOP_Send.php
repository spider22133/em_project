<?
IncludeModuleLangFile(__FILE__);
require __DIR__ . '/Telegram.php';
require __DIR__ . '/Entity.php';
require __DIR__ . '/Command.php';
class JUSTDEVELOP_Send
{
		
	function __construct() {
		
	}
	public function Send_SMS($chid, $message, $translit = 0, $time = 0, $format = 0, $sender = false, $encoding = LANG_CHARSET)
	{
		
		$API_KEY = COption::GetOptionString('justdevelop.morder', 'password');
		$BOT_NAME = COption::GetOptionString('justdevelop.morder', 'login');
		
		
		try {
		    // Create Telegram API object
		    $telegram = new JUSTDEVELOP\TelegramBot\Telegram($API_KEY, $BOT_NAME);
		
        	$message = iconv($encoding, "UTF-8", $message);
			
			
			$data = array(
		            'chat_id' => $chid,
		            'action'    => 'typing',
		            'text'    => $message
		        );
		
		        $r = new JUSTDEVELOP\TelegramBot\Request;
				$r->sendMessage($data);
			
		} catch (\Exception $e) {}
	}
}