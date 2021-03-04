<?php

namespace App\Entity;

use App\Repository\LinkStatisticRepository;
use Doctrine\ORM\Mapping as ORM;
use WhichBrowser\Parser;

/**
 * @ORM\Entity(repositoryClass=LinkStatisticRepository::class)
 */
class LinkStatistic
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ipAddress;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $userAgent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $browser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $device;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $os;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Link::class, inversedBy="linkStatistics")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $link;

    /**
     * LinkStatistic constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(?string $browser): self
    {
        $this->browser = $browser;

        return $this;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(?string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getOs(): ?string
    {
        return $this->os;
    }

    public function setOs(?string $os): self
    {
        $this->os = $os;

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

    public function parseUA()
    {
        $uaParser = new Parser($this->userAgent);

        $this->os = $uaParser->os->toString();
        $this->device = $uaParser->device->toString() !== '' ? $uaParser->device->toString() : $uaParser->getType();
        $this->browser = $uaParser->browser->toString();
    }

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function setLink(?Link $link): self
    {
        $this->link = $link;

        return $this;
    }
}
