<?
namespace Vettich\Autoposting\Posts\facebook;
use Bitrix\Main\Entity;

class DBOptionTable extends \Vettich\Autoposting\DBase
{
	public static function getTableName()
	{
		return 'vettich_autoposting_posts_facebook_option_v3';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\StringField('FB_PUBLICATION_MODE'),
			new Entity\StringField('FB_PUBLISH_DATE'),
			new Entity\StringField('FB_LINK'),
			new Entity\StringField('FB_PHOTO'),
			new Entity\StringField('FB_NAME'),
			new Entity\StringField('FB_DESCRIPTION'),
			new Entity\TextField('FB_MESSAGE'),
			new Entity\StringField('FB_UTM_SOURCE'),
			new Entity\StringField('FB_UTM_MEDIUM'),
			new Entity\StringField('FB_UTM_CAMPAIGN'),
			new Entity\StringField('FB_UTM_TERM'),
			new Entity\StringField('FB_UTM_CONTENT'),
		);
	}
}
