<?
namespace Vettich\Autoposting\Posts\vk;
use Bitrix\Main\Entity;

class DBOptionTable extends \Vettich\Autoposting\DBase
{
	public static function getTableName()
	{
		return 'vettich_autoposting_posts_vk_option_v3';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\StringField('VK_PUBLICATION_MODE'),
			new Entity\StringField('VK_PUBLISH_DATE'),
			new Entity\StringField('VK_PHOTO'),
			new Entity\StringField('VK_PHOTOS'),
			new Entity\StringField('VK_LINK'),
			new Entity\TextField('VK_MESSAGE'),
			new Entity\StringField('VK_UTM_SOURCE'),
			new Entity\StringField('VK_UTM_MEDIUM'),
			new Entity\StringField('VK_UTM_CAMPAIGN'),
			new Entity\StringField('VK_UTM_TERM'),
			new Entity\StringField('VK_UTM_CONTENT'),
		);
	}
}
