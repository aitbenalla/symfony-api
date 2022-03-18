<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ProfileController;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    ORM\Entity(repositoryClass: UserRepository::class),
    ApiResource(
        collectionOperations: [
            'profile' => [
                'pagination_enabled' => false,
                'path' => '/profile',
                'method' => 'get',
                'controller' => ProfileController::class,
                'read' => false,
                'openapi_context' => ['security'=>['cookieAuth'=>[]]],
            ]
        ],
        itemOperations: [
            'get' => [
                'controller' => NotFoundAction::class,
                'openapi_context' => ['summary'=>'hidden'],
                'read' => false,
                'output' => false
            ]
        ],
        normalizationContext: ['groups' => ['read:user']],
        security: 'is_granted("ROLE_USER")',

    )
]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:user'])]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['read:user'])]
    private $email;

    #[ORM\Column(type: 'json')]
    #[Groups(['read:user'])]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
