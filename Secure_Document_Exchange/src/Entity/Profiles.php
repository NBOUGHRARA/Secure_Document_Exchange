<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfilesRepository")
 * @UniqueEntity(
 *  fields={"profileCode"}, 
 *  message="Role already exists."
 * )
 */
class Profiles
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profileCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profileLabel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfileCode(): ?string
    {
        return $this->profileCode;
    }

    public function setProfileCode(string $profileCode): self
    {
        $this->profileCode = $profileCode;

        return $this;
    }

    public function getProfileLabel(): ?string
    {
        return $this->profileLabel;
    }

    public function setProfileLabel(string $profileLabel): self
    {
        $this->profileLabel = $profileLabel;

        return $this;
    }
}
