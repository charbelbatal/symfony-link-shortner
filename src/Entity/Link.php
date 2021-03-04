<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LinkRepository::class)
 */
class Link
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint", options={"unsigned": true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2083)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=9, unique=true, options={"collation": "utf8mb4_bin"})
     */
    private $shortCode;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $hits;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ipAddress;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=LinkStatistic::class, mappedBy="link", orphanRemoval=true, cascade={"persist"})
     */
    private $linkStatistics;

    /**
     * Link constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->hits = 0;
        $this->linkStatistics = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getShortCode(): ?string
    {
        return $this->shortCode;
    }

    public function setShortCode(string $shortCode): self
    {
        $this->shortCode = $shortCode;

        return $this;
    }

    public function getHits(): ?int
    {
        return $this->hits;
    }

    public function setHits(int $hits): self
    {
        $this->hits = $hits;

        return $this;
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

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|LinkStatistic[]
     */
    public function getLinkStatistics(): Collection
    {
        return $this->linkStatistics;
    }

    public function addLinkStatistic(LinkStatistic $linkStatistic): self
    {
        if (!$this->linkStatistics->contains($linkStatistic)) {
            $this->linkStatistics[] = $linkStatistic;
            $linkStatistic->setLink($this);
        }

        return $this;
    }

    public function removeLinkStatistic(LinkStatistic $linkStatistic): self
    {
        if ($this->linkStatistics->removeElement($linkStatistic)) {
            // set the owning side to null (unless already changed)
            if ($linkStatistic->getLink() === $this) {
                $linkStatistic->setLink(null);
            }
        }

        return $this;
    }
}
