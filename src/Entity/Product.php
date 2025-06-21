<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $price;

    // imagePath property removed as it's now handled by the ProductImage entity

    #[ORM\Column(type: 'integer')]
    private $stock;

    #[ORM\Column(type: 'string', length: 255)]
    private $category;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'boolean', options: ["default" => false])]
    private $isNew = false;

    #[ORM\Column(type: 'boolean', options: ["default" => false])]
    private $freeShipping = false;
    
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $shippingPrice = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrderItem::class)]
    private $orderItems;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductImage::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private $images;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductColor::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private $colors;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->colors = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    // getImagePath and setImagePath methods removed
    // Images are now managed through the ProductImage entity and the getImages() collection

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setProduct($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getProduct() === $this) {
                $orderItem->setProduct(null);
            }
        }

        return $this;
    }

    public function isNew(): ?bool
    {
        return $this->isNew;
    }

    public function setIsNew(bool $isNew): self
    {
        $this->isNew = $isNew;
        
        return $this;
    }
    
    public function isFreeShipping(): ?bool
    {
        return $this->freeShipping;
    }
    
    public function setFreeShipping(bool $freeShipping): self
    {
        $this->freeShipping = $freeShipping;
        
        return $this;
    }
    
    public function getShippingPrice(): ?string
    {
        return $this->shippingPrice;
    }
    
    public function setShippingPrice(?string $shippingPrice): self
    {
        $this->shippingPrice = $shippingPrice;
        
        return $this;
    }
    
    /**
     * @return Collection<int, ProductImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ProductImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(ProductImage $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }
    
    public function getPrimaryImage(): ?ProductImage
    {
        foreach ($this->images as $image) {
            if ($image->getIsPrimary()) {
                return $image;
            }
        }
        
        // If no primary image is set but images exist, return the first one
        if ($this->images->count() > 0) {
            return $this->images->first();
        }
        
        return null;
    }
    
    /**
     * @return Collection<int, ProductColor>
     */
    public function getColors(): Collection
    {
        return $this->colors;
    }

    public function addColor(ProductColor $color): self
    {
        if (!$this->colors->contains($color)) {
            $this->colors[] = $color;
            $color->setProduct($this);
        }

        return $this;
    }

    public function removeColor(ProductColor $color): self
    {
        if ($this->colors->removeElement($color)) {
            // set the owning side to null (unless already changed)
            if ($color->getProduct() === $this) {
                $color->setProduct(null);
            }
        }

        return $this;
    }
}
