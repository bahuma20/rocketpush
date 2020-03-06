<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer",unique=true)
     */
    private $rocketbeansId;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=160)
     */
    private $rbtvAccessToken;

    /**
     * @ORM\Column(type="string", length=160)
     */
    private $rbtvRefreshToken;

    /**
     * @ORM\Column(type="integer")
     */
    private $rbtvExpires;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserSubscription", mappedBy="user", orphanRemoval=true)
     */
    private $userSubscriptions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lastSync;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $masterUser;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\WebPushUserSubscription", mappedBy="user", orphanRemoval=true)
     */
    private $webPushUserSubscriptions;

    public function __construct()
    {
        $this->userSubscriptions = new ArrayCollection();
        $this->webPushUserSubscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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
     * @see UserInterface
     */
    public function getPassword()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return int
     */
    public function getRocketbeansId()
    {
        return $this->rocketbeansId;
    }

    /**
     * @param mixed $rocketbeansId
     */
    public function setRocketbeansId($rocketbeansId): void
    {
        $this->rocketbeansId = $rocketbeansId;
    }

    /**
     * @return mixed
     */
    public function getRbtvAccessToken()
    {
        return $this->rbtvAccessToken;
    }

    /**
     * @param mixed $rbtvAccessToken
     */
    public function setRbtvAccessToken($rbtvAccessToken): void
    {
        $this->rbtvAccessToken = $rbtvAccessToken;
    }

    /**
     * @return mixed
     */
    public function getRbtvRefreshToken()
    {
        return $this->rbtvRefreshToken;
    }

    /**
     * @param mixed $rbtvRefreshToken
     */
    public function setRbtvRefreshToken($rbtvRefreshToken): void
    {
        $this->rbtvRefreshToken = $rbtvRefreshToken;
    }

    /**
     * @return mixed
     */
    public function getRbtvExpires()
    {
        return $this->rbtvExpires;
    }

    /**
     * @param mixed $rbtvExpires
     */
    public function setRbtvExpires($rbtvExpires): void
    {
        $this->rbtvExpires = $rbtvExpires;
    }

    /**
     * @return Collection|UserSubscription[]
     */
    public function getUserSubscriptions(): Collection
    {
        return $this->userSubscriptions;
    }

    public function addUserSubscription(UserSubscription $userSubscription): self
    {
        if (!$this->userSubscriptions->contains($userSubscription)) {
            $this->userSubscriptions[] = $userSubscription;
            $userSubscription->setUser($this);
        }

        return $this;
    }

    public function removeUserSubscription(UserSubscription $userSubscription): self
    {
        if ($this->userSubscriptions->contains($userSubscription)) {
            $this->userSubscriptions->removeElement($userSubscription);
            // set the owning side to null (unless already changed)
            if ($userSubscription->getUser() === $this) {
                $userSubscription->setUser(null);
            }
        }

        return $this;
    }

    public function getLastSync(): ?int
    {
        return $this->lastSync;
    }

    public function setLastSync(?int $lastSync): self
    {
        $this->lastSync = $lastSync;

        return $this;
    }

    public function getMasterUser(): ?bool
    {
        return $this->masterUser;
    }

    public function setMasterUser(?bool $masterUser): self
    {
        $this->masterUser = $masterUser;

        return $this;
    }

    /**
     * @return Collection|WebPushUserSubscription[]
     */
    public function getWebPushUserSubscriptions(): Collection
    {
        return $this->webPushUserSubscriptions;
    }

    public function addWebPushUserSubscription(WebPushUserSubscription $webPushUserSubscription): self
    {
        if (!$this->webPushUserSubscriptions->contains($webPushUserSubscription)) {
            $this->webPushUserSubscriptions[] = $webPushUserSubscription;
            $webPushUserSubscription->setUser($this);
        }

        return $this;
    }

    public function removeWebPushUserSubscription(WebPushUserSubscription $webPushUserSubscription): self
    {
        if ($this->webPushUserSubscriptions->contains($webPushUserSubscription)) {
            $this->webPushUserSubscriptions->removeElement($webPushUserSubscription);
            // set the owning side to null (unless already changed)
            if ($webPushUserSubscription->getUser() === $this) {
                $webPushUserSubscription->setUser(null);
            }
        }

        return $this;
    }
}
