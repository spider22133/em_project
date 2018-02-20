<?
namespace Vettich\Autoposting\Posts\instagram;
use Bitrix\Main\Entity;

class DBOptionTable extends \Vettich\Autoposting\DBase
{
	public static function getTableName()
	{
		return 'vettich_autoposting_posts_instagram_option_v3';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\StringField('INSTAGRAM_PUBLICATION_MODE'),
			new Entity\StringField('INSTAGRAM_PHOTO'),
			new Entity\StringField('INSTAGRAM_PHOTO_OTHER'),
			new Entity\TextField('INSTAGRAM_MESSAGE'),
			new Entity\StringField('INSTAGRAM_UTM_SOURCE'),
			new Entity\StringField('INSTAGRAM_UTM_MEDIUM'),
			new Entity\StringField('INSTAGRAM_UTM_CAMPAIGN'),
			new Entity\StringField('INSTAGRAM_UTM_TERM'),
			new Entity\StringField('INSTAGRAM_UTM_CONTENT'),
		);
	}
}
