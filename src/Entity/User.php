<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("trick:read")]
    private int $id;

    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column]
    #[Groups("trick:read")]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\Length(
        min: 6,
        minMessage: 'Your password must be at least {{ 6 }} characters long',
    )]
    #[ORM\Column]
    private string $password;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserMessage::class)]
    private Collection $messages;

    #[ORM\Column(type: 'boolean')]
    private $is_verified = false;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups("comment:read")]
    private string $username;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Trick::class)]
    private Collection $tricks;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("comment:read")]
    private ?string $avatar = null;

    #[ORM\ManyToMany(targetEntity: Trick::class, mappedBy: 'contributors')]
    private Collection $trick_contribution;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->tricks = new ArrayCollection();
        $this->trick_contribution = new ArrayCollection();
    }

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
        return (string) $this->username;
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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * @return Collection<int, UserMessage>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(UserMessage $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(UserMessage $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $is_verified): self
    {
        $this->is_verified = $is_verified;

        return $this;
    }

    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * @return Collection<int, Trick>
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks->add($trick);
            $trick->setUser($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->removeElement($trick)) {
            // set the owning side to null (unless already changed)
            if ($trick->getUser() === $this) {
                $trick->setUser(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Trick>
     */
    public function getTrickContribution(): Collection
    {
        return $this->trick_contribution;
    }

    public function addTrickContribution(Trick $trickContribution): self
    {
        if (!$this->trick_contribution->contains($trickContribution)) {
            $this->trick_contribution->add($trickContribution);
            $trickContribution->addContributor($this);
        }

        return $this;
    }

    public function removeTrickContribution(Trick $trickContribution): self
    {
        if ($this->trick_contribution->removeElement($trickContribution)) {
            $trickContribution->removeContributor($this);
        }

        return $this;
    }
}
