<?php

namespace App\Entity;

use App\Repository\PostHashtagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostHashtagRepository::class)]
class PostHashtag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Hashtag $hashtag_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostId(): ?Post
    {
        return $this->post_id;
    }

    public function setPostId(?Post $post_id): static
    {
        $this->post_id = $post_id;

        return $this;
    }

    public function getHashtagId(): ?Hashtag
    {
        return $this->hashtag_id;
    }

    public function setHashtagId(?Hashtag $hashtag_id): static
    {
        $this->hashtag_id = $hashtag_id;

        return $this;
    }
}
