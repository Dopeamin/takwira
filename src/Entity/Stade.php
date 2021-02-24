<?php

namespace App\Entity;

use App\Repository\StadeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * @ORM\Entity(repositoryClass=StadeRepository::class)
 */
class Stade implements UserInterface
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
    private $stadeName;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $stadeDescription;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stadePhone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stadeOwner;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stadeLocation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $stadeRating;

    /**
     * @ORM\Column(type="date")
     */
    private $stadeDate;
    
    /**
     * @ORM\Column(type="string")
     */
    private $brochureFilename;
    /**
     * @ORM\Column(type="string")
     */
    private $brochureFilename2;
/**
     * @ORM\Column(type="string")
     */
    private $brochureFilename3;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $stadeAvailable;

    /**
     * @ORM\Column(type="float")
     */
    private $x;

    /**
     * @ORM\Column(type="float")
     */
    private $y;

    /**
     * @ORM\Column(type="boolean")
     */
    private $featured;

    /**
     * @ORM\Column(type="integer")
     */
    private $superficie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $supplements;

    /**
     * @ORM\OneToMany(targetEntity=Orders::class, mappedBy="Stade",orphanRemoval=true)
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=Reviews::class, mappedBy="stade", orphanRemoval=true)
     */
    private $reviews;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getBrochureFilename()
    {
        return $this->brochureFilename;
    }

    public function setBrochureFilename($brochureFilename)
    {
        $this->brochureFilename = $brochureFilename;

        return $this;
    }
    public function getBrochureFilename2()
    {
        return $this->brochureFilename2;
    }

    public function setBrochureFilename2($brochureFilename)
    {
        $this->brochureFilename2 = $brochureFilename;

        return $this;
    }
    public function getBrochureFilename3()
    {
        return $this->brochureFilename3;
    }

    public function setBrochureFilename3($brochureFilename)
    {
        $this->brochureFilename3 = $brochureFilename;

        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStadeName(): ?string
    {
        return $this->stadeName;
    }

    public function setStadeName(string $stadeName): self
    {
        $this->stadeName = $stadeName;

        return $this;
    }

    public function getStadeDescription(): ?string
    {
        return $this->stadeDescription;
    }

    public function setStadeDescription(string $stadeDescription): self
    {
        $this->stadeDescription = $stadeDescription;

        return $this;
    }

    public function getStadePhone(): ?string
    {
        return $this->stadePhone;
    }

    public function setStadePhone(string $stadePhone): self
    {
        $this->stadePhone = $stadePhone;

        return $this;
    }

    public function getStadeOwner(): ?string
    {
        return $this->stadeOwner;
    }

    public function setStadeOwner(string $stadeOwner): self
    {
        $this->stadeOwner = $stadeOwner;

        return $this;
    }
    public function getStadeLocation(): ?string
    {
        return $this->stadeLocation;
    }

    public function setStadeLocation(string $stadeLocation): self
    {
        $this->stadeLocation = $stadeLocation;

        return $this;
    }

    public function getStadeRating(): ?int
    {
        return $this->stadeRating;
    }

    public function setStadeRating(?int $stadeRating): self
    {
        $this->stadeRating = $stadeRating;

        return $this;
    }

    public function getStadeDate(): ?\DateTimeInterface
    {
        return $this->stadeDate;
    }

    public function setStadeDate(\DateTimeInterface $stadeDate): self
    {
        $this->stadeDate = $stadeDate;

        return $this;
    }

    public function getStadeAvailable(): ?bool
    {
        return $this->stadeAvailable;
    }

    public function setStadeAvailable(?bool $stadeAvailable): self
    {
        $this->stadeAvailable = $stadeAvailable;

        return $this;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function setX(float $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?float
    {
        return $this->y;
    }

    public function setY(float $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getFeatured(): ?bool
    {
        return $this->featured;
    }
    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;

        return $this;
    }

    public function getSuperficie(): ?int
    {
        return $this->superficie;
    }

    public function setSuperficie(int $superficie): self
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getSupplements(): ?string
    {
        return $this->supplements;
    }

    public function setSupplements(string $supplements): self
    {
        $this->supplements = $supplements;

        return $this;
    }

    /**
     * @return Collection|Orders[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setStade($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getStade() === $this) {
                $order->setStade(null);
            }
        }

        return $this;
    }
    public function getUsername(){}
    /**
     * @return Collection|Reviews[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setStade($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getStade() === $this) {
                $review->setStade(null);
            }
        }

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    public function eraseCredentials()
    {
    }
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        if($this->id == 21){
            $roles[] = 'ROLE_ADMIN';
        }
        return array_unique($roles);
    }
}
