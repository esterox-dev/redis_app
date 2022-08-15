<?php

require_once 'env.php';

class PostController
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
    public function set_data($data)
    {
       foreach ($data as $key => $value) {
           $role = explode('_', $key)[0];
           $this->redis->set($value, $role);
           $this->updateAppUserRole($value, $role);
       }
       return ['status' => true];
    }

    public function updateAppUserRole($app, $role) {
        $app = explode('&&', $app);
        $url = APPS_UPDATING_URLS[$app[0]];
        $data = [
            'company_id' => $app[1],
            'user_email' => $app[2],
            'user_role' => $role,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close ($ch);

        return $response;
    }
}