<?php
namespace NewtimeEst\ErplyApi;

use NewtimeEst\ErplyApi\ErplyApiConnectInterface;

class ErplyApiConnect implements ErplyApiConnectInterface
{
    /**
     * @var string $username
     */
    protected $username;

    /**
     * @var string $password
     */
    protected $password;

    /**
     * @var string $clientCode
     */
    protected $clientCode;

    /**
     * @var string $url
     */
    protected $url;

    /**
     * @var array $topLevelParams
     */
    protected $topLevelParams = ['lang', 'clientCode', 'sessionKey', 'responseType', 'responseMode'];

    /**
     * @var string $sessionKey
     */
    private $sessionKey;

    /**
     * @var string $sessionKeyExpires
     */
    private $sessionKeyExpires;

    /**
     * @property string $username
     * @property string $password
     * @property string $clientCode
     * @property string $url
     */
    public function __construct($username = '', $password = '', $clientCode = '')
    {
        $this->username = $username;
        $this->password = $password;
        $this->clientCode = $clientCode;

        $this->url = "https://{$clientCode}.erply.com/api/";
    }

    /**
     * @property string $request
     * @property array $params
     */
    public function sendRequest($request = '', $params = [])
    {
        $params['request'] = $request;
        $params['clientCode'] = $this->clientCode;

        if ($request !== 'verifyUser') {
            $params['sessionKey'] = $this->getSessionKey();
        }

        $handle = curl_init($this->url);

        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $params);

        curl_setopt($handle, CURLOPT_HEADER, 0);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($handle, CURLOPT_TIMEOUT, 45);
        curl_setopt($handle, CURLOPT_FAILONERROR, true);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, false);

        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        //run
        $response = curl_exec($handle);
        $error = curl_error($handle);
        $errorNumber = curl_errno($handle);
        curl_close($handle);

        if ($error) {
            throw new Exception('CURL error: ' . $response . ':' . $error . ': ' . $errorNumber);
        }

        return json_decode($response, true);
    }

    /**
     * @return string
     */
    protected function getSessionKey()
    {
        if (!$this->isValidSession()) {
            $this->setSessionKey();
        }

        return $this->sessionKey;
    }

    /**
     * @throws Exception
     * @return string
     */
    protected function setSessionKey()
    {
        $user = $this->sendRequest('verifyUser', ['username' => $this->username, 'password' => $this->password]);

        if ($user['status']['responseStatus'] === 'error') {
            throw new Exception("Verify user failure, code: {$user['status']['errorCode']}");
        }

        $_SESSION['EAPISessionKey'][$this->clientCode][$this->username] = $user['records'][0]['sessionKey'];
        $_SESSION['EAPISessionKeyExpires'][$this->clientCode][$this->username] = time() + $user['records'][0]['sessionLength'] - 30;

        $this->sessionKey = $_SESSION['EAPISessionKey'][$this->clientCode][$this->username];
        $this->sessionKeyExpires = $_SESSION['EAPISessionKeyExpires'][$this->clientCode][$this->username];
    }

    /**
     * @return bool
     */
    private function isSetSession()
    {
        return isset($_SESSION['EAPISessionKey'][$this->clientCode][$this->username]);
    }

    /**
     * @return bool
     */
    private function isSetSessionExpiration()
    {
        return isset($_SESSION['EAPISessionKeyExpires'][$this->clientCode][$this->username]);
    }

    /**
     * @return bool
     */
    private function isSessionExpired()
    {
        return $_SESSION['EAPISessionKeyExpires'][$this->clientCode][$this->username] < time();
    }

    /**
     * @return bool
     */
    private function isValidSession()
    {
        return $this->isSetSession() && $this->isSetSessionExpiration() && !$this->isSessionExpired();
    }
}
