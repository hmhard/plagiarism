<?php

namespace App\Entity;

use App\Repository\ProjectFileDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectFileDetailRepository::class)
 */
class ProjectFileDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Project::class, inversedBy="projectFileDetail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="integer")
     */
    private $noOfParagraph;

    /**
     * @ORM\Column(type="integer")
     */
    private $sentenceCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $wordCount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $distinctWordCount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getNoOfParagraph(): ?int
    {
        return $this->noOfParagraph;
    }

    public function setNoOfParagraph(int $noOfParagraph): self
    {
        $this->noOfParagraph = $noOfParagraph;

        return $this;
    }

    public function getSentenceCount(): ?int
    {
        return $this->sentenceCount;
    }

    public function setSentenceCount(int $sentenceCount): self
    {
        $this->sentenceCount = $sentenceCount;

        return $this;
    }

    public function getWordCount(): ?int
    {
        return $this->wordCount;
    }

    public function setWordCount(int $wordCount): self
    {
        $this->wordCount = $wordCount;

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

    public function getDistinctWordCount(): ?int
    {
        return $this->distinctWordCount;
    }

    public function setDistinctWordCount(?int $distinctWordCount): self
    {
        $this->distinctWordCount = $distinctWordCount;

        return $this;
    }
}
