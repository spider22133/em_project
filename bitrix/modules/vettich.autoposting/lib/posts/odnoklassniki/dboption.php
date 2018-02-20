<?
namespace Vettich\Autoposting\Posts\odnoklassniki;
use Bitrix\Main\Entity;

class DBOptionTable extends \Vettich\Autoposting\DBase
{
	public static function getTableName()
	{
		return 'vettich_autoposting_posts_odnoklassniki_option_v3';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\StringField('ODNOKLASSNIKI_PUBLICATION_MODE'),
			new Entity\StringField('ODNOKLASSNIKI_PUBLISH_DATE'),
			new Entity\StringField('ODNOKLASSNIKI_PHOTO'),
			new Entity\StringField('ODNOKLASSNIKI_PHOTO_OTHER'),
			new Entity\StringField('ODNOKLASSNIKI_LINK'),
			new Entity\TextField('ODNOKLASSNIKI_MESSAGE'),
			new Entity\StringField('ODNOKLASSNIKI_UTM_SOURCE'),
			new Entity\StringField('ODNOKLASSNIKI_UTM_MEDIUM'),
			new Entity\StringField('ODNOKLASSNIKI_UTM_CAMPAIGN'),
			new Entity\StringField('ODNOKLASSNIKI_UTM_TERM'),
			new Entity\StringField('ODNOKLASSNIKI_UTM_CONTENT'),
		);
	}
}
