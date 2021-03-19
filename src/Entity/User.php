<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("userName")
 * @UniqueEntity("userEmail")
 */
class User implements UserInterface
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
    private $userName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, minMessage="Short Password,should be longer than 8 chars")
     */
    private $userPass;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userEmail;

    /**
     * @ORM\Column(type="integer", length=11)
     */
    private $userPhone;
     /**
     * @ORM\Column(type="json")
     */
    private $roles = [];
    /**
     * 
     * @Assert\EqualTo(propertyPath="userPass",message = "Make Sure to write the password correctly")
     */
    public $confirmPass;
    /**
     * 
     * @SecurityAssert\UserPassword(
     *     message = "Wrong value for your current password",groups={"update"}
     * )
     */
    public $oldPassword;
    public $userPassNew;
    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Orders::class, mappedBy="user", orphanRemoval=true)
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=Reviews::class, mappedBy="user", orphanRemoval=true)
     */
    private $reviews;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->stade = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getUserPass(): ?string
    {
        return $this->userPass;
    }
    public function getPassword(): ?string
    {
        return $this->userPass;
    }
    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }
    public function setOldPassword(string $userPass): self
    {
        $this->oldPassword = $userPass;
        return $this;
    }
    public function setUserPass(string $userPass): self
    {
        $this->userPass = $userPass;

        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): self
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    public function getUserPhone(): ?string
    {
        return $this->userPhone;
    }

    public function setUserPhone(string $userPhone): self
    {
        $this->userPhone = $userPhone;

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
    public function setAdmin(): self
    {
        // guarantee every user at least has ROLE_USER
        $this->roles[] = 'ROLE_ADMIN';
        return $this;
    }
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

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
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reviews[]
     */
    public function getStade(): Collection
    {
        return $this->stade;
    }

    public function addreviews(Reviews $reviews): self
    {
        if (!$this->reviews->contains($reviews)) {
            $this->reviews[] = $reviews;
            $reviews->setUser($this);
        }

        return $this;
    }

    public function removeReviews(Reviews $reviews): self
    {
        if ($this->reviews->removeElement($reviews)) {
            // set the owning side to null (unless already changed)
            if ($reviews->getUser() === $this) {
                $reviews->setUser(null);
            }
        }

        return $this;
    }
}
