<?php

namespace App\Entity;

use BenTools\WebPushBundle\Model\Subscription\UserSubscriptionInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WebPushUserSubscriptionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class WebPushUserSubscription implements UserSubscriptionInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="webPushUserSubscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $subscriptionHash;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $subscription = [];

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $label;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * WebPushUserSubscription constructor.
     * @param UserInterface $user
     * @param string $subscriptionHash
     * @param array $subscription
     */
    public function __construct(UserInterface $user, string $subscriptionHash, array $subscription)
    {
        $this->user = $user;
        $this->subscriptionHash = $subscriptionHash;
        $this->subscription = $subscription;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedTimestampOnCreate()
    {
        $this->setCreated(new \DateTime('now'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSubscriptionHash(): string
    {
        return $this->subscriptionHash;
    }

    public function setSubscriptionHash(string $subscriptionHash): self
    {
        $this->subscriptionHash = $subscriptionHash;

        return $this;
    }

    public function getSubscription(): ?array
    {
        return $this->subscription;
    }

    public function setSubscription(array $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEndpoint(): string
    {
        return $this->subscription['endpoint'];
    }

    /**
     * @inheritDoc
     */
    public function getPublicKey(): string
    {
        return $this->subscription['keys']['p256dh'];
    }

    /**
     * @inheritDoc
     */
    public function getAuthToken(): string
    {
        return $this->subscription['keys']['auth'];
    }

    /**
     * @inheritDoc
     */
    public function getContentEncoding(): string
    {
        return $this->subscription['content-encoding'] ?? 'aesgcm';
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }
}
