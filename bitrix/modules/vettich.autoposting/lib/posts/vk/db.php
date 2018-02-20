<?
namespace Vettich\Autoposting\Posts\vk;
use Bitrix\Main\Entity;

class DBTable extends \Vettich\Autoposting\DBase
{
	public static function getTableName()
	{
		return 'vettich_autoposting_posts_vk';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\TextField('NAME'),
			new Entity\TextField('IS_ENABLE'),
			new Entity\TextField('IS_GROUP_PUBLISH'),
			new Entity\TextField('GROUP_PUBLISH_ID'),
			new Entity\TextField('GROUP_PUBLISH'),
			new Entity\TextField('GROUP_ID_STD'),
			new Entity\TextField('GROUP_ID'),
			new Entity\TextField('ACCESS_TOKEN'),
		);
	}
}
