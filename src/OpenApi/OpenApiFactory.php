<?php

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use Symfony\Flex\Path;

class OpenApiFactory implements OpenApiFactoryInterface
{

    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        /** @var PathItem $path */
        foreach ($openApi->getPaths()->getPaths() as $key => $path) {
            if ($path->getGet() && $path->getGet()->getSummary() === 'hidden') {
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }
        //$openApi->getPaths()->addPath('/ping', new PathItem(null, 'Ping', 'Get Ping', new Operation('ping-id',[],[],'Get Ping')));
        $schemas = $openApi->getComponents()->getSecuritySchemes();
//        $schemas['cookieAuth'] = new \ArrayObject([
//            'type' => 'apiKey',
//            'in' => 'cookie',
//            'name' => 'PHPSESSID'
//        ]);
        $schemas['bearerAuth'] = new \ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'name' => 'JWT'
        ]);

        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'oussama@aitbenalla.com'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => '0000'
                ]
            ]
        ]);

        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true
                ]
            ]
        ]);

        $profileOperation = $openApi->getPaths()->getPath('/api/profile')->getGet()->withParameters([]);
        $profilePathItem = $openApi->getPaths()->getPath('/api/profile')->withGet($profileOperation);
        $openApi->getPaths()->addPath('/api/profile', $profilePathItem);

        $pathItem = new PathItem(

            post: new Operation(
                operationId: 'postApiLogin',
                tags: (array)'User',
                responses: [
                    '200' => [
                        'description' => 'Token JWT',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token'
                                ]
                            ]
                        ]
                    ]
                ],
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials'
                            ]
                        ]
                    ])
                )
            )
        );

        $openApi->getPaths()->addPath('/api/login', $pathItem);

        $pathItem = new PathItem(

            post: new Operation(
                operationId: 'postApiLogout',
                tags: (array)'User',
                responses: [
                    '204' => []
                ]
            )
        );

        $openApi->getPaths()->addPath('/logout', $pathItem);

        return $openApi;
    }
}