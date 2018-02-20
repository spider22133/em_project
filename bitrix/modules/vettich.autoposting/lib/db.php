<?
namespace Vettich\Autoposting;
use Bitrix\Main\Entity;
use Vettich\Autoposting\PostingFunc;

class DBTable extends DBase
{
	public static function getTableName()
	{
		return 'vettich_autoposting_posts_v2';
	}

	public static function getMap()
	{
		$arMap = array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\IntegerField('TYPE', array(
				'default_value' => function(){ return PostingFunc::DBTYPEPOSTS; }
			)),
			new Entity\StringField('NAME'),
			new Entity\StringField('IS_ENABLE'),
			new Entity\StringField('SITE_ID'),
			new Entity\StringField('DOMAIN_NAME'),
			new Entity\StringField('IBLOCK_TYPE'),
			new Entity\StringField('IBLOCK_ID'),
			new Entity\StringField('PROTOCOL'),
			new Entity\TextField('FIELD_CMP_GROUP',      array('serialized'=>true)),
			new Entity\StringField('MANUALLY'),
			new Entity\StringField('IS_SECTION_ENABLED'),
			new Entity\StringField('SECTIONS',           array('serialized'=>true)),
			new Entity\StringField('IS_UTM_ENABLE'),
			new Entity\TextField('PARAM_RESERVE1'),
			new Entity\TextField('PARAM_RESERVE2'),
			new Entity\TextField('PARAM_RESERVE3'),
			new Entity\TextField('PARAM_RESERVE4'),
			new Entity\TextField('ACCOUNT_VK',           array('serialized'=>true)),
			new Entity\TextField('ACCOUNT_VKGOODS',      array('serialized'=>true)),
			new Entity\TextField('ACCOUNT_FACEBOOK',     array('serialized'=>true)),
			new Entity\TextField('ACCOUNT_TWITTER',      array('serialized'=>true)),
			new Entity\TextField('ACCOUNT_ODNOKLASSNIKI',array('serialized'=>true)),
			new Entity\TextField('ACCOUNT_INSTAGRAM',    array('serialized'=>true)),
			new Entity\TextField('ACCOUNT_GOOGLEPLUS',   array('serialized'=>true)),
			new Entity\TextField('ACCOUNT_LINKEDIN',     array('serialized'=>true)),
			new Entity\TextField('ACCOUNT_LIVEJOURNAL',  array('serialized'=>true)),
			new Entity\TextField('ACCOUNT_PINTEREST',    array('serialized'=>true)),
			new Entity\TextField('ACCOUNT_MYMAILRU',     array('serialized'=>true)),
			new Entity\TextField('ACCOUNT_BLOGGER',      array('serialized'=>true)),
			new Entity\TextField('ACCOUNT_TUMBLR',       array('serialized'=>true)),
		);
		return $arMap;
	}

	public static function OnBeforeAdd(Entity\Event $event)
	{
		$result = new Entity\EventResult;

		$data = $event->getParameter('fields');
		$data['FIELD_CMP_GROUP'] = self::TrimCmpFields($data['FIELD_CMP_GROUP']);
		$result->modifyFields(array('FIELD_CMP_GROUP' => $data['FIELD_CMP_GROUP']));

		return $result;
	}

	public static function OnBeforeUpdate(Entity\Event $event)
	{
		$result = new Entity\EventResult;
		// $result->modifyFields(array('LAST_MODIFIED' => new Type\DateTime()));

		$data = $event->getParameter('fields');
		$data['FIELD_CMP_GROUP'] = self::TrimCmpFields($data['FIELD_CMP_GROUP']);
		$result->modifyFields(array('FIELD_CMP_GROUP' => $data['FIELD_CMP_GROUP']));

		return $result;
	}

	private static function TrimCmpFields($arFields)
	{
		if(empty($arFields['count']))
			$arFields['count'] = 0;
		for($i = 0; $i < $arFields['count']; ++$i)
		{
			if(empty($arFields[$i]['FIELD_1'])
				or $arFields[$i]['FIELD_1'] == 'none')
				continue;
			$arResult[] = $arFields[$i];
		}
		$arResult['count'] = count($arResult);
		return $arResult;
	}
}
