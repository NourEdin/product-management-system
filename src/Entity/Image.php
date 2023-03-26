<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $download_url = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDownloadUrl(): ?string
    {
        return $this->download_url;
    }

    public function setDownloadUrl(string $download_url): self
    {
        $this->download_url = $download_url;

        return $this;
    }
}
