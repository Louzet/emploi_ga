<?php

namespace App\Entity;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\JobRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Job
{
    use Timestamps;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="50", maxMessage="title must be described in 50 characters or less")
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"job:read", "job:write"})
     */
    private $title;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="text")
     * @Groups({"job:read"})
     */
    private $description;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value="0")
     *
     * @ORM\Column(type="integer")
     * @Groups({"job:read", "job:write"})
     */
    private $priceMinimum;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="integer")
     * @Groups({"job:read", "job:write"})
     */
    private $priceMaximum;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isPublished;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * Job constructor.
     * @param string $title
     * @param string $description
     * @param int $priceMinimum
     * @param int $priceMaximum
     */
    public function __construct(string $title, string $description, int $priceMinimum, int $priceMaximum)
    {
        $this->title = $title;
        $this->description = $description;
        $this->priceMinimum = $priceMinimum;
        $this->priceMaximum = $priceMaximum;
        if (empty($this->title) || empty($this->description) || empty($this->priceMinimum) || empty($this->priceMaximum)){
            throw new BadRequestHttpException("some fields are required", null, 400);
        }
        $this->createdAt = Carbon::now();
        $this->isPublished = false;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Job
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @Groups({"job:read"})
     * @return string|null
     */
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 70){
            return $this->description;
        }
        return substr($this->description, 0, 70). ' ...';
    }

    /**
     * @param string $description
     * @Groups({"job:write"})
     * @SerializedName("description")
     * @return Job
     */
    public function setTextDescription(string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPriceMinimum(): ?int
    {
        return $this->priceMinimum;
    }

    /**
     * @param int $priceMinimum
     * @return Job
     */
    public function setPriceMinimum(int $priceMinimum): self
    {
        $this->priceMinimum = $priceMinimum;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPriceMaximum(): ?int
    {
        return $this->priceMaximum;
    }

    /**
     * @param int $priceMaximum
     * @return Job
     */
    public function setPriceMaximum(int $priceMaximum): self
    {
        $this->priceMaximum = $priceMaximum;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Return created field in text format
     *
     * @return string
     * @Groups({"job:read"})
     */
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }

    /**
     * @return bool
     */
    public function getIsPublished(): bool
    {
        return $this->isPublished;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function publish(): void
    {
        $this->isPublished = true;
        $this->publishedAt = Carbon::now();
    }
}
