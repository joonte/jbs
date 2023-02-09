<?php

require_once 'Cache.class.php';

/**
 * APC cache implementation.
 *
 * @author vvelikodny
 */
class APCCache implements Cache {
    /**
     * PHP lib name.
     */
    const EXT_NAME = 'apc';

    /** APC cache singleton instance. */
    protected static $instance = NULL;

    /** Constructor. */
    private function __construct() {
        Debug("Initializing APC cache...");

        if (!extension_loaded(self::EXT_NAME)) {
            throw new Exception(SPrintF("PHP extension %s not installed or enabled in your system.", self::EXT_NAME));
        }

        Debug("APC cache has been initialized.");
    }

    /** */
    private function __clone() {}

    /**
     * Gets APC instance if exists, otherwise creates a new instance.
     *
     * @return APC cache instance.
     */
    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function add($key, $value, $ttl = 0) {
        $result = apc_store($key, $value, $ttl);

        if (!$result) {
            Debug(SPrintF('[APCCache::add]: не удалось закешировать объект [key=%s]', $key));
        }

        return $result;
    }

    public function flush() {
        Debug("Flush APC cache.");
        return apc_clear_cache('user');
    }

    public function get($key) {
        // Check args.
        $__args_types = Array('string');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);

        $result = apc_fetch($key);

        if (!$result && !Is_Array($result)) {
            Debug(SPrintF('[APCCache::get]: не удалось извлечь объект [key=%s]', $key));
        }

        return $result;
    }

    public function getStatistic() {
	$result = Array('type'=>self::EXT_NAME);

        $cache_user = apc_cache_info('user', 1);

        $result['version'] = phpversion('apc');
        $result['curr_items'] = $cache_user['num_entries'];
        $result['bytes'] = $cache_user['mem_size'];

        $mem = apc_sma_info();

        $result['limit_maxbytes'] = $mem['avail_mem'];

        return $result;
    }
}
?>
