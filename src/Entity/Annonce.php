<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Booking;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnnonceRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *      fields ={"title"},
 *      message= "Une autre annonce possède déjà ce titre, merci de le modifier")
 */
class Annonce
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 10, 
     *      max = 255, 
     *      minMessage = "Le titre doit faire plus de {{ limit }} caractères", 
     *      maxMessage = "Le titre ne peut pas faire plus de {{ limit }} caractères")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     *  @Assert\Length(
     *      min = 100, 
     *      max = 255, 
     *      minMessage = "L'introduction doit faire plus de {{ limit }} caractères", 
     *      maxMessage = "L'introduction ne peut pas faire plus de {{ limit }} caractères")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="annonce", orphanRemoval=true, cascade={"persist","remove"})
     * @Assert\Valid
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="annonces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="annonce")
     */
    private $bookings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="annonce", orphanRemoval=true)
     */
    private $comments;

    /**
     * permet de renvoyer la moyenne des notes des commentaires
     *
     * @return float
     */
    public function getAvgRatings(){
        // on va faire la somme des valeurs de notation de l'ensemble des comments
        // pour cela on utilise array_reduce qui va réduire le tableau à une seule valeur
        // mais comments étant un arrayCollection et non un simple array
        // on utilise la méthode toArray pour le transformer en simple Array
        // le 0 en 3eme paramètre est la valeur par défaut car on doit avoir une valeur initial pour le cumul !
        $sum=array_reduce($this->comments->toArray(),function($total,$comment){
            return $total+$comment->getRating();
        },0);
        if(count($this->comments)>0) return $sum/count($this->comments);
        return 0; // retourne 0 dans le cas d'aucun commentaire
    }

    /**
     * retourne le commentaire d'un auteur sur cette annonce ou renvoi null
     *
     * @param User $author
     * @return Comment|null
     */
    public function getCommentFromAuthor(User $author){
        foreach($this->comments as $comment){
            if($comment->getAuthor() === $author) return $comment;
        }
        return null;
    }

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId() : ? int
    {
        return $this->id;
    }

    public function getTitle() : ? string
    {
        return $this->title;
    }

    public function setTitle(string $title) : self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug() : ? string
    {
        return $this->slug;
    }

    public function setSlug(string $slug) : self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice() : ? float
    {
        return $this->price;
    }

    public function setPrice(float $price) : self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction() : ? string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction) : self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent() : ? string
    {
        return $this->content;
    }

    public function setContent(string $content) : self
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage() : ? string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage) : self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms() : ? int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms) : self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     *@ORM\PrePersist
     *@ORM\PreUpdate
     */
    public function makeSlug()
    {
        $monSlug = new Slugify();
        $this->setSlug($monSlug->slugify($this->title));
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages() : Collection
    {
        return $this->images;
    }

    public function addImage(Image $image) : self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAnnonce($this);
        }

        return $this;
    }

    public function removeImage(Image $image) : self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAnnonce() === $this) {
                $image->setAnnonce(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAnnonce($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getAnnonce() === $this) {
                $booking->setAnnonce(null);
            }
        }

        return $this;
    }

    /**
     * Permet d'obtenir un tableau des jours qui ne sont pas disponibles pour cette annonce
     *
     * @return array // un tableau d'objets DateTime représentant les jours d'occupation
     */
    public function getNotAvailableDays(Booking $bookingAZapper=null){
        $noteAvailableDays=[];
        foreach($this->bookings as $booking){
            // le if permet en cas de modification d'un booking déja enregistrer de ne pas le prendre en compte
            // dans la liste des jours à éviter
            if($booking != $bookingAZapper){ 
            // calculer les jours entre la date d'arrivée et de départ
            //resultat contient donc une liste de jour au format timestamp
            $resultat=range($booking->getStartDate()->getTimestamp(),
                            $booking->getEndDate()->getTimestamp(),
                            24*60*60);

            // array_map transforme un tableau en ce même tableau mais formaté différemment
            // par une fonction(closure) qu'on lui passe en paramètre
            // objectif : tranformer le tableau de timestamp en tableau de DateTime
            $days=array_map(function($dayTimestamp){
                return new \DateTime(date('Y-m-d',$dayTimestamp));
            }, $resultat);

            // on ajoute les jours du tableau concernant le booking en cours
            // au tableau général de tous les jours de l'annonce
            $noteAvailableDays=array_merge($noteAvailableDays,$days);
        }
        }
        return $noteAvailableDays;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAnnonce($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAnnonce() === $this) {
                $comment->setAnnonce(null);
            }
        }

        return $this;
    }
}
