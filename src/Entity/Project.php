<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $doc_location;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="projects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ownerGroup;

    /**
     * @ORM\Column(type="datetime")
     */
    private $uploadedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $checkedAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $uploadedBy;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $checkedBy;

    /**
     * @ORM\OneToMany(targetEntity=SimilarityHistory::class, mappedBy="checkedProject")
     */
    private $similarityHistories;

    public function __construct()
    {
        $this->similarityHistories = new ArrayCollection();
    }
    public function __toString()
    {
        return $this->title;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDocLocation(): ?string
    {
        return $this->doc_location;
    }

    public function setDocLocation(?string $doc_location): self
    {
        $this->doc_location = $doc_location;

        return $this;
    }

    public function getOwnerGroup(): ?Group
    {
        return $this->ownerGroup;
    }

    public function setOwnerGroup(?Group $ownerGroup): self
    {
        $this->ownerGroup = $ownerGroup;

        return $this;
    }

    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeInterface $uploadedAt): self
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    public function getCheckedAt(): ?\DateTimeInterface
    {
        return $this->checkedAt;
    }

    public function setCheckedAt(?\DateTimeInterface $checkedAt): self
    {
        $this->checkedAt = $checkedAt;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUploadedBy(): ?User
    {
        return $this->uploadedBy;
    }

    public function setUploadedBy(?User $uploadedBy): self
    {
        $this->uploadedBy = $uploadedBy;

        return $this;
    }

    public function getCheckedBy(): ?User
    {
        return $this->checkedBy;
    }

    public function setCheckedBy(?User $checkedBy): self
    {
        $this->checkedBy = $checkedBy;

        return $this;
    }

    /**
     * @return Collection|SimilarityHistory[]
     */
    public function getSimilarityHistories(): Collection
    {
        return $this->similarityHistories;
    }

    public function addSimilarityHistory(SimilarityHistory $similarityHistory): self
    {
        if (!$this->similarityHistories->contains($similarityHistory)) {
            $this->similarityHistories[] = $similarityHistory;
            $similarityHistory->setCheckedProject($this);
        }

        return $this;
    }

    public function removeSimilarityHistory(SimilarityHistory $similarityHistory): self
    {
        if ($this->similarityHistories->removeElement($similarityHistory)) {
            // set the owning side to null (unless already changed)
            if ($similarityHistory->getCheckedProject() === $this) {
                $similarityHistory->setCheckedProject(null);
            }
        }

        return $this;
    }
}
