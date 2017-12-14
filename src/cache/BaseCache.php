<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/3
 * Time: 17:21
 */

namespace toolbox\cache;


abstract class BaseCache
{
    protected $totalCacheKey;
    /**
     * @var string 默认为类名
     */
    protected $cacheKey;
    /**
     * @var string 前缀
     */
    protected $cacheKeySuffix="";
    protected $cacheKeyPrefix="";

    /**
     * @var bool 是否使用PHP静态变量缓存
     */
    protected $usePHPCache=true;
    /**
     * @var int 缓存时间
     */
    protected $remain=3600;

    /**
     * @var int 10分钟
     */
    protected $phpCacheRefreshTime=600;

    protected $phpCacheTime=0;
    /**
     * php内存区，根据key->value进行存储.key=$cacheKey,value=实际值.
     * @var
     */
    static private $values;

    function __construct($cacheKeySuffix=null)
    {
        $this->cacheKey=str_replace('\\',"-",get_class($this));
        $this->cacheKeySuffix=$cacheKeySuffix;
        $this->totalCacheKey = $this->getTotalCacheKey();
    }

    /**
     * 同步缓存与库存
     * @title sync
     * @description
     * @author: daniel
     */
    public  function sync()
    {
        $data =$this->generateData();
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
        $currentTime=time();
        $value=false;
        if ($this->usePHPCache)
        {
            //PHP缓存有缓存时间，且缓存没有过期
           if ($this->phpCacheTime&&$currentTime<$this->phpCacheTime+$this->phpCacheRefreshTime)
           {
               $value=isset(self::$values[$this->totalCacheKey])? self::$values[$this->totalCacheKey]:null;
           }
        }
        if($value!==false&&$value!==null)
        {
            return $value;
        }else{
            $value = cache($this->getTotalCacheKey());
            if ($value) {
                if ($this->usePHPCache)
                {
                    self::$values[$this->totalCacheKey]=$value;
                }
            }else{
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
        if ($this->usePHPCache)
        {
            self::$values[$this->totalCacheKey]=$value;
        }
        cache($this->getTotalCacheKey(), $value,$this->remain);
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
    public function getTotalCacheKey(){
        $key=$this->cacheKey;
        if ($this->cacheKeyPrefix)
        {
            $key=$this->cacheKeyPrefix.$key;
        }
        if ($this->cacheKeySuffix)
        {
            $key=$key.$this->cacheKeySuffix;
        }
        return $key;
    }

}