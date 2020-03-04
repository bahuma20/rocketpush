<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleItemRepository")
 */
class ScheduleItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $rbtvId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRbtvId(): ?int
    {
        return $this->rbtvId;
    }

    public function setRbtvId(int $rbtvId): self
    {
        $this->rbtvId = $rbtvId;

        return $this;
    }

    public function getSent(): ?bool
    {
        return $this->sent;
    }

    public function setSent(bool $sent): self
    {
        $this->sent = $sent;

        return $this;
    }
}
