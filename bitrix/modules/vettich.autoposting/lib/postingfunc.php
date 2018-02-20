<?
namespace Vettich\Autoposting;

class PostingFunc
{
	const DBTABLE = '\\Vettich\\Autoposting\\DBTable';
	const DBLOGSTABLE = '\\Vettich\\Autoposting\\DBLogsTable';
	const DBTYPEPOSTS = 1;
	const DBTYPEPOSTPOPUP = 2;

	const FIELD_CMP_EQUALLY = 1;
	const FIELD_CMP_MORE_OR_EQUALLY = 2;
	const FIELD_CMP_LESS_OR_EQUALLY = 3;
	const FIELD_CMP_CONTAINS = 4;
	const FIELD_CMP_NOT_CONTAINS = 5;

	const CURL_ENCTYPE_MULTIPART = 'multipart/form-data';
	const CURL_ENCTYPE_APPLICATION = 'application/x-www-form-urlencoded';

	static public $modules = array();
	static public $mod_params = false;
	static public $posts = false;
	static private $arFieldsDBTable = array();
	static private $_usort = array();
	static private $db_table = 'vettich_options';

	static function module_id()
	{
		return 'vettich.autoposting';
	}

	// is "vettich.autopostingplus" module installed?
	static function isPlus()
	{
		return IsModuleInstalled('vettich.autopostingplus');
	}

	// todo: delete
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

	// todo: delete
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

	static function isModule($module_name)
	{
		if(empty($module_name))
			return false;

		$arPost = self::module2($module_name);
		if(method_exists($arPost['func'], 'isSupport'))
			return $arPost['func']::isSupport();
		return false;
	}

	function module2($module_name, $mkey=false)
	{
		if($module_name == 'Posting')
		{
			$ret = array(
				'posting' => '\\Vettich\\Autoposting\\Posting',
				'option' => '\\Vettich\\Autoposting\\PostingOption',
				'logs' => '\\Vettich\\Autoposting\\PostingLogs',
				'func' => '\\Vettich\\Autoposting\\PostingFunc',
			);
			if(!!$mkey)
				if(isset($ret[$mkey]))
					return $ret[$mkey];
				else
					return false;
			return $ret;
		}
		if(self::$mod_params === false)
			self::__GetPosts();
		if(!!$mkey)
			if(!empty(self::$mod_params[$module_name][$mkey]))
				return self::$mod_params[$module_name][$mkey];
			else
				return false;
		if(!empty(self::$mod_params[$module_name]))
			return self::$mod_params[$module_name];
		return false;
	}

	static function event($event, $arParams=array())
	{
		$res = self::modules_event($event, $arParams);
		if(!self::event_other($event, $arParams))
			$res = false;
		return $res;
	}

	static function event_other($event, $arParams=array())
	{
		$res = true;
		foreach(GetModuleEvents(self::module_id(), $event, true) as $arHandler)
		{
			if(!ExecuteModuleEvent($arHandler, $arParams))
				$res = false;
		}
		return $res;
	}

	static function module_event($module_name, $event, $arParams=array())
	{
		$obj = self::module2($module_name, 'event');
		if(method_exists($obj, $event))
			return $obj::$event($arParams);
		return true;
	}

	static function modules_event($event, $arParams=array())
	{
		$res = true;
		foreach(self::__GetPosts() as $post)
		{
			if(!self::module_event($post, $event, $arParams))
				$res = false;
		}
		return $res;
	}

	static function GetFieldsDBTableFromPost($dbtable)
	{
		$arFields = array();
		foreach(self::GetFieldsDBTable($dbtable) as $fName)
		{
			if(isset($_POST[$fName]))
			{
				if(!is_array($_POST[$fName]) && !is_object($_POST[$fName]))
					$arFields[$fName] = trim($_POST[$fName]);
				else
					$arFields[$fName] = $_POST[$fName];
			}
			else
				$arFields[$fName] = '';
		}
		return $arFields;
	}

	static function GetFieldsDBTable($dbtable)
	{
		if(isset(self::$arFieldsDBTable[$dbtable]))
			return self::$arFieldsDBTable[$dbtable];
		$arFields = array();
		if(empty($dbtable))
			return $arFields;

		foreach($dbtable::getMap() as $field)
			$arFields[] = $field->getName();

		if(!empty($arFields))
			self::$arFieldsDBTable[$dbtable] = $arFields;
		return $arFields;
	}

	static function GetValues($ID, $dbtable=self::DBTABLE)
	{
		if($ID < 0
			or $dbtable == null)
			return array();

		$arValues = array();
		if($ar = $dbtable::GetRowById($ID))
			$arValues = $ar;
		return $arValues;
	}

	static function GetNextIdDB($dbtable=self::DBTABLE)
	{
		if($dbtable == null)
			return 0;
		$ar = $dbtable::GetList(array(
			'order' => array('ID' => 'DESC'),
			'select'=>array('ID'),
			'limit' => 1
		))->Fetch();
		return intval($ar['ID']) + 1;
	}

	private static $_posts = null;
	/**
	* возвращает список соц. сетей, которые "включены"
	*
	*/
	function GetPosts()
	{
		if(self::$_posts === null) {
			$showPosts = \COption::GetOptionString(self::module_id(), 'show_accounts', null);
			$showPosts = unserialize($showPosts);

			$posts = self::__GetPosts();
			if($showPosts == null) {
				self::$_posts = $posts;
			}
			foreach($showPosts as $post) {
				if(in_array($post, $posts)) {
					self::$_posts[] = $post;
				}
			}
		}

		return self::$_posts;
	}

	function isHiddenPost($post)
	{
		return !in_array($post, self::GetPosts());
	}

	/**
	* возвращает список соц. сетей, которые "включены"
	* для отображения в настройках
	*/
	function GetPosts4Options()
	{
		$posts = self::__GetPosts();
		$arResult = array();
		foreach ($posts as $post) {
			if(($mod = self::module2($post, 'func'))
				&& method_exists($mod, 'get_name')) {
				$arResult[$post] = $mod::get_name();
			}
		}
		return $arResult;
	}

	/**
	* возвращает список "присутствующих" соц. сетей в системе
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
					self::$mod_params[$v] = array(
						'posting' => '\\Vettich\\Autoposting\\Posts\\'.$v.'\\Posting',
						'option' => '\\Vettich\\Autoposting\\Posts\\'.$v.'\\Option',
						'func' => '\\Vettich\\Autoposting\\Posts\\'.$v.'\\Func',
						'event' => '\\Vettich\\Autoposting\\Posts\\'.$v.'\\Event',
					);
					foreach (self::$mod_params[$v] as $key => $mod) {
						if(!class_exists($mod)) {
							unset(self::$mod_params[$v][$key]);
						}
					}
				}
			}
		}

		self::event_other('OnGetPosts', array(
			'posts' => &self::$posts,
			'mod_params' => &self::$mod_params,
		));
		self::$posts = array_unique(self::$posts);

		return self::$posts;
	}

	/*
	old method
	string $url - адрес запроса
	string|array $post_data - данные запроса
	string $is_cookie - использовать ли cookie
	return resurl curl request
	*/
	static function _curl_post($url, $post_data, $cookie_postfix = '', $user_agent=false, $timeout=120/*seconds*/)
	{
		$params = array();
		if(is_array($cookie_postfix))
		{
			$params['cookie_type'] = $cookie_postfix[0] ?: $cookie_postfix['type'];
			$params['cookie_postfix'] = $cookie_postfix[1] ?: $cookie_postfix['postfix'];
			$params['cookies'] = $cookie_postfix['cookies'];
		}
		elseif(!empty($cookie_postfix))
		{
			$params['cookie_type'] = 1;
			$params['cookie_postfix'] = $cookie_postfix;
		}

		$params['user_agent'] = $user_agent;
		$params['timeout'] = $timeout;

		return self::curl_post($url, $post_data, $params);
	}
	/*
	string $url - адрес запроса
	string|array $post_data - данные запроса
	string $params - параметры
	return resurl curl request
	*/
	static function curl_post($url, $post_data, $params = array())
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
		if($params['cookie_type'] and (is_dir($cookie_file) or mkdir($cookie_file, 0744, true)))
		{
			if(empty($params['cookie_postfix']))
				$cookie_file .= 'cookie.txt';
			else
				$cookie_file .= 'cookie_'.$params['cookie_postfix'].'.txt';
		}
		else
			$cookie_file .= 'cookie.txt';

		$c = curl_init($url);

		if($params['CUSTOM_REQUEST'])
		{
			curl_setopt($c, CURLOPT_CUSTOMREQUEST, $params['CUSTOM_REQUEST']);
		}

		curl_setopt($c, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);

		if(!!$params['user_agent'])
		{
			curl_setopt($c, CURLOPT_USERAGENT, $params['user_agent']);
		}

		if($params['cookie_type'] == 1)
		{
			curl_setopt($c, CURLOPT_COOKIEFILE, $cookie_file);
			curl_setopt($c, CURLOPT_COOKIEJAR, $cookie_file);
		}
		elseif($params['cookie_type'] == 2)
			curl_setopt($c, CURLOPT_COOKIEJAR, $cookie_file);
		elseif($params['cookie_type'] == 3)
			curl_setopt($c, CURLOPT_COOKIEFILE, $cookie_file);

		if($is_https)
		{
			curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
		}

		if(!empty($post_data))
		{
			if(!$params['CUSTOM_REQUEST'] or $params['CUSTOM_REQUEST'] == 'POST')
				curl_setopt($c, CURLOPT_POST, true);
			if(isset($params['enctype'])
				&& $params['enctype'] == self::CURL_ENCTYPE_APPLICATION)
			{
				$post_data = http_build_query($post_data);
				curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($post_data)));
			}
			curl_setopt($c, CURLOPT_POSTFIELDS, $post_data);
		}

		curl_setopt($c, CURLOPT_TIMEOUT, $params['timeout']);

// curl_setopt($c, 'CURLINFO_HEADER_OUT', true);
// curl_setopt($c, 'CURLINFO_HEADER', true);
		$res = curl_exec($c);
// \VOptions::debugg(curl_getinfo($c), 'curl_getinfo');
		curl_close($c);
		
		return $res;
	}

	static function _usort(&$array, $sort)
	{
		foreach($sort as $by=>$order)
		{
			self::$_usort = array('by'=>$by, 'order'=>$order);
			if($order == 'desc')
				usort($array, array("\\Vettich\\Autoposting\\PostingFunc", "callback_usort_desc"));
			else
				usort($array, array("\\Vettich\\Autoposting\\PostingFunc", "callback_usort_asc"));
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
		// if(empty($ret))
		// 	$ret = json_decode(\CVDB::get('vettich_service_method.'.$method_url), 1);
		// else
		// 	\CVDB::set('vettich_service_method.'.$method_url, $ret);
		return $ret;
	}

	static function getVersion()
	{
		$arModuleVersion = array();
		include VETTICH_AUTOPOSTING_DIR.'/install/version.php';
		if(empty($arModuleVersion['VERSION']))
			return '1.0.0';
		return $arModuleVersion['VERSION'];
	}
}