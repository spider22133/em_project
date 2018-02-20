<?php
namespace JUSTDEVELOP\TelegramBot\Entities;
class Entity
{
    protected $bot_name;


    public function getBotName()
    {

        return $this->bot_name;
    }

    public function toJson()
    {
        $fields = $this->reflect($this);
        $json = json_encode($fields);

        return $json;
    }

    public function reflect($object = null)
    {
        if ($object == null) {
            $object = $this;
        }

        $reflection = new \ReflectionObject($object);
        $properties = $reflection->getProperties();

        $fields = array();

        foreach ($properties as $property) {
            $name = $property->getName();
            if ($name == 'bot_name') {
                continue;
            }

            if (!$property->isPrivate()) {
                $array_of_obj = false;
                $array_of_array_obj = false;
                if (is_array($object->$name)) {
                    $array_of_obj = true;
                    $array_of_array_obj = true;
                    foreach ($object->$name as $elm) {
                        if (!is_object($elm)) {
                            $array_of_obj = false;
                        }
                        if (is_array($elm)) {
                            foreach ($elm as $more_net) {
                                if (!is_object($more_net)) {
                                    $array_of_array_obj = false;
                                }
                            }
                        }
                    }
                }

                if (is_object($object->$name)) {
                    $fields[$name] = $this->reflect($object->$name);
                } elseif ($array_of_obj) {
                    foreach ($object->$name as $elm) {
                        $fields[$name][] = $this->reflect($elm);
                    }
                } elseif ($array_of_array_obj) {
                    foreach ($object->$name as $elm) {
                        $temp = null;
                        foreach ($elm as $obj) {
                            $temp[] = $this->reflect($obj);
                        }
                        $fields[$name][] = $temp;
                    }
                } else {
                    $property->setAccessible(true);
                    $value = $property->getValue($object);
                    if (is_null($value)) {
                        continue;
                    }
                    $fields[$name] = $value;
                }
            }
        }
        return $fields;
    }

    public function __toString()
    {
        return $this->toJson();
    }
}
class Chat extends Entity
{
    protected $id;
    public function __construct(array $data)
    {
        $this->id = isset($data['id']) ? $data['id'] : null;
        if (isset($data['type'])) {
            $this->type = $data['type'];
        } else {
            if ($this->id > 0) {
                $this->type = 'private';
            } elseif ($this->id < 0) {
                $this->type = 'group';
            } else {
                $this->type = null;
            }
        }
    }
    public function getId()
    {
        return $this->id;
    }
}
class MessageEntity extends Entity
{
    protected $type;
    protected $offset;
    protected $length;
    protected $url;

    public function __construct(array $data)
    {
        $this->type = isset($data['type']) ? $data['type'] : null;
        $this->offset = isset($data['offset']) ? $data['offset'] : null;
        $this->length = isset($data['length']) ? $data['length'] : null;
        $this->url = isset($data['url']) ? $data['url'] : null;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getOffset()
    {
        return $this->offset;
    }
    public function getLength()
    {
        return $this->length;
    }
    public function getUrl()
    {
        return $this->url;
    }
}

class Message extends Entity
{
    protected $message_id;
    protected $date;
    protected $chat;
    protected $text;
    protected $entities;
    private $command;
    private $type;

    public function __construct(array $data, $bot_name)
    {
        $this->init($data, $bot_name);
    }

    protected function init(array & $data, & $bot_name)
    {
        $this->bot_name = $bot_name;
        $this->type = 'Message';
        $this->message_id = isset($data['message_id']) ? $data['message_id'] : null;
        $this->chat = isset($data['chat']) ? $data['chat'] : null;
        $this->chat = new Chat($this->chat);
        $this->date = isset($data['date']) ? $data['date'] : null;
        $this->text = isset($data['text']) ? $data['text'] : null;
        $command = $this->getCommand();
        if (!empty($command)) {
            $this->type = 'command';
        }

        $this->entities = isset($data['entities']) ? $data['entities'] : null;
        if (!empty($this->entities)) {
            foreach ($this->entities as $entity) {
                if (!empty($entity)) {
                    $entities[] = new MessageEntity($entity);
                }
            }
            $this->entities = $entities;
        }
    }

    public function getFullCommand()
    {
        if (substr($this->text, 0, 1) === '/') {
            $no_EOL = strtok($this->text, PHP_EOL);
            $no_space = strtok($this->text, ' ');

            //try to understand which separator \n or space divide /command from text
            if (strlen($no_space) < strlen($no_EOL)) {
                return $no_space;
            } else {
                return $no_EOL;
            }
        } else {
            return;
        }
    }

    public function getCommand()
    {
        if (!empty($this->command)) {
            return $this->command;
        }

        $cmd = $this->getFullCommand();

        if (substr($cmd, 0, 1) === '/') {
            $cmd = substr($cmd, 1);

            //check if command is follow by botname
            $split_cmd = explode('@', $cmd);
            if (isset($split_cmd[1])) {
                //command is followed by name check if is addressed to me
                if (strtolower($split_cmd[1]) == strtolower($this->bot_name)) {
                    return $this->command = $split_cmd[0];
                }
            } else {
                //command is not followed by name
                return $this->command = $cmd;
            }
        }

        return false;
    }

    public function getMessageId()
    {
        return $this->message_id;
    }
    public function getDate()
    {
        return $this->date;
    }
    public function getChat()
    {
        return $this->chat;
    }
    public function getText($without_cmd = false)
    {
        $text = $this->text;
        if ($without_cmd) {
            $command = $this->getFullCommand();
            if (!empty($command)) {
                //$text = substr($text, strlen($command.' '), strlen($text));
                $text = substr($text, strlen($command) + 1, strlen($text));
            }
        }

        return $text;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getEntities()
    {
        return $this->entities;
    }
}

class ServerResponse extends Entity
{

    protected $ok;
    protected $result;
    protected $error_code;
    protected $description;


    public function __construct(array $data, $bot_name)
    {
        if (isset($data['ok']) & isset($data['result'])) {
            if (is_array($data['result'])) {
                if ($data['ok'] & !$this->isAssoc($data['result'])) {
                    //get update
                    foreach ($data['result'] as $update) {
                        $this->result[] = new Update($update, $bot_name);
                    }
                } elseif ($data['ok'] & $this->isAssoc($data['result'])) {
                        //Response from sendMessage
                        $this->result = new Message($data['result'], $bot_name);
                }
                $this->ok = $data['ok'];
                $this->error_code = null;
                $this->description = null;
            } else {
                if ($data['ok'] & $data['result'] == true) {
                    //Response from setWebhook set
                    $this->ok = $data['ok'];
                    $this->result = true;
                    $this->error_code = null;
    
                    if (isset($data['description'])) {
                        $this->description = $data['description'];
                    } else {
                        $this->description = '';
                    }
                } else {
                    $this->ok = false;
                    $this->result = null;
                    $this->error_code = $data['error_code'];
                    $this->description = $data['description'];
                }
            }
        } else {
            //webHook not set
            $this->ok = false;

            if (isset($data['result'])) {
                $this->result = $data['result'];
            } else {
                $this->result = null;
            }

            if (isset($data['error_code'])) {
                $this->error_code = $data['error_code'];
            } else {
                $this->error_code = null;
            }

            if (isset($data['description'])) {
                $this->description = $data['description'];
            } else {
                $this->description = null;
            }
        }
    }

    protected function isAssoc(array $array)
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }
    public function isOk()
    {
        return $this->ok;
    }
    public function getResult()
    {
        return $this->result;
    }
    public function getErrorCode()
    {
        return $this->error_code;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function printError()
    {
        return 'Error N: '.$this->getErrorCode().' Description: '.$this->getDescription();
    }
}

class Update extends Entity
{

    protected $update_id;
    protected $message;
    private $update_type;

    public function __construct(array $data, $bot_name)
    {

        $this->bot_name = $bot_name;

        $update_id = isset($data['update_id']) ? $data['update_id'] : null;
        $this->update_id = $update_id;

        $this->message = isset($data['message']) ? $data['message'] : null;
        if (!empty($this->message)) {
            $this->message = new Message($this->message, $bot_name);
            $this->update_type = 'message';
        }
    }

    public function getUpdateId()
    {
        return $this->update_id;
    }

    public function getMessage()
    {
        return $this->message;
    }
    public function getUpdateType()
    {
        return $this->update_type;
    }
    public function getUpdateContent()
    {
        if ($this->update_type == 'message') {
            return $this->getMessage();
        }
    }
}