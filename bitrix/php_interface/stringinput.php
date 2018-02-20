
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class StringInput extends \Bitrix\Sale\Internals\Input\Base // String reserved in php 7
{
protected static function getEditHtmlSingle($name, array $input, $value)
{
if ($input['MULTILINE'] == 'Y')
{
$attributes = static::extractAttributes($input,
array('DISABLED'=>'', 'READONLY'=>'', 'AUTOFOCUS'=>'', 'REQUIRED'=>''),
array('FORM'=>1, 'MAXLENGTH'=>1, 'PLACEHOLDER'=>1, 'DIRNAME'=>1, 'ROWS'=>1, 'COLS'=>1, 'WRAP'=>1));

return '<textarea name="'.$name.'"'.$attributes.'>'.htmlspecialcharsbx($value).'</textarea>';
}
else
{
$attributes = static::extractAttributes($input,
array('DISABLED'=>'', 'READONLY'=>'', 'AUTOFOCUS'=>'', 'REQUIRED'=>'', 'AUTOCOMPLETE'=>'on'),
array('FORM'=>1, 'MAXLENGTH'=>1, 'PLACEHOLDER'=>1, 'DIRNAME'=>1, 'SIZE'=>1, 'LIST'=>1, 'PATTERN'=>1));

return '<input type="text" name="'.$name.'" value="'.htmlspecialcharsbx($value).'"'.$attributes.'>';
}
}

/**
* @param $name
* @param array $input
* @param $value
* @return string
*/
public static function getFilterEditHtml($name, array $input, $value)
{
return static::getEditHtmlSingle($name, $input, $value);
}

protected static function getErrorSingle(array $input, $value)
{
$errors = array();

$value = trim($value);

if ($input['MINLENGTH'] && strlen($value) < $input['MINLENGTH'])
$errors['MINLENGTH'] = Loc::getMessage('INPUT_STRING_MINLENGTH_ERROR', array("#NUM#" => $input['MINLENGTH']));

if ($input['MAXLENGTH'] && strlen($value) > $input['MAXLENGTH'])
$errors['MAXLENGTH'] = Loc::getMessage('INPUT_STRING_MAXLENGTH_ERROR', array("#NUM#" => $input['MAXLENGTH']));

if ($input['PATTERN'] && !preg_match($input['PATTERN'], $value))
$errors['PATTERN'] = Loc::getMessage('INPUT_STRING_PATTERN_ERROR');

return $errors;
}

static function getSettings(array $input, $reload)
{
$settings = array(
'MINLENGTH' => array('TYPE' => 'NUMBER', 'LABEL' => Loc::getMessage('INPUT_STRING_MINLENGTH'), 'MIN' => 0, 'STEP' => 1),
'MAXLENGTH' => array('TYPE' => 'NUMBER', 'LABEL' => Loc::getMessage('INPUT_STRING_MAXLENGTH'), 'MIN' => 0, 'STEP' => 1),
'PATTERN'   => array('TYPE' => 'STRING', 'LABEL' => Loc::getMessage('INPUT_STRING_PATTERN'  )),
'MULTILINE' => array('TYPE' => 'Y/N'   , 'LABEL' => Loc::getMessage('INPUT_STRING_MULTILINE'), 'ONCLICK' => $reload),
);

if ($input['MULTILINE'] == 'Y')
{
$settings['COLS'] = array('TYPE' => 'NUMBER', 'LABEL' => Loc::getMessage('INPUT_STRING_SIZE'), 'MIN' => 0, 'STEP' => 1);
$settings['ROWS'] = array('TYPE' => 'NUMBER', 'LABEL' => Loc::getMessage('INPUT_STRING_ROWS'), 'MIN' => 0, 'STEP' => 1);
}
else
{
$settings['SIZE'] = array('TYPE' => 'NUMBER', 'LABEL' => Loc::getMessage('INPUT_STRING_SIZE'), 'MIN' => 0, 'STEP' => 1);
}

return $settings;
}
}

\Bitrix\Sale\Internals\Input\Manager::register('STRING_C', array(
'CLASS' => '\StringInput',
'NAME' => \Bitrix\Main\Localization\Loc::getMessage('INPUT_STRING'),
));