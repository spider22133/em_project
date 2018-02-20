<?
namespace Vettich\Autoposting;
use Bitrix\Main\Entity;
use Bitrix\Main\Type;

class DBLogsTable extends DBase
{
	public static function getTableName()
	{
		return 'vettich_autoposting_logs';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\StringField('NAME'),
			new Entity\DatetimeField('DATETIME', array(
				'default_value' => function () {
					return new Type\DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s');
				}
			)),
			new Entity\StringField('TYPE'),
			new Entity\TextField('TEXT'),
		);
	}
}
