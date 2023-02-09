<?php

require_once 'Cache.class.php';

/**
 * APCu cache implementation.
 *
 * @author vvelikodny
 */
class APCuCache implements Cache {
    /**
     * PHP lib name.
     */
    const EXT_NAME = 'apcu';

    /** APCu cache singleton instance. */
    protected static $instance = NULL;

    /** Constructor. */
    private function __construct() {
        Debug("Initializing APCu cache...");

        if (!extension_loaded(self::EXT_NAME)) {
            throw new Exception(SPrintF("PHP extension %s not installed or enabled in your system.", self::EXT_NAME));
        }

        Debug("APCu cache has been initialized.");
    }

    /** */
    private function __clone() {}

    /**
     * Gets APCu instance if exists, otherwise creates a new instance.
     *
     * @return APCu cache instance.
     */
    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function add($key, $value, $ttl = 0) {
        $result = apcu_store($key, $value, $ttl);

        if (!$result) {
            Debug(SPrintF('[APCuCache::add]: не удалось закешировать объект [key=%s]', $key));
        }

        return $result;
    }

    public function flush() {
        Debug("Flush APCu cache.");
        return apcu_clear_cache();
    }

    public function get($key) {
        // Check args.
        $__args_types = Array('string');
        $__args__ = Func_Get_Args(); Eval(FUNCTION_INIT);

        $result = apcu_fetch($key);

        if (!$result && !Is_Array($result)) {
            Debug(SPrintF('[APCuCache::get]: не удалось извлечь объект [key=%s]', $key));
        }

        return $result;
    }

    public function getStatistic() {
        $result = Array('type'=>self::EXT_NAME);

        $cache_user = apcu_cache_info();
        $result['version'] = phpversion('apcu');
        $result['curr_items'] = $cache_user['num_entries'];
        $result['bytes'] = $cache_user['mem_size'];

        $mem = apcu_sma_info();

        $result['limit_maxbytes'] = $mem['avail_mem'];

        return $result;
    }
}
?>
