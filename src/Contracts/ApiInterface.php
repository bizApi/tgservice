<?php

/*
 * TgService REST API Interface
 *
 * Documentation
 * https://tg.bizandsoft.ru/documentation/lk_api
 * https://tg.bizandsoft.ru/documentation/tg_api
 *
 */

namespace BizApi\TgService\Contracts;

interface ApiInterface
{

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';

    /**
     * Send GET request
     * @param string $path
     * @param array $data
     * @return array|null
     */
    public function get(string $path, array $data = []): ?array;

    /**
     * Send POST request
     * @param string $path
     * @param array $data
     * @return array|null
     */
    public function post(string $path, array $data = []): ?array;

    /**
     * Send PATCH request
     * @param string $path
     * @param array $data
     * @return array|null
     */
    public function patch(string $path, array $data = []): ?array;

    /**
     * Send DELETE request
     * @param string $path
     * @param array $data
     * @return array|null
     */
    public function delete(string $path, array $data = []): ?array;

}
