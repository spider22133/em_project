<?
namespace Vettich\Autoposting\PostsBase;
use Vettich\Autoposting\PostingFunc;
use Vettich\Autoposting\PostingOption;
use Bitrix\Main\Entity;

class EventBase extends PostBase
{
	static function OnSavePostsParams($params)
	{
		$post_name = basename(str_replace('\\', '/', static::$namespace));
		if(PostingFunc::isHiddenPost($post_name))
			return false;
		PostingOption::SaveParams($params['ID'], static::$dbOptionTable, false);
	}

	static function OnDeletePosts($params)
	{
		PostingOption::Delete($params['ID'], static::$dbOptionTable);
	}

	static function OnPost($params)
	{
		$post = substr(static::$namespace, strrpos(static::$namespace, '\\') + 1);
		$post_accounts = $params['arPost']['ACCOUNT_'.strtoupper($post)];
		if(is_array($post_accounts) && !empty($post_accounts))
		{
			$cls = static::$namespace.'\Posting';
			if(method_exists($cls, 'post'))
				$params['arResult'][$post] = $cls::post(
					$params['arFields'],
					$post_accounts,
					$params['arPost'],
					$params['arSite'],
					$params['arOptionally'][$post]
				);
		}
		return true;
	}
}
