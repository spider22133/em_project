<?
namespace Vettich\Autoposting\Posts\facebook;
use Bitrix\Main\Entity;

class DBTable extends \Vettich\Autoposting\DBase
{
	public static function getTableName()
	{
		return 'vettich_autoposting_posts_facebook';
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
			new Entity\TextField('GROUP_ID'),
			new Entity\TextField('APP_ID'),
			new Entity\TextField('APP_SECRET'),
			new Entity\TextField('ACCESS_TOKEN'),
		);
	}
}
