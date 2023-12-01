<?php

/*
 * TgService REST API Client
 *
 * Documentation
 * https://tg.bizandsoft.ru/documentation/lk_api
 * https://tg.bizandsoft.ru/documentation/tg_api
 */

namespace BizApi\TgService;

use BizApi\TgService\Contracts\ApiInterface;

class ApiClient implements ApiInterface
{

    /**
     * @var string
     */
    private $apiUrl = 'https://tg.bizandsoft.ru/api';

    /**
     * @var string
     */
    private $userLogin;

    /**
     * @var string
     */
    private $userPassword;

    /**
     * ApiClient constructor
     * @param string $userId
     * @param string $secret
     * @throws ApiClientException
     */
    public function __construct(string $userLogin, string $userPassword)
    {
        if (empty($userLogin) || empty($userPassword)) {
            throw new ApiClientException('Empty LOGIN or PASSWORD');
        }

        $this->userLogin = $userLogin;
        $this->userPassword = $userPassword;
    }

    /**
     * Form and send request to API service
     * @param string $path
     * @param string $method
     * @param array $data
     * @return array|null
     * @throws ApiClientException
     */
    protected function sendRequest(string $path, string $method = self::METHOD_GET, array $data = []): ?array
    {
        $url = $this->apiUrl . '/' . $path;
        $curl = curl_init();

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];


        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $this->userLogin . ":" . $this->userPassword);

        switch ($method) {
            case self::METHOD_POST:
                curl_setopt($curl, CURLOPT_POST, count($data));
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case self::METHOD_PATCH:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, self::METHOD_PATCH);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case self::METHOD_DELETE:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, self::METHOD_DELETE);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            default:
                if (!empty($data)) {
                    $url .= '?' . http_build_query($data);
                }
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);

        $response = curl_exec($curl);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $responseBody = json_decode(substr($response, $headerSize), true);
        $responseHeaders = substr($response, 0, $headerSize);
        $curlErrors = curl_error($curl);

        curl_close($curl);

        if ($httpCode >= 400) {
            throw new ApiClientException(
                'Request ' . $method . ' ' . $url . ' failed!',
                $httpCode,
                null,
                $responseBody,
                $responseHeaders,
                $curlErrors
            );
        }

        return empty($responseBody) ? null : $responseBody;
    }

     /**
     * Send GET request
     * @param string $path
     * @param array $data
     * @return array|null
     * @throws ApiClientException
     */
    public function get(string $path, array $data = []): ?array
    {
        return $this->sendRequest($path, self::METHOD_GET, $data);
    }

    /**
     * Send POST request
     * @param string $path
     * @param array $data
     * @return array|null
     * @throws ApiClientException
     */
    public function post(string $path, array $data = []): ?array
    {
        return $this->sendRequest($path, self::METHOD_POST, $data);
    }

    /**
     * Send PATCH request
     * @param string $path
     * @param array $data
     * @return array|null
     * @throws ApiClientException
     */
    public function patch(string $path, array $data = []): ?array
    {
        return $this->sendRequest($path, self::METHOD_PATCH, $data);
    }

    /**
     * Send DELETE request
     * @param string $path
     * @param array $data
     * @return array|null
     * @throws ApiClientException
     */
    public function delete(string $path, array $data = []): ?array
    {
        return $this->sendRequest($path, self::METHOD_DELETE, $data);
    }

    /**
     * Get all bots
     * @link https://tg.bizandsoft.ru/documentation/lk_api#get_bots
     * @param int $id
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::get()
     */
    public function getBots(int $id = null): ?array
    {
        $path = 'bots' . (!empty($id) ? '/' . $id : '');
        return $this->get($path);
    }

    /**
     * Get all templates
     * @link https://tg.bizandsoft.ru/documentation/lk_api#get_templates
     * @param int $id
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::get()
     */
    public function getTemplates(int $id = null): ?array
    {
        $path = 'templates' . (!empty($id) ? '/' . $id : '');

        return $this->get($path);
    }

    /**
     * Get all hooks
     * @link https://tg.bizandsoft.ru/documentation/lk_api#get_hooks
     * @param int $id
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::get()
     */
    public function getHooks(int $id = null): ?array
    {
        $path = 'hooks' . (!empty($id) ? '/' . $id : '');
        return $this->get($path);
    }

    /**
     * Get all channels
     * @link https://tg.bizandsoft.ru/documentation/lk_api#get_channels
     * @param int $id
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::get()
     */
    public function getChannels(int $id = null): ?array
    {
        $path = 'channels' . (!empty($id) ? '/' . $id : '');
        return $this->get($path);
    }

    /**
     * Edit bots
     * @link https://tg.bizandsoft.ru/documentation/lk_api#patch_bot
     * @param int $id
     * @param array $fields
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::patch()
     */
    public function editBots(int $id, array $fields): ?array
    {
        return $this->patch('bots/' . $id, $fields);
    }

    /**
     * Edit templates
     * @link https://tg.bizandsoft.ru/documentation/lk_api#patch_template
     * @param int $id
     * @param array $fields
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::patch()
     */
    public function editTemplates(int $id, array $fields): ?array
    {
        return $this->patch('templates/' . $id, $fields);
    }

    /**
     * Edit hooks
     * @link https://tg.bizandsoft.ru/documentation/lk_api#patch_hook
     * @param int $id
     * @param array $fields
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::patch()
     */
    public function editHooks(int $id, array $fields): ?array
    {
        return $this->patch('hooks/' . $id, $fields);
    }

    /**
     * Edit channels
     * @link https://tg.bizandsoft.ru/documentation/lk_api#patch_channel
     * @param int $id
     * @param array $fields
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::patch()
     */
    public function editChannels(int $id, array $fields): ?array
    {
        return $this->patch('channels/' . $id, $fields);
    }

    /**
     * Create bots
     * @link https://tg.bizandsoft.ru/documentation/lk_api#post_bot
     * @param string $name
     * @param string $token
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::post()
     */
    public function createBots(string $name, string $token): ?array
    {
        if (empty($name) || empty($token)) {
            throw new ApiClientException('Empty NAME or TOKEN');
        }

        return $this->post('bots', ['name' => $name, 'token' => $token]);
    }

    /**
     * Create templates
     * @link https://tg.bizandsoft.ru/documentation/lk_api#post_template
     * @param string $name
     * @param string $text
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::post()
     */
    public function createTemplates(string $name, string $text): ?array
    {
        if (empty($name) || empty($text)) {
            throw new ApiClientException('Empty NAME or TEXT');
        }

        return $this->post('templates', ['name' => $name, 'text' => $text]);
    }

    /**
     * Create hooks
     * @link https://tg.bizandsoft.ru/documentation/lk_api#post_hook
     * @param string $name
     * @param string $url
     * @param int $botId
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::post()
     */
    public function createHooks(string $name, string $url, int $botId): ?array
    {
        if (empty($name) || empty($url) || empty($botId)) {
            throw new ApiClientException('Empty NAME or URL or BOTID');
        }

        return $this->post('hooks', ['name' => $name, 'url' => $url, 'id_bot' => $botId]);
    }

    /**
     * Create channels
     * @link https://tg.bizandsoft.ru/documentation/lk_api#post_channel
     * @param string $name
     * @param string $url
     * @param int $botId
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::post()
     */
    public function createChannels(string $name, int $botId, int $chatId): ?array
    {
        if (empty($name) || empty($botId) || empty($chatId)) {
            throw new ApiClientException('Empty NAME or BOTID or CHATID');
        }

        return $this->post('channels', ['name' => $name, 'bot_id' => $botId, 'chat_id' => $chatId]);
    }

    /**
     * Send message telegram
     * @link https://tg.bizandsoft.ru/documentation/tg_api#send_message
     * @param int $botId
     * @param int $channelId
     * @param int $templateId
     * @param string $text
     * @param array $tags
     * @return array|null
     * @throws ApiClientException
     * @see ApiClient::post()
     */
    public function sendMessageTg(int $botId = null, int $channelId = null, int $templateId = null, string $text = null, array $tags = null): ?array
    {
        if (empty($botId) && empty($channelId)) {
            throw new ApiClientException('Empty BOTID or CHANNELID');
        }

        if (empty($templateId) && empty($text)) {
            throw new ApiClientException('Empty TEMPLATEDID or TEXT');
        }

        $data = array(
            'bot_id' => $botId,
            'channel_id' => $channelId,
            'template_id' => $templateId,
            'text' => $text,
            'tags' => $tags
        );

        return $this->post('send/message', $data);
    }
}