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
     * Set value on redis
     */
    public function set_data($data)
    {
        header("Content-Type: application/json");

        if ($data && is_array($data)) {
            $responseData = [];

            foreach ($data as $key => $value) {
                $role = explode('_', $key)[0];
                $res = $this->updateAppUserRole($value, $role);
                if ($res['status']) {
                    $this->redis->set($value, $role);
                    $responseData[$res['app']] = $res['message'];
                }
            }
            echo json_encode($responseData);
            exit;
        }
        echo json_encode(['status' => false, 'message' => 'wrong data']);
        exit;
    }

    /**
     * @param $dataStr
     * @param $role
     * @return array
     * send application request and update user role
     */
    public function updateAppUserRole($dataStr, $role)
    {
        $app = explode('&&', $dataStr);
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
        $response = json_decode(curl_exec($ch));
        curl_close($ch);

        if (!file_exists('logs')) {
            mkdir('logs', 0777, true);
        }

        $logData = $dataStr . " _____ $role _____ message: " . $response->message . "\n";
        $log = fopen('logs/connect_to_apps.log', 'a');
        fwrite($log, $logData);
        fclose($log);

        return ['app' => $app[0], 'message' => $response->message, 'status' => $response->status];
    }
}