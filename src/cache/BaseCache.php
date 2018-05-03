<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/3
 * Time: 17:21
 */

namespace toolbox\cache;;

use think\Cache;

/**
 * Tp5使用缓存
 * @title toolbox\cache\BaseCache
 * @description
 * @author: daniel
 */
abstract class BaseCache extends AbsCache
{

    public function cache($name, $value = '', $options = null, $tag = null)
    {
        if ($value !== '') {
            Cache::store($this->cacheConfigName)->set($name, $value,$this->remain);
        } else {
            return Cache::store($this->cacheConfigName)->get($name);
        }
    }

}