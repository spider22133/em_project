<?
namespace Vettich\Autoposting\Posts\twitter;
use Bitrix\Main\Entity;

class DBOptionTable extends \Vettich\Autoposting\DBase
{
	public static function getTableName()
	{
		return 'vettich_autoposting_posts_twitter_option_v3';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\StringField('TWITTER_PUBLICATION_MODE'),
			new Entity\StringField('TWITTER_PHOTO'),
			new Entity\StringField('TWITTER_PHOTOS'),
			new Entity\StringField('TWITTER_LINK'),
			new Entity\StringField('TWITTER_MESSAGE_SEP'),
			new Entity\TextField('TWITTER_MESSAGE'),
			new Entity\StringField('TWITTER_UTM_SOURCE'),
			new Entity\StringField('TWITTER_UTM_MEDIUM'),
			new Entity\StringField('TWITTER_UTM_CAMPAIGN'),
			new Entity\StringField('TWITTER_UTM_TERM'),
			new Entity\StringField('TWITTER_UTM_CONTENT'),
		);
	}
}
