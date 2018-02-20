<?php
namespace JUSTDEVELOP\TelegramBot;
define('BASE_PATH', __DIR__);
define('BASE_COMMANDS_PATH', BASE_PATH);
use JUSTDEVELOP\TelegramBot\Entities\Update;
class Telegram
{
    protected $version = '0.1.0';
    protected $api_key = '';
    protected $bot_name = '';
    protected $input;
    protected $commands_paths = array();
    protected $update;
    protected $commands_config = array();
    protected $admins_list = array();
    protected $last_command_response;

    public function __construct($api_key, $bot_name)
    {
        if (!empty($api_key) && !empty($bot_name)) {
			$this->api_key = $api_key;
			$this->bot_name = $bot_name;
			Request::initialize($this);
		}
    }
    public function getCommandsList() //@return array $commands
    {
        $commands = array();
        foreach ($this->commands_paths as $path) {
            try {
                //Get all "*Command.php" files
                $files = new \RegexIterator(
                    new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($path)
                    ),
                    '/^.+Command.php$/'
                );
                foreach ($files as $file) {
                    //Remove "Command.php" from filename
                    $command = str_replace(' ', '', mb_convert_case(str_replace('_', ' ', substr($file->getFilename(), 0, -11)), MB_CASE_TITLE, 'UTF-8'));
                    $command_name = strtolower($command);
                    if (array_key_exists($command_name, $commands))
                        continue;
                    require_once $file->getPathname();
                    $command_obj = $this->getCommandObject($command);
                    if ($command_obj instanceof Commands\Command) {
                        $commands[$command_name] = $command_obj;
                    }
                }
            } catch (\Exception $e) {}
        }

        return $commands;
    }

    public function getCommandObject($command)
    {
        $which = array('System');
        $which[] = 'User';

        foreach ($which as $auth) {
            $command_namespace = __NAMESPACE__ . '\\Commands\\' . $auth . 'Commands\\' . $this->ucfirstUnicode($command) . 'Command';
            if (class_exists($command_namespace)) {
                return new $command_namespace($this, $this->update);
            }
        }
        return null;
    }

    public function handleGetUpdates($limit = null, $timeout = null)
    {
		$offset = \COption::GetOptionString('justdevelop.morder', 'telegram_last_update') + 1;
        $response = Request::getUpdates(array(
            'offset'  => $offset,
            'limit'   => $limit,
            'timeout' => $timeout,
        ));
        if ($response->isOk()) {
            foreach ((array) $response->getResult() as $result) {
                $this->processUpdate($result);
            }
        }
        return $response;
    }

    private function getCommandFromType($type)
    {
        return $this->ucfirstUnicode(str_replace('_', '', $type));
    }
	
    public function processUpdate(Update $update) //return Entities\ServerResponse
    {
        $this->update = $update;
        $command = 'genericmessage';
        $update_type = $this->update->getUpdateType();
        if (in_array($update_type, array('inline_query', 'chosen_inline_result', 'callback_query'))) {
            $command = $this->getCommandFromType($update_type);
        } elseif ($update_type === 'message') {
            $message = $this->update->getMessage();

            $this->addCommandsPath(BASE_COMMANDS_PATH . '/UserCommands', false);

            $type = $message->getType();
            if ($type === 'command') {
                $command = $message->getCommand();
            } elseif (in_array($type, array(
                'channel_chat_created',
                'delete_chat_photo',
                'group_chat_created',
                'left_chat_member',
                'migrate_from_chat_id',
                'migrate_to_chat_id',
                'new_chat_member',
                'new_chat_photo',
                'new_chat_title',
                'supergroup_chat_created',
            ))) {
                $command = $this->getCommandFromType($type);
            }
        }

        $this->getCommandsList();

		\COption::SetOptionString('justdevelop.morder', 'telegram_last_update', $update->getUpdateId());
        return $this->executeCommand($command);
    }

    public function executeCommand($command)
    {
        $command_obj = $this->getCommandObject($command);

        if (!$command_obj) {
            $this->last_command_response = $this->executeCommand('Generic');
        } else {
            $this->last_command_response = $command_obj->preExecute();
        }
        return $this->last_command_response;
    }

    public function addCommandsPath($path, $before = true)
    {
        if (!in_array($path, $this->commands_paths)) {
            if ($before) {
                array_unshift($this->commands_paths, $path);
            } else {
                array_push($this->commands_paths, $path);
            }
        }
        return $this;
    }
	
    public function setCommandConfig($command, array $config)
    {
        $this->commands_config[$command] = $config;
        return $this;
    }
    public function getCommandConfig($command)
    {
        return isset($this->commands_config[$command]) ? $this->commands_config[$command] : array();
    }
    public function getApiKey()
    {
        return $this->api_key;
    }
    public function getBotName()
    {
        return $this->bot_name;
    }
    public function getVersion()
    {
        return $this->version;
    }
    protected function ucfirstUnicode($str, $encoding = 'UTF-8')
    {
        return mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) . mb_strtolower(mb_substr($str, 1, mb_strlen($str), $encoding), $encoding);
    }
}
use JUSTDEVELOP\TelegramBot\Entities\ServerResponse;
class Request
{
    private static $telegram;
    private static $methods = array(
        'getUpdates',
        'sendMessage'
    );
    public static function initialize(Telegram $telegram)
    {
        if (is_object($telegram)) {
            self::$telegram = $telegram;
        }
    }
	
    public static function generateGeneralFakeServerResponse(array $data = null)
    {
        $fake_response = array('ok' => true); // :)
        if (!isset($data)) {
            $fake_response['result'] = true;
        }
        if (isset($data['chat_id'])) {
            $data['message_id'] = '1234';
            $data['date'] = '1441378360';
            $data['from'] = array(
                'id'         => 123456789,
                'first_name' => 'botname',
                'username'   => 'namebot',
            );
            $data['chat'] = array('id' => $data['chat_id']);

            $fake_response['result'] = $data;
        }
        return $fake_response;
    }

	public static function executeCurl($action, array $data = null)
    {
        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL            => 'https://api.telegram.org/bot' . self::$telegram->getApiKey() . '/' . $action,
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            //CURLOPT_SAFE_UPLOAD    => true,
        );

        if (!empty($data)) {
            $curlConfig[CURLOPT_POSTFIELDS] = $data;
        }

        curl_setopt_array($ch, $curlConfig);
        $result = curl_exec($ch);

        curl_close($ch);
        return $result;
    }

    public static function send($action, array $data = null)
    {
        $bot_name = self::$telegram->getBotName();

        if (defined('PHPUNIT_TESTSUITE')) {
            $fake_response = self::generateGeneralFakeServerResponse($data);
            return new ServerResponse($fake_response, $bot_name);
        }
        $response = json_decode(self::executeCurl($action, $data), true);
		//ignore media messages 
		$ignore_array=array('forward_from', 'forward_from_chat', 'reply_to_message', 'audio', 'document', 'photo', 'sticker', 'video', 'voice', 'caption', 'contact', 'location', 'venue', 'new_chat_member', 'left_chat_member', 'new_chat_title', 'new_chat_photo', 'delete_chat_photo', 'group_chat_created', 'supergroup_chat_created', 'channel_chat_created', 'migrate_to_chat_id', 'migrate_from_chat_id', 'pinned_message');
		foreach($response[result] as $key=>$message){
			if (!isset($message['message'])) unset($response[result][$key]);
			foreach($ignore_array as $type)
				if(isset($message['message'][$type]))
					unset($response[result][$key]);
		}
        return new ServerResponse($response, $bot_name);
    }

    public static function sendMessage(array $data)
    {
        $text = $data['text'];
        $string_len_utf8 = mb_strlen($text, 'UTF-8');
        if ($string_len_utf8 > 4096) {
            $data['text'] = mb_substr($text, 0, 4096);
            $result = self::send('sendMessage', $data);
            $data['text'] = mb_substr($text, 4096, $string_len_utf8);
            return self::sendMessage($data);
        }
        return self::send('sendMessage', $data);
    }

    public static function getUpdates(array $data)
    {
        return self::send('getUpdates', $data);
    }

    public static function emptyResponse()
    {
        return new ServerResponse(array('ok' => true, 'result' => true), null);
    }
}