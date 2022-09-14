<?php

require_once 'env.php';

class GetController
{
    protected $redis;

    public function __construct()
    {
        $this->redis = new redis();
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
            $appName = explode('&&', $key)[0];
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
        foreach (APPS as $company) {
            $data[$company] = $this->redis->get($company . '&&' . $key);
        }
        return $data;
    }

    /**
     * Get value from redis by key
     *
     * @param $key
     * @return mixed
     * @throws RedisException
     */
    public function get_by_company_key($key)
    {
        if (is_string($key)) {
            return $this->redis->get($key);
        }
        return false;
    }
}