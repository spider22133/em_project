<?php
namespace JUSTDEVELOP\TelegramBot\Commands;
use JUSTDEVELOP\TelegramBot\Request;
use JUSTDEVELOP\TelegramBot\Telegram;
use JUSTDEVELOP\TelegramBot\Entities\Chat;
use JUSTDEVELOP\TelegramBot\Entities\Update;
abstract class Command
{
    protected $telegram;
    protected $update;
    protected $message;
    protected $name = '';
    protected $description = 'Command description';
    protected $usage = 'Command usage';
    protected $version = '1.0.0';
    protected $enabled = true;
    protected $config = array();
    public function __construct(Telegram $telegram, Update $update = null)
    {
        $this->telegram = $telegram;
        $this->setUpdate($update);
        $this->config = $telegram->getCommandConfig($this->name);
    }
    public function setUpdate(Update $update = null)
    {
        if (!empty($update)) {
            $this->update = $update;
            $this->message = $this->update->getMessage();
        }
        return $this;
    }
   public function preExecute()
    {
        return $this->execute();
    }
    abstract public function execute();

    public function getUpdate()
    {
        return $this->update;
    }
    public function getMessage()
    {
        return $this->message;
    }

    public function getConfig($name = null)
    {
        if ($name === null) {
            return $this->config;
        }
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }
        return null;
    }
}
abstract class UserCommand extends Command
{

}

