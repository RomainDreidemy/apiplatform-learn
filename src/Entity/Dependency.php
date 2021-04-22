<?php


namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ApiResource(
        collectionOperations: ['get' => ['normalization_context' => ['groups' => ['read:Dependenies']]]],
        itemOperations: ['get' => ['normalization_context' => ['groups' => ['read:Dependency']]]],
        paginationEnabled: false
    )
]
class Dependency
{
    #[
        ApiProperty(identifier: true),
        Groups(['read:Dependency', 'read:Dependenies'])
    ]
    private string $uuid;

    #[
        ApiProperty(description: 'Nom de la dépendance', ),
        Groups(['read:Dependency', 'read:Dependenies'])
    ]
    private string $name;

    #[
        ApiProperty(description: 'Version de la dépendance'),
        Groups(['read:Dependency'])
    ]
    private string $version;

    public function __construct(
        string $uuid,
        string $name,
        string $version
    )
    {
        $this->uuid = $uuid;
        $this->name     = $name;
        $this->version  = $version;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}