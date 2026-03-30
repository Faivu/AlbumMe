<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ExclusionPolicy('all')]
#[Hateoas\Relation(
    'self',
    href: new Hateoas\Route(
        'review_get',
        parameters: ['album_id' => 'expr(object.getAlbum().getId())', 'review_id' => 'expr(object.getId())']),
        exclusion: new Hateoas\Exclusion(groups: ['review_list', 'review_detail'])
)]
#[Hateoas\Relation(
    'album',
    href: new Hateoas\Route(
        'album_get',
        parameters: ['album_id' => 'expr(object.getAlbum().getId())']),
        exclusion: new Hateoas\Exclusion(groups: ['review_list', 'review_detail'])
)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Expose]
    #[Groups(['review_list', 'review_detail'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Expose]
    #[Groups(['review_list', 'review_detail'])]
    private ?string $comment = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Expose]
    #[Groups(['review_list', 'review_detail'])]
    private ?int $rating = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Expose]
    #[Groups(['review_list', 'review_detail'])]
    private ?User $reviewer = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Album $album = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReviewer(): ?User
    {
        return $this->reviewer;
    }

    public function setReviewer(?User $reviewer): static
    {
        $this->reviewer = $reviewer;

        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): static
    {
        $this->album = $album;

        return $this;
    }
}
