<?

class CVDB // Class Vettich DataBase
{
	static private $table = 'vettich_options';

	function get($optname, $default='')
	{
		if(!$optname)
			return $default;

		$arRes = CVDBTable::getRow(array(
			'filter' => array('=NAME' => $optname),
		));
		if(!$arRes)
			return $default;
		return $arRes['VALUE'];
	}

	function set($optname, $value)
	{
		if(!$optname)
			return false;

		if(is_array($value) or is_object($value))
			$value = serialize($value);

		$arRes = CVDBTable::getRow(array(
			'filter' => array('=NAME' => $optname),
		));
		$res = false;
		if(!!$arRes)
		{
			$result = CVDBTable::update($arRes['ID'], array(
				'VALUE' => $value,
			));
			if($result->isSuccess())
				$res = true;
		}
		else
		{
			$result = CVDBTable::add(array(
				'NAME' => $optname,
				'VALUE' => $value,
			));
			if ($result->isSuccess())
				$res = true;
		}
		return $res;
	}

	function rem($optname)
	{
		if(!$optname)
			return;

		$arRes = CVDBTable::getRow(array(
			'filter' => array('=NAME' => $optname),
		));
		if(!!$arRes)
		{
			$result = CVDBTable::delete($arRes['ID']);
			return $result->isSuccess();
		}
		return false;
	}
}