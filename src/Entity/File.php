<?php

namespace App\Entity;

use ZipArchive;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FileRepository;
use Exception;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 3)]
    private ?string $extension = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    static public function unzip(string $pathZip, string $pathDestination): string
    {
        $zip = new ZipArchive();
        try {
            $zip->open($pathZip);
            $zip->extractTo($pathDestination);
            $zip->close();
            return basename($pathZip) . " extrait !";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
}
