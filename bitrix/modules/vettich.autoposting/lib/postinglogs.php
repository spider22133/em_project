<?
namespace Vettich\Autoposting;

IncludeModuleLangFile(__FILE__);

class PostingLogs
{
	static function addLog($name, $text, $type)
	{
		if(empty($name) or empty($text) or empty($type))
			return;

		$name = strtolower($name);
		$type = strtolower($type);
		$text = trim($text);

		$arFields = array(
			'NAME' => $name,
			'TYPE' => $type,
			'TEXT' => $text,
		);
		$rs = DBLogsTable::add($arFields);
		if(!$rs->isSuccess())
			return;

		if($type == 'error')
		{
			$rs = \CAdminNotify::GetList(array(), array(
				'MODULE_ID'=>'vettich.autoposting',
				'TAG'=>'ERRORS_LOG'
			));
			if(!($ar = $rs->Fetch()))
			{
				$ar = array(
					'MESSAGE' => GetMessage('ERRORS_LOG_MESSAGE'),
					'TAG' => 'ERRORS_LOG',
					'MODULE_ID' => 'vettich.autoposting',
					'ENABLE_CLOSE' => 'Y',
				);
				\CAdminNotify::Add($ar);
			}
		}
	}

	static function addLogFromException($exception)
	{
		if($exception instanceof \Exception)
		{
			$text = 'The exception was thrown, when running the script.'
					.'Please contact the developer of this module and show error.'.PHP_EOL.PHP_EOL;
			$text .= $exception->getMessage().PHP_EOL;
			$text .= $exception->getTraceAsString();
			$text = str_replace(array(PHP_EOL), array('<br>'), $text);
			self::addLog('all', $text, 'warning');
		}
	}

	static function getLogs($name, $page=1, $item_count=20)
	{
		if(empty($name))
			return;

		$dblist = DBLogsTable::getList(array(
			'filter' => array('NAME'=>$name),
			'order' => array('DATETIME' => 'desc'),
			'count_total' => true,
			'offset' => ($page - 1) * $item_count,
			'limit' => $item_count,
		));

		$arResult = array();
		$arResult['TOTAL_COUNT'] = $dblist->getCount();
		while($ar = $dblist->fetch())
		{
			$arResult['VALUES'][] = $ar;
		}
		return $arResult;

		$name = 'logs.'.$name;
		$ret = array(
			'COUNT' => CVDB::get($name.'.count', 0),
		);
		for($i=0; $i<$ret['COUNT']; $i++)
		{
			$log = CVDB::get($name.'.log-'.$i, '');
			if($_log = unserialize($log))
				$log = $_log;

			$ret['LOGS'][$i] = $log;
		}

		return $ret;
	}

	static function clearLogs($name='')
	{
		if(empty($name))
			return;

		$dblist = DBLogsTable::getList(array(
			'filter' => array('NAME'=>$name),
			'select' => array('ID'),
		));
		while($ar = $dblist->Fetch())
		{
			DBLogsTable::delete($ar['ID']);
		}
	}
}