<?php
namespace JUSTDEVELOP\TelegramBot\Commands\UserCommands;

use JUSTDEVELOP\TelegramBot\Commands\UserCommand;
use JUSTDEVELOP\TelegramBot\Request;
class StartCommand extends UserCommand
{
    protected $name = 'start';
    protected $description = 'Start command';
    protected $usage = '/start';
    protected $version = '1.0.1';

    public function execute()
    {
        $message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
        $text = 'Hi there!' . "\n" . 'Current chat id: '.$chat_id;
        $data = array(
            'chat_id' => $chat_id,
            'text'    => $text,
        );
        return Request::sendMessage($data);
    }
}
