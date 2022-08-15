<?php

require_once 'env.php';

class PostController
{
    protected $redis;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect(SETTINGS['redis_host'], SETTINGS['redis_port']);
//        $this->redis->auth(SETTINGS['redis_password']);
    }

    /**
     * @param $key
     * @return false|mixed|Redis|string
     * @throws RedisException
     * Get value from redis by key
     */
    public function set_data($data)
    {
       foreach ($data as $key => $value) {
           $role = explode('_', $key)[0];
           $this->redis->set($value, $role);
       }
       return ['status' => true];
    }
}