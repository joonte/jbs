<?php

require_once 'Cache.class.php';

/**
 * Memcache distributed cache implementation.
 * 
 * @author vvelikodny
 */
class MemcacheCache implements Cache {
    /**
     * PHP lib name.
     */
    const EXT_NAME = 'memcache';

    /**
     * Default memcache port.
     */
    const DFLT_PORT = 11211;

    protected static $memcache;

    /** Memcache singleton instance. */
    protected static $instance;

    /** Constructor. */
    private function __construct() {
        Debug("Initializing memcahe system...");

        // Check library.
        if (!extension_loaded(self::EXT_NAME)) {
            throw new Exception(SPrintF("PHP extension %s not installed or enabled in your system.", self::EXT_NAME));
        }

        $Port = (IsSet($GLOBALS['HOST_CONF']['memcache.port'])?$GLOBALS['HOST_CONF']['memcache.port']:self::DFLT_PORT);

        // Check connection.
        self::$memcache = new Memcache();

        $connected = self::$memcache->connect('localhost', $Port, 1);
        if(!$connected) {
            throw new Exception(SPrintF("Could not connet to memcache [port=%s]", $Port));
        }

        $version = self::$memcache->getVersion();

        Debug(SPrintF('[Memcache]: memcache connected [version=%s]', $version));
    }

    /** */
    private function __clone() {}

    /**
     * Gets Memcache instance if exists, otherwise creates a new instance.
     * 
     * @return Memcache instance.
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

        $key = SPrintF('[%s]-[%s]-%s', HOST_ID, self::EXT_NAME ,$key);
	
        $result = self::$memcache->add($key, $value, NULL, $time);

        if (!$result) {
            Debug(SPrintF('[MemcacheCache::add]: не удалось закешировать объект [key=%s]', $key));
	    # пробуем тупо удалить ключ и воткнуть значение заново, ибо ключи у memcache нельзя перезаписывать
	    $IsDelete = self::$memcache->delete($key);
	    $result = self::$memcache->add($key, $value, NULL, $time);
        }

        return $result;
    }


    function get($key) {
        // Check args.
        $__args_types = Array('string');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);

        $key = SPrintF('[%s]-[%s]-%s', HOST_ID, self::EXT_NAME, $key);

        $result = self::$memcache->get($key);

        if (!$result) {
            Debug(SPrintF('[MemcacheCache::get]: не удалось извлечь объект [key=%s]', $key));
        }

        return $result;
    }

    function flush() {
        $result = self::$memcache->flush();

        if (!$result) {
            Debug('[MemoryCache_Flush]: не удалось очистить память');
        }

        return $result;
    }


    function getStatistic() {
        $result = self::$memcache->getStats();

        if($result === FALSE) {
            Debug('[MemcacheCache::getStatistic]: не удалось получить статистику кэшированной памяти');
        }else{
	    $result['type'] = self::EXT_NAME;
	}
        
        return $result;
    }
}
?>
