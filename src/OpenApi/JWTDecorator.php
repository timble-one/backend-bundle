<?php

namespace TimbleOne\BackendBundle\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

final class JWTDecorator implements OpenApiFactoryInterface
{
    public function __construct(private readonly OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $authPath = '/api/auth';
        $paths = $openApi->getPaths();
        $loginPath = $paths->getPath($authPath) ?? new PathItem();

        $postOperation = $loginPath->getPost();
        if ($postOperation instanceof Operation) {
            $responses = $postOperation->getResponses() ?? [];
            $responses[(string)HttpResponse::HTTP_OK] = [
                'description' => 'Authentication succeeded',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'token' => ['type' => 'string', 'readOnly' => true],
                                'id' => ['type' => 'string', 'readOnly' => true, 'example' => '/api/users/1'],
                            ],
                            'required' => ['token', 'id'],
                        ],
                    ],
                ],
            ];
            $postOperation = $postOperation->withResponses($responses);
            $paths->addPath($authPath, $loginPath->withPost($postOperation));
        }

        return $openApi;
    }
}

