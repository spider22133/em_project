<?

IncludeModuleLangFile(__FILE__);

class VPostingInst
{
	public static $user_agent = false;
	public static $guid = false;

	function user_agent()
	{
		if(!!self::$user_agent)
			return self::$user_agent;

		$resolutions = array('720x1280', '320x480', '480x800', '1024x768', '1280x720', '768x1024', '480x320');
		$versions = array('GT-N7000', 'SM-N9000', 'GT-I9220', 'GT-I9100');
		$dpis = array('120', '160', '320', '240');

		$ver = $versions[array_rand($versions)];
		$dpi = $dpis[array_rand($dpis)];
		$res = $resolutions[array_rand($resolutions)];

		self::$user_agent = 'Instagram 4.'.mt_rand(1,2).'.'.mt_rand(0,2).' Android ('.mt_rand(10,11).'/'.mt_rand(1,3).'.'.mt_rand(3,5).'.'.mt_rand(0,5).'; '.$dpi.'; '.$res.'; samsung; '.$ver.'; '.$ver.'; smdkc210; en_US)';
		return self::$user_agent;
	}

	function guid()
	{
		if(!!self::$guid)
			return self::$guid;

		self::$guid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(16384, 20479),
			mt_rand(32768, 49151),
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 65535)
		);
		return self::$guid;
	}

	function genSig($data)
	{
		return hash_hmac('sha256', $data, 'b4a23f5e39b5929e0666ac5de94c89d1618a2916');
	}

	function method($method, $data, $cookie)
	{
		if($method == '')
			return false;

		$url = 'https://instagram.com/api/v1/'.$method;
		$return = json_decode(VettichPostingFunc::_curl_post($url, $data, array($cookie, 'instagram'), self::user_agent()), 1);
		return $return;
	}

	/**
	* Публикует запись в Instagram
	* 
	* @param array $arFields поля публикуемого элемента
	* @param array $arAcconts в какие аккаунты публиковать
	* @param array $arPost данные о публикации
	* @param array $arSite массив полей сайта
	*/
	function post($arFields, $arAccounts, $arPost, $arSite)
	{
		global $APPLICATION;

		if(VOptions::get("is_instagram_enable", false, VettichPostingFunc::module_id()) != 'Y')
			return;
		
		$post_data = array();
		$device_id = 'android-'.self::guid();
		foreach($arAccounts as $acc_id)
		{
			if(intval(CVDB::get("instagram_accounts")) >= intval($acc_id))
			{
				$post_data = array();
				$arAccount = VettichPosting::paramValues('instagram_accounts', $acc_id);

				if($arAccount['is_enable'] != 'Y')
					continue;

				$data = '{"device_id":"' . $device_id . '","guid":"' . self::guid() . '","username":"' . $arAccount['login'] . '","password":"' . $arAccount['password'] . '","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
				$sig = self::genSig($data);
				$data = 'signed_body=' . $sig . '.' . urlencode($data) . '&ig_sig_key_version=4';
				$cookie = 'instagram';
				$rs = self::method('accounts/login/', $data, 2);
				define('DEBUG', true);
				if($rs['status'] == 'ok')
				{
					$next = false;
					if($arPost['instagram_photo'] != '' && $arPost['instagram_photo'] != 'none')
					{
						$next = true;
						$data = self::attach_photo($arPost['instagram_photo'], $arFields, $arAccount, $arPost);
						$rs = self::method('media/upload/', $data, 3);
						if(!strcmp(self::getImgPathTmp(), self::getUploadFileName($data)))
							unlink(self::getImgPathTmp());
					}
					if(($next && $rs['status'] != 'ok' && $arPost['instagram_photo_other'] != '' && $arPost['instagram_photo_other'] != 'none')
						or !($arPost['instagram_photo'] != '' && $arPost['instagram_photo'] != 'none'))
					{
						$next = true;
						$data = self::attach_photo($arPost['instagram_photo_other'], $arFields, $arAccount, $arPost);
						$rs = self::method('media/upload/', $data, 3);
						if(!strcmp(self::getImgPathTmp(), self::getUploadFileName($data)))
							unlink(self::getImgPathTmp());
					}
					if($next && $rs['status'] == 'ok')
					{
						$message = VettichPosting::replaceMacros($arPost['instagram_message'], $arFields, $arSite, $arPost);
						$message = strip_tags($message);
						$message = $APPLICATION->ConvertCharset($message, SITE_CHARSET, "UTF-8");
						$data = (object)array(
							'device_id' => $device_id,
							'guid' => $guid,
							'media_id' => $rs['media_id'],
							'caption' => html_entity_decode(trim($message)),
							'device_timestamp' => time(),
							'source_type' => '5',
							'filter_type' => '0',
							'extra' => '{}',
							'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
						);
						$data = json_encode($data);
						$sig = self::genSig($data);
						$data = 'signed_body=' . $sig . '.' . urlencode($data) . '&ig_sig_key_version=4';
						$rs = self::method('media/configure/', $data, 3);
					}
				}

				if($rs['status'] != 'ok')
				{
					if(VOptions::get('instagram_log_error', false, VettichPostingFunc::module_id()) == 'Y')
					{
						$text = GetMessage('INSTAGRAM_ERROR',
							array(
								'#STATUS#' => $rs['status'],
								'#MESSAGE#'=>$rs['message'],
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => VettichPosting::GetUrlPostAcc('instagram', $acc_id, $arAccount['name']),
							)
						);
						VettichPostingLogs::addLog('instagram', $text, 'Error');
					}
				}
				else
				{
					if(VOptions::get('instagram_log_success', false, VettichPostingFunc::module_id()) == 'Y')
					{
						$text = GetMessage('INSTAGRAM_SUCCESS',
							array(
								'#URL#' => self::getUrlPost($rs),
								'#ACC_NAME#' => VettichPosting::GetUrlPostAcc('instagram', $acc_id, $arAccount['name']),
							)
						);
						VettichPostingLogs::addLog('instagram', $text, 'Success');
					}
				}
			}
		}
	}

	function getUrlPost($response)
	{
		return 'http://instagram.com/p/'.$response['media']['code'].'/';
	}

	function attach_photo($sProp, $arFields, $arAccount, $arPost)
	{
		$result = '';
		if(strpos($sProp, 'PROPERTY_') === 0)
		{
			$files = array();
			$prop_code = substr($sProp, strlen('PROPERTY_'));
			$arProp = $arFields['PROPERTIES'][$prop_code];
			if($arProp && $arProp['PROPERTY_TYPE'] == 'F')
			{
				if($arProp['MULTIPLE'] == 'Y')
				{
					foreach($arProp['VALUES'] as $k=>$arValue)
					{
						$files[] = $_SERVER['DOCUMENT_ROOT'].CFile::GetPath($arValue);
					}
				}
				else
				{
					$files[] = $_SERVER['DOCUMENT_ROOT'].CFile::GetPath($arProp['VALUE']);
				}
			}
			if(!empty($files))
				$result = self::upload_files($files);
		}
		else
		{
			$img_path = CFile::GetPath($arFields[$sProp]);
			if($img_path != '')
				$result = self::upload_files(array($_SERVER['DOCUMENT_ROOT'].$img_path));
		}
		return $result;
	}

	function upload_files($fileName)
	{
		if(is_array($fileName))
			foreach($fileName as $val)
			{
				$fileName = $val;
				break;
			}

		if($newfile = self::getSquareImg($fileName))
			$fileName = $newfile;

		$ret = array('device_timestamp' => time());
		if (version_compare(PHP_VERSION, '5.6.0', '<'))
			$ret['photo'] = '@'.$fileName;
		else
			$ret['photo'] = new \CURLFile($fileName);
		return $ret;
	}

	function getUploadFileName($data)
	{
		if (!version_compare(PHP_VERSION, '5.6.0', '<')
			&& is_object($data['photo'])
			&& get_class($data['photo']) == 'CURLFile')
		{
			return $data['photo']->getFilename();
		}
		else
			return substr($data['photo'], 1);
	}

	function getImgPathTmp()
	{
		return $_SERVER['DOCUMENT_ROOT'].'/upload/vettich.autoposting.instagram.image.tmp.png';
	}

	function getSquareImg($img_path)
	{
		if(empty($img_path))
			return '';

		list($iw, $ih, $itype) = getimagesize($img_path);

		if($iw == $ih)
			return $img_path;

		$types = array('','gif','jpeg','png');
		$ext = $types[$itype];
		if($ext)
		{
			$func = 'imagecreatefrom'.$ext;
			$img = $func($img_path);
		}
		else
			return '';

		$size = min($iw, $ih);
		$ox = 0;
		if($size != $iw)
		{
			$ox = $iw/2 - $size/2;
		}

		$img_o = imagecreatetruecolor($size, $size);
		imagecopy($img_o, $img, 0, 0, $ox, 0, $size, $size);

		$dest = self::getImgPathTmp();

		if ($type == 2) {
			if(imagejpeg($img_o,$dest,100))
				return $dest;
		} else {
			$func = 'image'.$ext;
			if($func($img_o,$dest))
				return $dest;
		}
		return '';
	}
}