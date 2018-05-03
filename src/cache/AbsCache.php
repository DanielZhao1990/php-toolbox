<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/3
 * Time: 12:30
 */

namespace toolbox\cache;


abstract class AbsCache
{
    protected $totalCacheKey;
    /**
     * @var string 默认为类名
     */
    protected $cacheKey;
    /**
     * @var string 前缀
     */
    protected $cacheKeySuffix = "";
    protected $cacheKeyPrefix = "";

    /**
     * @var bool 是否使用PHP静态变量缓存
     */
    protected $usePHPCache = true;
    /**
     * @var int 缓存时间
     */
    protected $remain = 3600;

    /**
     * @var int 10分钟
     */
    protected $phpCacheRefreshTime = 600;

    protected $cacheConfigName = '';
    /**
     * php内存区，根据key->value进行存储.key=$cacheKey,value=实际值.
     * @var
     */
    static private $values;
    static private $phpRefreshTimes;

    function __construct($cacheKeySuffix = null)
    {
        $this->cacheKey = str_replace('\\', "-", get_class($this));
        $this->cacheKeySuffix = $cacheKeySuffix;
        $this->totalCacheKey = $this->getTotalCacheKey();
    }

    /**
     * 同步缓存与库存
     * @title sync
     * @description
     * @author: daniel
     */
    public function sync()
    {
        $data = $this->generateData();
        $this->setValue($data);
    }

    /**
     * 取出缓存的值.
     * 1. 尝试从内存中取
     * 2. 尝试从Memcache中取
     * 3. 都没有则调用generateData进行生成.生成后调用$this->setValue.
     * @return mixed|string|void
     */
    public function getCacheValue()
    {
        $currentTime = time();
        $value = false;
        if ($this->usePHPCache) {
            $cacheTime = isset(self::$phpRefreshTimes[$this->totalCacheKey])? self::$phpRefreshTimes[$this->totalCacheKey]:0;
            //PHP缓存有缓存时间，且缓存没有过期
            if ($cacheTime && $currentTime < $cacheTime + $this->phpCacheRefreshTime) {
                $value = isset(self::$values[$this->totalCacheKey]) ? self::$values[$this->totalCacheKey] : null;
            }
        }
        if ($value !== false && $value !== null) {
            return $value;
        } else {
            $value = $this->cache($this->totalCacheKey);
            if ($value) {
                if ($this->usePHPCache) {
                    $this->updatePhpValue($value);
                }
            } else {
                $value = $this->generateData();
                $this->setValue($value);
            }
            return $value;
        }
    }

    /**
     * 写入内存变量，并刷新缓存.
     * @param $value
     */
    public function setValue($value)
    {
        if ($this->usePHPCache) {
            $this->updatePhpValue($value);
        }
        $this->cache($this->getTotalCacheKey(), $value);
    }


    public function updatePhpValue($value)
    {
        self::$values[$this->totalCacheKey] = $value;
        self::$phpRefreshTimes[$this->totalCacheKey] = time();
    }

    /**
     * 生成缓存数据
     * @return mixed
     */
    public abstract function generateData();

    /**
     * 返回缓存的key值
     * @return mixed
     */
    public function getTotalCacheKey()
    {
        $key = $this->cacheKey;
        if ($this->cacheKeyPrefix) {
            $key = $this->cacheKeyPrefix . "_" . $key;
        }
        if ($this->cacheKeySuffix) {
            $key = $key . "_" . $this->cacheKeySuffix;
        }
        return $key;
    }


    /**
     * 缓存方法
     * @description
     * @author: daniel
     * @param $name
     * @param string $value
     * @param null $options
     * @return mixed
     */
    public abstract function cache($name, $value = '', $options = null);
}