<?php
/**
 * Manage of system cache.
 *
 * @author vvelikodny
 */
class CacheManager {
    /** Cache singleton instance. */
    protected static $instance = NULL;

    public static function init() {
        if (self::$instance === NULL) {
            try {
                Debug("Start initializing cache system.");

                if (extension_loaded(APCCache::EXT_NAME)) {
                    self::$instance = APCCache::getInstance();
                }
                else if (extension_loaded(Memcached::EXT_NAME)) {
                    self::$instance = Memcached::getInstance();
                }
                else {
                    throw new Exception("Any supported cache not installed in your sysytem.");
                }

                Debug("Cache system has been initialized.");
            }
            catch (Exception $e) {
                Debug("Cache system has not been installed: ".$e->getTraceAsString());
            }
        }

        return self::$instance;
    }

    protected function __construct() { }
    
    /**
     * Adds new pair key/value to cache.
     *
     * @return
     */
    public static function add($key, $value, $ttl = 0) {
        if (self::isEnabled()) {
            //Debug(sprintf("Adds new key/value to cache [key=%s, ttl=%d]", $key, $ttl));
            
            return self::$instance->add($key, $value, $ttl);
        }
    }

    /**
     * Gets value for given key from cache.
     *
     * @return
     */
    public static function get($key) {
        if (self::isEnabled()) {
            //Debug(sprintf("Gets value from cache [key=%s]", $key));
            
            return self::$instance->get($key);
        }

        return ERROR;
    }

    public static function flush() {
        if (self::isEnabled()) {
            Debug("Flush the system cache.");

            return self::$instance->flush();
        }
    }

    public static function getStat() {
        if (self::isEnabled()) {
            return self::$instance->getStatistic();
        }

        return ERROR;
    }

    public static function isEnabled() {
        return IsSet(self::$instance);
    }
}
?>
