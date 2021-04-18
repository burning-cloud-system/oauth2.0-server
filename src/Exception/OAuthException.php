<?php
/**
 * @author Burning Cloud System <package@burning-cloud.net>
 * @copyright Copyright (c) 2020 Burning Cloud System.
 * @license http://mit-license.org/
 * 
 * @link https://github.com/burning-cloud-system/oauth2.0-server
 */

namespace BurningCloudSystem\OAuth2\Server\Exception;

use BurningCloudSystem\OAuth2\Server\Exception\ErrorType\AccessDenied;
use BurningCloudSystem\OAuth2\Server\Exception\ErrorType\InvalidClient;
use BurningCloudSystem\OAuth2\Server\Exception\ErrorType\InvalidRequest;
use BurningCloudSystem\OAuth2\Server\Exception\ErrorType\InvalidScope;
use BurningCloudSystem\OAuth2\Server\Exception\ErrorType\ServerError;
use BurningCloudSystem\OAuth2\Server\Exception\ErrorType\UnsupportedGrant;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class OAuthException extends Exception
{
    private int $httpStatusCode;

    private string $errorType;

    private ?string $hint;

    private ?string $redirectUri;

    private array $payload = [];

    private ServerRequestInterface $serverRequest;

    /**
     * Throw a new exception.
     *
     * @param string $message
     * @param integer $code
     * @param string $errorType
     * @param integer $httpStatusCode
     * @param string|null $hint
     * @param string|null $redirectUri
     * @param Throwable $previous
     */
    public function __construct(string $message, int $code, string $errorType, int $httpStatusCode = 400, ?string $hint = null, ?string $redirectUri = null, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->httpStatusCode = $httpStatusCode;
        $this->errorType      = $errorType;
        $this->hint           = $hint;
        $this->redirectUri    = $redirectUri;

        $this->payload = [
            'error'             => $errorType,
            'error_description' => $message
        ];
        if (!is_null($hint)) 
        {
            $this->payload['hint'] = $hint;
        }
    }

    /**
     * Returns the current payload.
     *
     * @return array
     */
    public function getPayload() : array
    {
        $payload = $this->payload;
        if (isset($payload['error_description']) && !isset($payload['message']))
        {
            $payload['message'] = $payload['error_description'];
        }

        return $payload;
    }

    /**
     * Updates the current payload.
     *
     * @param array $payload
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Set the server request that is responsible for generating the exception
     *
     * @param ServerRequestInterface $serverRequest
     */
    public function setServerRequest(ServerRequestInterface $serverRequest)
    {
        $this->serverRequest = $serverRequest;
    }

    /**
     * Unsupported grant error.
     *
     * @return OAuthException
     */
    public static function unsupportedGrant() : OAuthException
    {
        $hint = 'Check that all required parameters have been provided';
        return new static(UnsupportedGrant::Message,
                          UnsupportedGrant::Code,
                          UnsupportedGrant::ErrorType,
                          UnsupportedGrant::HttpStatusCode,
                          $hint);
    }

    /**
     * Invalid client error.
     *
     * @param string $parameter
     * @param string|null $hint
     * @param Throwable|null $previous
     * @return OAuthException
     */
    public static function invalidRequest(string $parameter, ?string $hint = null, ?Throwable $previous = null) : OAuthException
    {
        $hint = ($hint === null) ? sprintf('Check the `%s` parameter', $parameter) : $hint;
        return new static(InvalidRequest::Message, 
                          InvalidRequest::Code, 
                          InvalidRequest::ErrorType, 
                          InvalidRequest::HttpStatusCode, 
                          $hint, null, $previous);
    }

    /**
     * Invalid client error.
     *
     * @param ServerRequestInterface $request
     * @return OAuthException
     */
    public static function invalidClient(ServerRequestInterface $request) : OAuthException
    {
        $exception = new static(InvalidClient::Message,
                                InvalidClient::Code,
                                InvalidClient::ErrorType,
                                InvalidClient::HttpStatusCode);
        $exception->setServerRequest($request);
        return $exception;
    }

    /**
     * Invalid scope error.
     *
     * @param string $scope
     * @param string|null $redirectUri
     * @return OAuthException
     */
    public static function invalidScope(string $scope, ?string $redirectUri = null) : OAuthException
    {
        $hint = empty($scope) ? 'Specify a scope in the request or set a default scope'
                              : sprintf('Check the `%s` scope', 
                                htmlspecialchars($scope, ENT_QUOTES, 'UTF-8', false));

        return new static(InvalidScope::Message,
                          InvalidScope::Code,
                          InvalidScope::ErrorType,
                          InvalidScope::HttpStatusCode,
                          $hint, $redirectUri);
    }

    /**
     * Access denied.
     *
     * @param string|null $hint
     * @param string|null $redirectUri
     * @param Throwable|null $previous
     * @return OAuthException
     */
    public static function accessDenied(?string $hint = null, ?string $redirectUri = null, ?Throwable $previous = null) : OAuthException
    {
        return new static(AccessDenied::Message,
                          AccessDenied::Code,
                          AccessDenied::ErrorType,
                          AccessDenied::HttpStatusCode,
                          $hint, $redirectUri, $previous);
    }

    /**
     * Server error.
     *
     * @param string|null $hint
     * @param Throwable|null $previous
     * @return OAuthException
     */
    public static function serverError(?string $hint, ?Throwable $previous = null) : OAuthException
    {
        return new static(ServerError::Message,
                          ServerError::Code,
                          ServerError::ErrorType,
                          ServerError::HttpStatusCode,
                          $hint, null, $previous);
    }

    /**
     * Get all headers that have to be send with the error response.
     * RFC 6749, section 5.2.:
     * 
     * @return array
     */
    public function getHttpHeaders(): array
    {
        $headers = [
            'Content-type' => 'application/json',
        ];

        if ($this->errorType === InvalidClient::ErrorType && $this->serverRequest->hasHeader('Authorization') === true)
        {
            $authSchem = strpos($this->serverRequest->getHeader('Authorization')[0], 'Bearer') === 0 ? 'Bearer' : 'Basic';
            $headers['WWW-Authenticate'] = $authSchem . ' realm="OAuth"';
        }
        return $headers;
    }

    /**
     * Returns the HTTP status code to send when the exceptions is output.
     *
     * @return int
     */
    public function getHttpStatusCode() : int
    {
        return $this->httpStatusCode;
    }

    public function generateHttpResponse(ResponseInterface $response, $useFragment = false, $jsonOptions = 0)
    {
        $headers = $this->getHttpHeaders();
        $payload = $this->getPayload();

        if ($this->redirectUri !== null)
        {
            $q = $useFragment === true ? '#' : '?';
            $this->redirectUri .= (strstr($this->redirectUri, $q) === false) ? $q : '&';
            return $response->withStatus(302)->withHeader('Location', $this->redirectUri, http_build_query($payload));
        }

        foreach($headers as $header => $content)
        {
            $response = $response->withHeader($header, $content);
        }

        $responseBody = json_encode($payload, $jsonOptions) ?: 'JSON encoding of payload failed';
        $response->getBody()->write($responseBody);
        return $response->withStatus($this->getHttpStatusCode());
    }
}
