<?
namespace Vettich\Autoposting\Posts\twitter;
use Bitrix\Main\Entity;

class DBTable extends \Vettich\Autoposting\DBase
{
	public static function getTableName()
	{
		return 'vettich_autoposting_posts_twitter';
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
			new Entity\StringField('API_KEY'),
			new Entity\StringField('API_SECRET'),
			new Entity\TextField('ACCESS_TOKEN'),
			new Entity\TextField('ACCESS_TOKEN_SECRET'),
		);
	}
}
