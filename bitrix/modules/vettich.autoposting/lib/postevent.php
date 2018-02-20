<?
namespace Vettich\Autoposting;
use Bitrix\Main\Entity;

class PostEvent
{
	static function OnSavePostsParams($params, $db=null)
	{
		if($db == null/* or !is_object($db)*/)
			return;

		$ar = $db::GetRow(array(
			'filter' => array('ID' => $params['ID']),
			'select' => array('ID'),
		));
		$arFields = PostingFunc::GetFieldsDBTableFromPost($db);
		if($ar == null)
		{
			$arFields['ID'] = $params['ID'];
			$rs = $db::add($arFields);
		}
		else
		{
			unset($arFields['ID']);
			$rs = $db::update($params['ID'], $arFields);
		}
	}
}
