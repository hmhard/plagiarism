<?php

namespace App\Entity;

use App\Repository\SimilarityHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SimilarityHistoryRepository::class)
 */
class SimilarityHistory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="similarityHistories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $checkedProject;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="float")
     */
    private $similarity;

    /**
     * @ORM\Column(type="datetime")
     */
    private $checkedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $checkedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCheckedProject(): ?Project
    {
        return $this->checkedProject;
    }

    public function setCheckedProject(?Project $checkedProject): self
    {
        $this->checkedProject = $checkedProject;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getSimilarity(): ?float
    {
        return $this->similarity;
    }

    public function setSimilarity(float $similarity): self
    {
        $this->similarity = $similarity;

        return $this;
    }

    public function getCheckedAt(): ?\DateTimeInterface
    {
        return $this->checkedAt;
    }

    public function setCheckedAt(\DateTimeInterface $checkedAt): self
    {
        $this->checkedAt = $checkedAt;

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
}
