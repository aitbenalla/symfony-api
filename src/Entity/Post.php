<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\PostController;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'post', //=> ['validation_groups' => [Post::class, 'validationGroups']] //=> ['validation_groups' => ['write:post']]
        'count' => [
            'method' => 'GET',
            'path' => '/posts/count',
            'controller' => PostController::class,
            'read' => false,
            'pagination_enabled' => false,
            'filters' => [],
            'openapi_context' => [
                'summary' => 'Publish Post',
                'parameters' => [
                    ['in' => 'query',
                    'name' => 'online',
                    'description' => 'Change Post Status',
                    'schema' => [
                        'type' => 'integer',
                        'maximum' => 1,
                        'minimum' => 0
                    ]]

                ],
                'responses' => [
                    '200' => [
                        'description' => 'Posts Count',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'integer',
                                    'example' => 10
                                ]
                            ]
                        ]
                    ]
                ]
            ]

        ]
    ],
    itemOperations: [
        'put',
        'delete',
        'get' => ['normalization_context' => ['groups' => ['read:posts' , 'read:post:item', 'read:Post'],'openapi_definition_name'=>'Detail']],
        'publish' => [
            'method' => 'POST',
            'path' => '/posts/{id}/publish',
            'controller' => PostController::class,
            'openapi_context' => [
                'summary' => 'Publish Post',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => []
                        ]
                    ]
                ]
            ]
        ]
    ],
    denormalizationContext: ['groups' => ['write:post']],
    normalizationContext: ['groups' => ['read:posts'], 'openapi_definition_name'=>'Collection'],
//    paginationClientItemsPerPage: true,
//    paginationItemsPerPage: 2,
//    paginationMaximumItemsPerPage: 2
),
    ApiFilter(SearchFilter::class, properties: ['id' => 'exact','title' => 'partial'])
]
class Post implements UserOwnedInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:posts'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[
        Groups(['read:posts', 'write:post']),
        Length(min: 5, groups: ['write:post'])
    ]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:post:item', 'write:post'])]
    private $content;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:posts', 'write:post'])]
    private $slug;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['read:post:item'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'posts')]
    #[Groups(['read:post:item', 'write:post']), Valid()]
    private $category;

    #[
        ORM\Column(type: 'boolean', options: ['default' => "0"]),
        Groups(['read:posts:User']),
        ApiProperty(openapiContext: ['type'=>'boolean', 'description'=>'Change Post Status'])]
    private $online;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    private $user;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->online = false;
    }

    public static function validationGroups(self $post): array
    {
        return ['write:post'];
    }

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
