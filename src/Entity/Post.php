<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\PostCountController;
use App\Controller\PostPublishController;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => 'read:Posts']],
        'post' => ['denormalization_context' => ['groups' => 'write:Post'], 'normalization_context' => ['groups' => 'write:Post']],
        'count' => [
            'security' => 'is_granted("ROLE_USER")',
            'method' => 'GET',
            'path' => '/posts/count',
            'controller' => PostCountController::class,
            'openapi_context' => [
                'summary' => 'Compte le nombre d\'article.',
                'parameters' => [],
                'responses' => [
                    '200' => [
                        'description' => 'Nombre de résultat',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'integer',
                                    'example' => 3
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:Post']],
            'security' => "is_granted(['ROLE_USER'])" ,
            'openapi_context' => [
                'security' => [['bearerAuth' => []], '']
            ]
        ],
        'put' => ['denormalization_context' => ['groups' => 'write:Post'], 'normalization_context' => ['groups' => 'write:Post']],
        'delete',
        'publish' => [
            'method' => 'POST',
            'path' => '/posts/{id}/publish',
            'controller' => PostPublishController::class,
            'openapi_context' => [
                'summary' => 'Permet de publier un article.',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [],
                        ]
                    ]
                ]
            ]
        ]
    ],
    paginationEnabled: false
),
ApiFilter(SearchFilter::class, properties: ['title' => 'partial']),
ApiFilter(OrderFilter::class, properties: ['title'])
]
class Post
{
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:Post', 'read:Posts'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups(['read:Post', 'read:Posts', 'write:Post']),
        Assert\Length(min: 3)
    ]
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups(['read:Post', 'read:Posts', 'write:Post']),
        Assert\Length(min: 3)
    ]
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    #[Groups(['read:Post', 'write:Post'])]
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:Post'])]
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:Post'])]
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts", cascade="persist")
     * @ORM\JoinColumn(nullable=true)
     */
    #[
        Groups(['read:Post', 'write:Post']),
        Assert\Valid
    ]
    private $category;

    /**
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    private $online = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): self
    {
        $this->online = $online;

        return $this;
    }
}
