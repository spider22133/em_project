<?

class VettichPostingFunc
{
	static public $modules = array();
	static public $posts = false;
	static private $_usort = array();
	static private $db_table = 'vettich_options';

	static function module_id()
	{
		return 'vettich.autoposting';
	}

	static function IncludeModule($module_name)
	{
		if(empty($module_name))
			return false;

		if(isset(self::$modules[$module_name]) && !!self::$modules[$module_name])
			return true;

		$path = __DIR__."/posts/$module_name/include.php";
		if(file_exists($path))
		{
			if($ret = include_once($path))
			{
				self::$modules[$module_name] = $ret;
				return true;
			}
		}
		return false;
	}

	function module($module_name)
	{
		if($module_name == 'VettichPosting')
		{
			return array(
				'class'=> 'VettichPosting',
				'option'=> 'VettichPostingOption',
				'logs'=> 'VettichPostingLogs',
				'func'=> 'VettichPostingFunc',
			);
		}
		return self::$modules[$module_name];
	}

	/**
	*@return array posts
	*/
	function __GetPosts($refresh=false)
	{
		if(!$refresh && self::$posts !== false)
			return self::$posts;

		self::$posts = array();
		$_dir_name = __DIR__.'/posts/';
		$_dir = scandir($_dir_name);
		if($_dir !== false)
		{
			foreach($_dir as $v)
			{
				if($v != '.' && $v != '..' && is_dir($_dir_name.$v))
				{
					self::$posts[] = $v;
				}
			}
		}

		return self::$posts;
	}

	/*
	string $url - адрес запроса
	string|array $post_data - данные запроса
	string $is_cookie - использовать ли cookie
	return resurl curl request
	*/
	static function _curl_post($url, $post_data, $cookie_postfix = '', $user_agent=false, $timeout=120/*seconds*/)
	{
		if($url == '')
			return false;
		
		$is_https = strpos($url, 'https') === 0;

		if(is_array($cookie_postfix))
		{
			$cookie_type = $cookie_postfix[0];
			$cookie_postfix = $cookie_postfix[1];
		}
		else
			$cookie_type = (empty($cookie_postfix)? 0 : 1);

		$cookie_file = __DIR__.'/../../../tmp/vettich.autoposting/';
		if($cookie_type and (is_dir($cookie_file) or mkdir($cookie_file, 0744, true)))
		{
			if(empty($cookie_postfix))
				$cookie_file .= 'cookie.txt';
			else
				$cookie_file .= 'cookie_'.$cookie_postfix.'.txt';
		}
		else
			$cookie_file .= 'cookie.txt';

		$c = curl_init($url);

		curl_setopt($c, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);

		if(!!$user_agent)
		{
			curl_setopt($c, CURLOPT_USERAGENT, $user_agent);
		}

		if($cookie_type == 1)
		{
			curl_setopt($c, CURLOPT_COOKIEFILE, $cookie_file);
			curl_setopt($c, CURLOPT_COOKIEJAR, $cookie_file);
		}
		elseif($cookie_type == 2)
			curl_setopt($c, CURLOPT_COOKIEJAR, $cookie_file);
		elseif($cookie_type == 3)
			curl_setopt($c, CURLOPT_COOKIEFILE, $cookie_file);

		if($is_https)
		{
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
		}

		if(!empty($post_data))
		{
			curl_setopt($c, CURLOPT_POST, true);
			curl_setopt($c, CURLOPT_POSTFIELDS, $post_data);
		}

		curl_setopt($c, CURLOPT_TIMEOUT, $timeout);

		$res = curl_exec($c);
		curl_close($c);
		
		return $res;
	}

	static function _usort(&$array, $sort)
	{
		foreach($sort as $by=>$order)
		{
			self::$_usort = array('by'=>$by, 'order'=>$order);
			if($order == 'desc')
				usort($array, array("VettichPostingFunc", "callback_usort_desc"));
			else
				usort($array, array("VettichPostingFunc", "callback_usort_asc"));
		}
	}

	static function callback_usort_asc($ar1, $ar2)
	{
		if(is_numeric($ar1[self::$_usort['by']]) && is_numeric($ar2[self::$_usort['by']]))
			return intval($ar1[self::$_usort['by']]) > intval($ar2[self::$_usort['by']])? 1 : (-1);
		return strcmp($ar1[self::$_usort['by']], $ar2[self::$_usort['by']]);
	}

	static function callback_usort_desc($ar1, $ar2)
	{
		return (-1)*self::callback_usort_asc($ar1, $ar2);
	}

	static function substr($str, $start, $length=false, $encoding='UTF-8', $wordwrap=false, $postfix=false)
	{
		$orig_len = strlen($str);
		if($orig_len <= $length)
			return $str;
		
		$postfix_len = strlen($postfix);
		if($length===false)
			$length = $orig_len;
		if($postfix && $length>$postfix_len)
			$length -= $postfix_len;
		$res = mb_substr($str, $start, $length, $encoding);
		if($wordwrap)
			$res = mb_substr($res, 0, mb_strripos($res, ' ', 0, $encoding), $encoding);
		if($postfix && $length>$postfix_len && strlen($res) < $orig_len)
			$res .= $postfix;

		return $res;
	}

	static function get_youtube_frame($url, $width=640, $height=360)
	{
		if(empty($url))
			return '';
		return '<iframe width="'.$width.'" height="'.$height.'" src="'.$url.'" frameborder="0" allowfullscreen></iframe>';
	}

	static function vettich_service($method, $params='')
	{
		if(empty($method))
			return false;

		if(is_array($params))
			$params = http_build_query($params);

		$method_url = $method;
		if(!empty($params))
			$method_url .= '?'.$params;
		$url = 'http://service.vettich.ru/method.'.$method_url;
		$ret = json_decode(self::_curl_post($url, false, false, false, 1), 1);
		if(empty($ret))
			$ret = json_decode(CVDB::get('vettich_service_method.'.$method_url), 1);
		else
			CVDB::set('vettich_service_method.'.$method_url, $ret);
		return $ret;
	}

	static function getVersion()
	{
		$arModuleVersion = array();
		include __DIR__.'/../install/version.php';
		if(empty($arModuleVersion['VERSION']))
			return '1.0.0';
		return $arModuleVersion['VERSION'];
	}
}