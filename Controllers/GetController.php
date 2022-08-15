<?php

require_once 'env.php';

class GetController
{
    protected $redis;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect(SETTINGS['redis_host'], SETTINGS['redis_port']);
        $this->redis->auth(SETTINGS['redis_password']);
    }

    /**
     * @param $key
     * @return false|mixed|Redis|string
     * @throws RedisException
     * Get value from redis by key
     */
    public function get_by_key($key)
    {
        if (is_string($key)) {
            $appName = explode('_', $key)[0];
            return [$appName => $this->redis->get($key)];
        }
        return false;
    }

    /**
     * @param $key
     * @return array
     * @throws RedisException
     *   Get values from redis for all apps
     */
    public function get_for_all_companies($key)
    {
        $data = [];
        foreach (COMPANIES as $company) {
            $data[$company] = $this->redis->get($company . '_' . $key);
        }
        return $data;
    }
}