<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Exceptions\JobException;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={"get", "post"},
 *     itemOperations={"get", "delete", "put"},
 *     normalizationContext={"groups"={"job:read"}, "swagger_definition_name"="READ"},
 *     denormalizationContext={"groups"={"job:write"}, "swagger_definition_name"="WRITE"},
 *     attributes={
 *          "pagination_items_per_page"=10
 *     }
 * )
 * @ApiFilter(BooleanFilter::class, properties={"isPublished"})
 * @ApiFilter(SearchFilter::class, properties={"title":"partial", "description":"partial"})
 * @ApiFilter(RangeFilter::class, properties={"priceMinimum", "priceMaximum"})
 * @ApiFilter(PropertyFilter::class)
 *
 * @ORM\Entity(repositoryClass="App\Repository\JobRepository")
 */
class Job
{
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
     * @
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
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

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
     */
    public function __construct(string $title)
    {
        $this->title = $title;
        if (empty($this->title)){
            throw JobException::EmptyParamsException();
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
