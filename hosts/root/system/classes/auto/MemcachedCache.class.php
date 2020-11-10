<?php

require_once 'Cache.class.php';

/**
 * Memcached distributed cache implementation.
 * 
 * @author vvelikodny
 */
class MemcachedCache implements Cache {
    /**
     * PHP lib name.
     */
    const EXT_NAME = 'memcache';

    /**
     * Default memcached port.
     */
    const DFLT_PORT = 11211;

    protected static $memcached;

    /** Memcached singleton instance. */
    protected static $instance;

    /** Constructor. */
    private function __construct() {
        Debug("Initializing memcahe system...");

        // Check library.
        if (!extension_loaded(self::EXT_NAME)) {
            throw new Exception(SPrintF("PHP extension %s not installed or enabled in your system.", self::EXT_NAME));
        }

        $Port = (IsSet($GLOBALS['HOST_CONF']['memcached.port'])?$GLOBALS['HOST_CONF']['memcached.port']:self::DFLT_PORT);

        // Check connection.
        self::$memcached = new Memcache();

        $connected = self::$memcached->connect('localhost', $Port, 1);
        if(!$connected) {
            throw new Exception(SPrintF("Could not connet to memcached [port=%s]", $Port));
        }

        $version = self::$memcached->getVersion();

        Debug(SPrintF('[Memcached]: memcached connected [version=%s]', $version));
    }

    /** */
    private function __clone() {}

    /**
     * Gets Memcached instance if exists, otherwise creates a new instance.
     * 
     * @return Memcached instance.
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }

    function add($key, $value, $time = 0) {
        // Checks args.
        $__args_types = Array('string', 'boolean,integer,string,array,object', 'integer');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);

        $key = SPrintF('[%s]-%s', HOST_ID, $key);
	
        $result = self::$memcached->add($key, $value, NULL, $time);

        if (!$result) {
            Debug(SPrintF('[MemcachedCache::add]: не удалось закешировать объект [key=%s]', $key));
	    # пробуем тупо удалить ключ и воткнуть значение заново, ибо ключи у memcache нельзя перезаписывать
	    $IsDelete = self::$memcached->delete($key);
	    $result = self::$memcached->add($key, $value, NULL, $time);
        }

        return $result;
    }


    function get($key) {
        // Check args.
        $__args_types = Array('string');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);

        $key = SPrintF('[%s]-%s', HOST_ID, $key);

        $result = self::$memcached->get($key);

        if (!$result) {
            Debug(SPrintF('[MemcachedCache::get]: не удалось извлечь объект [key=%s]', $key));
        }

        return $result;
    }

    function flush() {
        $result = self::$memcached->flush();

        if (!$result) {
            Debug('[MemoryCache_Flush]: не удалось очистить память');
        }

        return $result;
    }


    function getStatistic() {
        $result = self::$memcached->getStats();

        if($result === FALSE) {
            Debug('[MemcachedCache::getStatistic]: не удалось получить статистику кэшированной памяти');
        }else{
	    $result['type'] = 'memcache';
	}
        
        return $result;
    }
}
?>
