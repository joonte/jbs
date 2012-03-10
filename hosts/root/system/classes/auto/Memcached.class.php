<?php
/**
 * Memcached distributed cache implementation.
 * 
 * @author vvelikodny
 */
class Memcached implements Cache {
    /**
     * PHP lib name.
     */
    const EXT_NAME = 'memcache';

    /**
     * Default memcached port.
     */
    const DFLT_PORT = 11211;

    protected static $link;

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
        $Link = @memcache_connect('localhost', $Port, 0x1);
        #---------------------------------------------------------------------------
        if(!$Link) {
            throw new Exception(SPrintF("Could not connet to memcached [port=%s]", $Port));
        }

        self::$link = $Link;

        $version = @MemCache_Get_Version(self::$link);

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

        $link = self::$link;

        if($link === FALSE) {
            return ERROR;
        }

        $key = SPrintF('[%s]-%s', HOST_ID, $key);

        $result = MemCache_Add($link, $key, $value, NULL, $time);

        if (!$result) {
            return ERROR | @Trigger_Error('[MemoryCache_Add]: не удалось закешировать объект');
        }

        return TRUE;
    }


    function get($key) {
        // Check args.
        $__args_types = Array('string');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);

        $link = self::$link;

        if ($link === FALSE) {
            return ERROR;
        }

        $key = SPrintF('[%s]-%s', HOST_ID, $key);

        $result = @MemCache_Get($link, $key);

        if ($result === FALSE) {
            return ERROR | @Trigger_Error('[MemoryCache_Get]: не удалось извлечь объект');
        }

        return $result;
    }

    function flush() {
        $link = self::$link;

        if ($link === FALSE) {
            return ERROR;
        }

        $result = @MemCache_Flush($link);

        if ($result === FALSE) {
            return ERROR | @Trigger_Error('[MemoryCache_Flush]: не удалось очистить память');
        }

        return TRUE;
    }


    function getStatistic() {
        $link = self::$link;

        if ($link === FALSE) {
            return ERROR;
        }

        $Result = @Memcache_Get_Stats($link);
        if($Result === FALSE)
        return ERROR | @Trigger_Error('[MemoryCache_Get_Stats]: не удалось получить статистику кэшированной памяти');
        
        return $Result;
    }
}
?>
