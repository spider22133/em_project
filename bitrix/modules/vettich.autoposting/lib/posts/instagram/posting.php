<?
namespace Vettich\Autoposting\Posts\instagram;
use Vettich\Autoposting\PostingFunc;
use Vettich\Autoposting\PostingLogs;

IncludeModuleLangFile(__FILE__);

class Posting extends \Vettich\Autoposting\Posting
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
		$return = json_decode(PostingFunc::_curl_post($url, $data, array($cookie, 'instagram'), self::user_agent()), 1);
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
	static function post($arFields, $arAccounts, $arPost, $arSite, $arOptionally=array())
	{
		global $APPLICATION;
		$type = $arOptionally['type'];

		if(\COption::GetOptionString(PostingFunc::module_id(), 'is_instagram_enable', false) != 'Y')
			return;
		
		$device_id = 'android-'.self::guid();
		$arResult = array();
		foreach($arAccounts as $acc_id)
		{
			$post_id = $arOptionally['post_ids'][$acc_id];
			$arAccount = Func::GetAccountValues($acc_id, $arPost['ID']);
			if(empty($arAccount))
				continue;

			$arPost['arAccount'] = $arAccount;
			$arPost['ACCPREFIX'] = Func::ACCPREFIX;

			$post_data = array();
			if($arAccount['IS_ENABLE'] != 'Y')
				continue;

			if(($type == 'delete' or $type == 'edit')
				&& $arAccount['INSTAGRAM_PUBLICATION_MODE'] == 'none')
				continue;

			if(empty($arAccount['INSTAGRAM_PUBLICATION_MODE']))
				$arAccount['INSTAGRAM_PUBLICATION_MODE'] = 'update';

			$data = '{"device_id":"' . $device_id . '","guid":"' . self::guid() . '","username":"' . $arAccount['LOGIN'] . '","password":"' . $arAccount['PASSWORD'] . '","Content-Type":"application/x-www-form-urlencoded; charset=UTF-8"}';
			$sig = self::genSig($data);
			$data = 'signed_body=' . $sig . '.' . urlencode($data) . '&ig_sig_key_version=4';
			$cookie = 'instagram';
			$rs = self::method('accounts/login/', $data, 2);
			if($rs['status'] == 'ok')
			{
				if($type == 'delete'
					or ($type == 'edit'
						&& $arAccount['INSTAGRAM_PUBLICATION_MODE'] == 'del_add'))
				{
					if(!empty($post_id))
					{
						$data = (object)array(
							'device_id' => $device_id,
							'guid' => self::guid(),
							'media_id' => $post_id['id'],
							'device_timestamp' => time(),
							'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
						);
						$data = json_encode($data);
						$sig = self::genSig($data);
						$data = 'signed_body=' . $sig . '.' . urlencode($data) . '&ig_sig_key_version=4';
						$rs = self::method('media/'.$post_id['id'].'/delete/?media_type=1', $data, 3);
					}
				}

				if($type != 'delete')
				{
					if($type == 'edit'
						&& !empty($post_id)
						&& $arAccount['INSTAGRAM_PUBLICATION_MODE'] == 'update')
					{
						$message = parent::replaceMacros($arAccount['INSTAGRAM_MESSAGE'], $arFields, $arSite, $arPost);
						$message = strip_tags($message);
						$message = $APPLICATION->ConvertCharset($message, SITE_CHARSET, "UTF-8");
						$data = (object)array(
							'device_id' => $device_id,
							'guid' => self::guid(),
							'caption_text' => html_entity_decode(trim($message)),
							'device_timestamp' => time(),
							'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
						);
						$data = json_encode($data);
						$sig = self::genSig($data);
						$data = 'signed_body=' . $sig . '.' . urlencode($data) . '&ig_sig_key_version=4';
						$rs = self::method('media/'.$post_id['id'].'/edit_media/', $data, 3);
					}
					else
					{
						$next = false;
						if($arAccount['INSTAGRAM_PHOTO'] != '' && $arAccount['INSTAGRAM_PHOTO'] != 'none')
						{
							$next = true;
							$data = self::attach_photo($arAccount['INSTAGRAM_PHOTO'], $arFields, $arAccount, $arAccount);
							if(empty($data))
							{
								$rs['status'] = 'fail';
								$rs['message'] = 'Not set picture';
							}
							else
								$rs = self::method('media/upload/', $data, 3);
							if(!strcmp(self::getImgPathTmp(), self::getUploadFileName($data)))
								unlink(self::getImgPathTmp());
						}
						if(($next && $rs['status'] != 'ok' && $arAccount['INSTAGRAM_PHOTO_OTHER'] != '' && $arAccount['INSTAGRAM_PHOTO_OTHER'] != 'none')
							or !($arAccount['INSTAGRAM_PHOTO'] != '' && $arAccount['INSTAGRAM_PHOTO'] != 'none'))
						{
							$next = true;
							$data = self::attach_photo($arAccount['INSTAGRAM_PHOTO_OTHER'], $arFields, $arAccount, $arPost);
							if(empty($data))
							{
								$rs['status'] = 'fail';
								$rs['message'] = 'Not set picture';
							}
							else
								$rs = self::method('media/upload/', $data, 3);
							if(!strcmp(self::getImgPathTmp(), self::getUploadFileName($data)))
								unlink(self::getImgPathTmp());
						}
// \VOptions::debugg(array($rs));
						if($next && $rs['status'] == 'ok')
						{
							$message = parent::replaceMacros($arAccount['INSTAGRAM_MESSAGE'], $arFields, $arSite, $arPost);
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
				}
			}

			if($rs['status'] != 'ok')
			{
				if(\COption::GetOptionString(PostingFunc::module_id(), 'instagram_log_error', false) == 'Y')
				{
					if($type == 'delete')
						$text = GetMessage('INSTAGRAM_ERROR_DELETE',
							array(
								'#STATUS#' => $rs['status'],
								'#MESSAGE#' => $rs['message'],
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => parent::GetUrlPostAcc('instagram', $acc_id, $arAccount['NAME']),
							)
						);
					elseif($type == 'edit' && !empty($post_id) && $arAccount['INSTAGRAM_PUBLICATION_MODE'] == 'update')
						$text = GetMessage('INSTAGRAM_ERROR_EDIT',
							array(
								'#STATUS#' => $rs['status'],
								'#MESSAGE#' => $rs['message'],
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => parent::GetUrlPostAcc('instagram', $acc_id, $arAccount['NAME']),
							)
						);
					else
						$text = GetMessage('INSTAGRAM_ERROR',
							array(
								'#STATUS#' => $rs['status'],
								'#MESSAGE#' => $rs['message'],
								'#IBLOCK_ID#' => $arFields['IBLOCK_ID'],
								'#IBLOCK_TYPE#' => $arFields['IBLOCK_TYPE_ID'],
								'#ID#' => $arFields['ID'],
								'#ACC_NAME#' => parent::GetUrlPostAcc('instagram', $acc_id, $arAccount['NAME']),
							)
						);
					PostingLogs::addLog('instagram', $text, 'Error');
				}
			}
			else
			{
				if($type != 'delete')
					$arResult[$acc_id] = array(
						'code' => $rs['media']['code'],
						'id' => $rs['media']['id'],
					);
				if(\COption::GetOptionString(PostingFunc::module_id(), 'instagram_log_success', false) == 'Y')
				{
					if($type == 'delete')
						$text = GetMessage('INSTAGRAM_SUCCESS_DELETE',
							array(
								'#URL#' => self::getUrlPost($arAccount, $arResult[$acc_id]),
								'#ACC_NAME#' => parent::GetUrlPostAcc('instagram', $acc_id, $arAccount['NAME']),
							)
						);
					elseif($type == 'edit' && !empty($post_id) && $arAccount['INSTAGRAM_PUBLICATION_MODE'] == 'update')
						$text = GetMessage('INSTAGRAM_SUCCESS_EDIT',
							array(
								'#URL#' => self::getUrlPost($arAccount, $arResult[$acc_id]),
								'#ACC_NAME#' => parent::GetUrlPostAcc('instagram', $acc_id, $arAccount['NAME']),
							)
						);
					else
						$text = GetMessage('INSTAGRAM_SUCCESS',
							array(
								'#URL#' => self::getUrlPost($arAccount, $arResult[$acc_id]),
								'#ACC_NAME#' => parent::GetUrlPostAcc('instagram', $acc_id, $arAccount['NAME']),
							)
						);
					PostingLogs::addLog('instagram', $text, 'Success');
				}
			}
		}
		return $arResult;
	}

	function getUrlPost($arAccount, $response)
	{
		return 'http://instagram.com/p/'.$response['code'].'/';
	}

	function attach_photo($sProp, $arFields, $arAccount, $arPost)
	{
		$result = '';
		$files = parent::getFilesFromProperty($sProp, $arFields);
		foreach($files as $file)
		{
			$result = self::upload_files($files);
			break;
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
		$ret['photo'] = parent::getCurlFilename($fileName);
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
// 	function getSquareImg($img_path)
// 	{
// 		if(empty($img_path))
// 			return '';

// 		list($iw, $ih, $itype) = getimagesize($img_path);

// 		$types = array('','gif','jpeg','png');
// 		$ext = $types[$itype];
// 		if($ext)
// 		{
// 			$func = 'imagecreatefrom'.$ext;
// 			$img = $func($img_path);
// 		}
// 		else
// 			return '';

// 		if($iw == $ih)
// 			return $img_path;

// 		$ox = 0;
// 		$oy = 0;

// 		$r = $iw / $ih;
// 		$rr = $ih / $iw;
// 		if($rr >= 1.6 && $rr < 1.9) // 9:16
// 		{
// 			$rw = $iw / 9;
// 			$rh = $ih / 16;
// 			if($rw == $rh)
// 				return $img_path;

// 			$r = floor(min($rw, $rh));
// 			$r = 120;
// 			$ow = 9 * $r;
// 			$oh = 16 * $r;
// 		}		
// 		elseif($r >= 1.6 && $r < 1.9) // 16:9
// 		{
// 			$rw = $iw / 16;
// 			$rh = $ih / 9;
// 			if($rw == $rh)
// 				return $img_path;

// 			$r = floor(min($rw, $rh));
// 			$r = 120;
// 			$ow = 16 * $r;
// 			$oh = 9 * $r;
// 		}
// 		else/*if(($r > 1 && $r < 1.4)
// 			or ($rr > 1 && $rr < 1.4))*/ // 1:1
// 		{
// 			$size = min($iw, $ih);
// 			$ow = $oh = $size;
// 			if($size != $iw)
// 			{
// 				$ox = $iw/2 - $size/2;
// 			}
// 			elseif($size != $ih)
// 			{
// 				$oy = $ih/2 - $size/2;
// 			}
// 		}

// \VOptions::debugg(array(
// 	'iw' => $iw,
// 	'ih' => $ih,
// 	'ow' => $ow,
// 	'oh' => $oh,
// 	'r' => $r,
// 	'rr' => $rr,
// ));
// 		$img_o = imagecreatetruecolor($ow, $oh);
// 		imagecopy($img_o, $img, 0, 0, $ox, $oy, $ow, $oh);

// 		$dest = self::getImgPathTmp();

// 		if ($type == 2) {
// 			if(imagejpeg($img_o,$dest,100))
// 				return $dest;
// 		} else {
// 			$func = 'image'.$ext;
// 			if($func($img_o,$dest))
// 				return $dest;
// 		}
// 		return '';
// 	}
}