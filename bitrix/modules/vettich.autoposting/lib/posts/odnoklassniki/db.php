<?
namespace Vettich\Autoposting\Posts\odnoklassniki;
use Bitrix\Main\Entity;

class DBTable extends \Vettich\Autoposting\DBase
{
	public static function getTableName()
	{
		return 'vettich_autoposting_posts_odnoklassniki';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\StringField('NAME'),
			new Entity\StringField('IS_ENABLE'),
			new Entity\StringField('IS_GROUP_PUBLISH'),
			new Entity\StringField('GROUP_ID'),
			new Entity\StringField('API_ID'),
			new Entity\StringField('API_PUBLIC_KEY'),
			new Entity\StringField('API_SECRET_KEY'),
			new Entity\StringField('ACCESS_TOKEN'),
		);
	}
}
