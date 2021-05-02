<?php


namespace App\OpenApi;


use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{

    public function __construct(private OpenApiFactoryInterface $decorated){}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $this->hidePaths($openApi);

        $this->setAuthSecurityScheme($openApi);

        $this->setLoginPath($openApi);

        return $openApi;
    }

    private function hidePaths(OpenApi $openApi)
    {
        /** @var PathItem $path */
        foreach ($openApi->getPaths()->getPaths() as $key => $path){
            if ($path->getGet() && $path->getGet()->getSummary() === 'hidden'){
                $openApi->getPaths()->addPath($key, $path->withGet(null));
            }
        }
    }

    private function setAuthSecurityScheme(OpenApi $openApi)
    {
        $schemas = $openApi->getComponents()->getSecuritySchemes();
        $schemas['bearerAuth'] = new \ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT'
        ]);
    }

    private function setLoginPath(OpenApi $openApi)
    {
        $this->addSchema($openApi, 'Credentials', new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'test@gmail.com'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'romain'
                ]
            ]
        ]));

        $this->addSchema($openApi, 'JWT', new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                ]
            ]
        ]));


        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogin',
                tags: ['Auth'],
                responses: [
                '200' => [
                    'description' => 'Utilisateur connecté',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/JWT'
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
    }

    private function addSchema(OpenApi $openApi, string $key,\ArrayObject $schema)
    {
        $schemas = $openApi->getComponents()->getSchemas();
        $schemas[$key] = $schema;
    }
}