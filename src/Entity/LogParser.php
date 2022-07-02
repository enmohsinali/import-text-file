<?php

namespace App\Entity;

use App\Repository\LogParserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogParserRepository::class)]
class LogParser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $FileName;

    #[ORM\Column(type: 'string', length: 255)]
    private $FilePath;

    #[ORM\Column(type: 'datetime')]
    private $parse_At;

    #[ORM\Column(type: 'integer')]
    private $pointer;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->FileName;
    }

    public function setFileName(string $FileName): self
    {
        $this->FileName = $FileName;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->FilePath;
    }

    public function setFilePath(string $FilePath): self
    {
        $this->FilePath = $FilePath;

        return $this;
    }

    public function getParseAt(): ?\DateTimeInterface
    {
        return $this->parse_At;
    }

    public function setParseAt(\DateTimeInterface $parse_At): self
    {
        $this->parse_At = $parse_At;

        return $this;
    }

    public function getPointer(): ?int
    {
        return $this->pointer;
    }

    public function setPointer(int $pointer): self
    {
        $this->pointer = $pointer;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
