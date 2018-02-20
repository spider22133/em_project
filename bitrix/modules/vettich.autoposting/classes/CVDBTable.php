<?
use Bitrix\Main\Entity;
use Bitrix\Main\Type;

class CVDBTable extends Entity\DataManager
{
	public static function getTableName()
	{
		return 'vettich_options';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true
			)),
			new Entity\TextField('NAME', array(
				'validation' => function(){
					return array();
				}
			)),
			new Entity\TextField('VALUE', array(
				'validation' => function(){
					return array();
				}
			)),
		);
	}
}
