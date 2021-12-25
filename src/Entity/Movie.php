<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MovieRepository::class)
 */
class Movie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $moviename;

    /**
     * @ORM\Column(type="string", length=255)
     * * @Assert\Regex(
     *      pattern="/^MR[1-9]$/",
     *      message="Movie Room must be between MR1 and MR9"
     * )
     */
    private $movieroom;

    /**
     * @ORM\Column(type="string", length=255)
     * * @Assert\Regex(
     *     pattern="/^(1[7-9]|2[0-2]):[0-5][0-9]$/",
     *     message="The Start Time must be in this type 'HH:MM and between 17:00 and 22:00'"
     * )
     */
    private $starttime;

    /**
     * @ORM\Column(type="string", length=255)
     * * @Assert\Regex(
     *     pattern="/^(1[8-9]|2[0-3]):[0-5][0-9]$/",
     *     message="The End Time must be in this type 'HH:MM and between 18:00 and 23:00'" 
     * )
     */
    private $endtime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoviename(): ?string
    {
        return $this->moviename;
    }

    public function setMoviename(string $moviename): self
    {
        $this->moviename = $moviename;

        return $this;
    }

    public function getMovieroom(): ?string
    {
        return $this->movieroom;
    }

    public function setMovieroom(string $movieroom): self
    {
        $this->movieroom = $movieroom;

        return $this;
    }

    public function getStarttime(): ?string
    {
        return $this->starttime;
    }

    public function setStarttime(string $starttime): self
    {
        $this->starttime = $starttime;

        return $this;
    }

    public function getEndtime(): ?string
    {
        return $this->endtime;
    }

    public function setEndtime(string $endtime): self
    {
        $this->endtime = $endtime;

        return $this;
    }
}
