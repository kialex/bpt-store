<?php

namespace kialex\BptStore;

use GuzzleHttp;
use GuzzleHttp\RequestOptions;

class File
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * File constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $hash
     * @return string public file link
     * @see https://dev-api.bpt-store.com/api-docs/#/File/ResourceGetFileByContentHash
     */
    public function getPublicUrl(string $hash): string
    {
        return $this->client->resolveAliases("{baseUrl}/files/{$hash}");
    }

    /**
     *
     * @param string $uuid
     * @return string private file link
     * @see https://dev-api.bpt-store.com/api-docs/#/File/ResourceGetFile
     */
    public function getPrivateUrl(string $uuid): string
    {
        return $this->client->resolveAliases("{baseUrl}/users/{userUuid}/files/{$hash}");
    }

    /**
     * Add new file to BPT Store
     * @param string $path to the file to be added
     * @param integer $groupId ID of file group
     * @param bool $isPublic if u want to get a private access, set to `false`. Default `true` - file is public, u may
     * get the access via hash. See `getPublicUrl()` and `getPrivateUrl()` methods.
     * @return object of file data. Example:
     * ```
     * {
     *   "uuid": "6a29d6bd9267491ab84c6d65280fba1658b6ebbd1689275b408feab2f187e367",
     *   "name": "Example_File.png",
     *   "size": 117185,
     *   "mimeType": "image/png",
     *   "hash": "58b6ebbd1689275b408feab2f187e367"
     * }
     * ```
     * @see https://dev-api.bpt-store.com/api-docs/#/File/ResourceAddFile
     */
    public function add(string $path, int $groupId, bool $isPublic = true): bool
    {
        $response = $this->client->request('POST', '/users/{userUuid}/files', [
            RequestOptions::MULTIPART => [
                [
                    'name' => 'groupId',
                    'contents' => $groupId
                ],
                [
                    'name' => 'isPublic',
                    'contents' => $isPublic
                ],
                [
                    'name' => 'isPublic',
                    'contents' => fopen($path, 'r')
                ]
            ]
        ]);

        return GuzzleHttp\json_decode($response->getBody()->getContents());
    }
}