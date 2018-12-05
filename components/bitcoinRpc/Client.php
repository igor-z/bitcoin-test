<?php
namespace app\components\bitcoinRpc;

class Client implements ClientInterface
{
    protected $user;
    protected $password;
    protected $host;
    protected $port;
    protected $client;
    protected $calls;

    public function __construct(string $user, string $password, string $host, int $port)
    {
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;

        $this->client = new \GuzzleHttp\Client([
            'base_uri' => "http://{$this->host}:{$this->port}",
            'auth' => [$this->user, $this->password],
        ]);
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function addCall(string $id, string $method, array $arguments = []) : ClientInterface
    {
        $this->calls[] = [$id, $method, $arguments];

        return $this;
    }

    public function buildRequestData() : array
    {
        $requestData = [];

        foreach ($this->calls as $call) {
            $requestData[] = [
                'jsonrpc' => '2.0',
                'id' => $call[0],
                'method' => $call[1],
                'params' => $call[2],
            ];
        }

        return [
            'json' => $requestData,
        ];
    }

    /**
     * @param string $response
     * @return CallResponseInterface[]
     */
    public static function parseResponse(string $response) : array
    {
        $callRawResponses = json_decode($response, true);

        $callResponses = [];
        foreach ($callRawResponses as $callRawResponse) {
            $callResponses[] = new CallResponse($callRawResponse);
        }

        return $callResponses;
    }

    /**
     * @throws BadRemoteCallException
     * @param CallResponseInterface $callResponse
     */
    public static function checkCallResponseError(CallResponseInterface $callResponse)
    {
        if ($callResponse->getError()) {
            throw new BadRemoteCallException($callResponse);
        }
    }

    /**
     * @return CallResponseInterface[]
     */
    public function send() : array
    {
        $response = $this->client->post('', $this->buildRequestData());

        $callResponses = static::parseResponse($response->getBody()->getContents());

        foreach ($callResponses as $callResponse) {
            static::checkCallResponseError($callResponse);
        }

        return $callResponses;
    }
}