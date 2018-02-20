<?php
namespace JUSTDEVELOP\TelegramBot\Commands\UserCommands;
use JUSTDEVELOP\TelegramBot\Commands\UserCommand;
use JUSTDEVELOP\TelegramBot\Request;
class IdCommand extends UserCommand
{
    protected $name = 'id';
    protected $description = 'Show current chat ID';
    protected $usage = '/id';
    protected $version = '1.0.0';
    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text = trim($message->getText(true));
        $data = array(
            'chat_id' => $chat_id,
            'text'    => 'Current chat id: ' . $chat_id,
        );
        return Request::sendMessage($data);
    }
}
