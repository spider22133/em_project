<?
namespace Vettich\Autoposting;
use Bitrix\Main\Entity;

class DBase extends Entity\DataManager
{
	public static function createTable()
	{
		try
		{
			$entity = self::getEntity();
			$connection = $entity->getConnection();
			if(!$connection->isTableExists($entity->getDBTableName()))
			{
				$sql = $entity->compileDbTableStructureDump();
				$connection->query($sql[0]);
				return $connection->isTableExists($entity->getDBTableName());
			}
			return true;
		}
		catch(\Exception $e){}
		return false;
	}

	public static function dropTable()
	{
		try
		{
			$entity = self::getEntity();
			$connection = $entity->getConnection();
			if($connection->isTableExists($entity->getDBTableName()))
			{
				$connection->dropTable($entity->getDBTableName());
				return !$connection->isTableExists($entity->getDBTableName());
			}
			return true;
		}
		catch(\Exception $e){}
		return false;
	}
}
