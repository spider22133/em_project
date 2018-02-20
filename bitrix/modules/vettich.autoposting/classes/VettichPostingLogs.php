<?

class VettichPostingLogs
{
	function addLog($name, $text, $type)
	{
		if(empty($name) or empty($text) or empty($type))
			return;

		$name = 'logs.'.$name;

		$count = CVDB::get($name.'.count', 0);
		$log = array(
			'DATETIME' => date('d.m.Y H:i:s'),
			'TYPE' => $type,
			'TEXT' => $text,
		);
		CVDB::set($name.'.log-'.$count, $log);
		CVDB::set($name.'.count', $count+1);

		if(strtolower($type) == 'error'
			&& COption::GetOptionString('vettich.autoposting', 'is_errors_log', 'N') != 'Y')
			COption::SetOptionString('vettich.autoposting', 'is_errors_log', 'Y');
	}

	function getLogs($name)
	{
		if(empty($name))
			return;

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

	function getLogsID()
	{
	}

	function clearLogs($name='')
	{
		if(empty($name))
			return;

		$name = 'logs.'.$name;
		$count = CVDB::get($name.'.count', 0);
		for($i=0; $i<$count; $i++)
		{
			CVDB::rem($name.'.log-'.$i);
		}
		CVDB::set($name.'.count', 0);
	}
}