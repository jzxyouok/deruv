<?php

namespace service;

use Yii;
use common\models\Category as CategoryModel;

class Category
{

	const CACHE_TREE = 'category:tree';
	const CACHE_CHILDS = 'category:childs';
	const CACHE_PARENTS = 'category:parents';

	private static $tree;

	public static function loadTree()
	{
		if (!is_object(self::$tree)) {
			self::$tree = new \wskm\Tree();
			self::$tree->setTree(self::getList(), 'id', 'parentid', 'name');
		}

		return self::$tree;
	}

	public static function getListOptions($showTop = true, $exceptid = false)
	{
		//return self::loadTree()->getOptions(0, 0, $except, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $showTop ? 'Top' : '');
		$data = self::getCache(self::CACHE_CHILDS);
		$options = [];
		$childs = isset($data[0]) ? $data[0] : '';

		if ($showTop) {
			$options[0] = \Wskm::t('Category Top');
		}

		$getLayer = function($id, $space = false) {
			return $space ? str_repeat($space, abs(self::getInfo($id)['layer'] - 1)) : '';
		};

		$layer = 0;
		foreach ($childs as $id) {
			if ($exceptid && ($id == $exceptid || in_array($id, self::getChilds($exceptid)))) {
				continue;
			}
			$info = self::getInfo($id);

			if ($id > 0 && ($layer <= 0 || $getLayer($id) <= $layer)) {
				$options[$id] = $getLayer($id, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;') . $info['name'];
			}
		}
		return $options;
	}

	public static function getList()
	{
		$list = [];
		$query = (new \yii\db\Query())
				->from(CategoryModel::tableName())
				->orderBy('parentid,sorting DESC');

		foreach ($query->each() as $item) {
			$list[$item['id']] = $item;
		}

		return $list;
	}
	
	public static function getParents($id)
	{
		return isset(self::getCache(self::CACHE_PARENTS)[$id]) ?
				self::getCache(self::CACHE_PARENTS)[$id] : [];
	}

	public static function getChilds($id)
	{
		return isset(self::getCache(self::CACHE_CHILDS)[$id]) ?
				self::getCache(self::CACHE_CHILDS)[$id] : [];
	}
	
	public static function getInfo($id)
	{
		if (!$id) {
			return false;
		}
		return isset(self::getCache()[$id]) ? self::getCache()[$id] : false;
	}

	public static function getInfoName($id)
	{
		$info = self::getInfo($id);

		return $info ? $info['name'] : \Wskm::t('Category Top');
	}

	public static function getListStatus()
	{
		return \wskm\Status::getEnableOrDisable();
	}

	public static function getInfoStatus($key)
	{
		return isset(self::getListStatus()[$key]) ? self::getListStatus()[$key] : '';
	}

	public static function setCache($key = self::CACHE_TREE)
	{
		if (!$key) {
			return false;
		}
		$list = self::getList();
		$tree = new \wskm\Tree();
		$tree->setTree($list, 'id', 'parentid', 'name');
		
		\wskm\Cache::set(self::CACHE_TREE, $tree->getTreeList());
		\wskm\Cache::set(self::CACHE_CHILDS, $tree->getChildList());
		\wskm\Cache::set(self::CACHE_PARENTS, $tree->getParentList());

		return \wskm\Cache::get($key);
	}

	public static function getCache($key = self::CACHE_TREE, $w = false)
	{
		$data = \wskm\Cache::get($key);
		if ($data === false || $w) {
			$data = self::setCache($key);
		}

		return $data;
	}

}