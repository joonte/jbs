<?php
/**
 * Memcached distributed cache implementation.
 * 
 * @author vvelikodny
 */
class Memcached implements Cache {
    /** Memcached singleton instance. */
    protected static $instance;

    /** Constructor. */
    private function __construct() {}

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

        $link = &Link_Get('MemoryCache');

        if($link === FALSE) {
            return ERROR;
        }

        if (!Function_Exists('MemCache_Connect')) {
            $link = FALSE;

            return ERROR | @Trigger_Error('[MemoryCache_Add]: модуль кеширования не установлен');
        }

        if (!Is_Object($link)) {
            $port = (
                IsSet($GLOBALS['HOST_CONF']['memcached.port']) ? $GLOBALS['HOST_CONF']['memcached.port'] : 11211
            );

            $link = @MemCache_Connect('localhost', $port, 0x1);

            if (!$link) {
              $link = FALSE;

              return ERROR | @Trigger_Error('[MemoryCache_Add]: не удалось подключиться к серверу кеширования');
            }

            $version = @MemCache_Get_Version($link);

            Debug(SPrintF('[MemoryCache_Add]: соединение с сервером кеширования установлено версия (%s)', $version));
        }

        $key = SPrintF('[%s]-%s', HOST_ID, $key);

        $result = @MemCache_Add($link, $key, $value, NULL, $time);

        if (!$result) {
            return ERROR | @Trigger_Error('[MemoryCache_Add]: не удалось закешировать объект');
        }

        return TRUE;
    }


    function get($key) {
        // Check args.
        $__args_types = Array('string');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);

        $link = &Link_Get('MemoryCache');

        if ($link === FALSE) {
            return ERROR;
        }

        if(!Function_Exists('MemCache_Connect')) {
            $link = FALSE;

            return ERROR | @Trigger_Error('[MemoryCache_Get]: модуль кеширования не установлен');
        }

        if (!Is_Object($link)) {
            $port = (
                IsSet($GLOBALS['HOST_CONF']['memcached.port']) ? $GLOBALS['HOST_CONF']['memcached.port'] : 11211
            );

            $link = @MemCache_Connect('localhost', $port, 0x1);

            if (!$link) {
                $link = NULL;

                return ERROR | @Trigger_Error('[MemoryCache_Get]: не удалось подключиться к серверу кеширования');
            }

            $version = @MemCache_Get_Version($link);
            
            Debug(SPrintF('[MemoryCache_Get]: соединение с сервером кеширования установлено версия (%s)', $version));
        }

        $key = SPrintF('[%s]-%s', HOST_ID, $key);

        $result = @MemCache_Get($link, $key);

        if ($result === FALSE) {
            return ERROR | @Trigger_Error('[MemoryCache_Get]: не удалось извлечь объект');
        }

        return $result;
    }

    function flush() {
        $link = &Link_Get('MemoryCache');

        if ($link === FALSE) {
            return ERROR;
        }

        if (!Function_Exists('MemCache_Connect')) {
            $link = FALSE;

            return ERROR | @Trigger_Error('[MemoryCache_Flush]: модуль кеширования не установлен');
        }

        if (!Is_Object($link)) {
            $port = (
                IsSet($GLOBALS['HOST_CONF']['memcached.port']) ? $GLOBALS['HOST_CONF']['memcached.port'] : 11211
            );

            $link = @MemCache_Connect('localhost', $port, 0x1);

            if(!$link){
              $link = NULL;

              return ERROR | @Trigger_Error('[MemoryCache_Flush]: не удалось подключиться к серверу кеширования');
            }

            $version = @MemCache_Get_Version($link);

            SPrintF('[MemoryCache_Flush]: соединение с сервером кеширования установлено версия (%s)', $version);
        }

        $result = @MemCache_Flush($link);

        if ($result === FALSE) {
            return ERROR | @Trigger_Error('[MemoryCache_Flush]: не удалось очистить память');
        }

        return TRUE;
    }


    function getStatistic() {
        $link = &Link_Get('MemoryCache');

        if ($link === FALSE) {
            return ERROR;
        }

        if (!Function_Exists('MemCache_Connect')) {
            $link = FALSE;

            return ERROR | @Trigger_Error('[MemoryCache_Get_Stats]: модуль кеширования не установлен');
        }

        if (!Is_Object($link)) {
            $port = (
                IsSet($GLOBALS['HOST_CONF']['memcached.port']) ? $GLOBALS['HOST_CONF']['memcached.port'] : 11211
            );

            $link = @MemCache_Connect('localhost', $port, 0x1);

            if(!$Link){

              $Link = NULL;

              return ERROR | @Trigger_Error('[MemoryCache_Get_Stats]: не удалось подключиться к серверу кеширования');
            }

            $Version = @MemCache_Get_Version($Link);

            SPrintF('[MemoryCache_Get_Stats]: соединение с сервером кеширования установлено версия (%s)',$Version);
        }

        $Result = @Memcache_Get_Stats($Link);
        if($Result === FALSE)
        return ERROR | @Trigger_Error('[MemoryCache_Get_Stats]: не удалось получить статистику кэшированной памяти');
        
        return $Result;
    }
}
?>
