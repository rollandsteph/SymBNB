<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Annonce", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $annonce;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message= "Attention ce champ doit être une date")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date(message= "Attention ce champ doit être une date")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonce $annonce): self
    {
        $this->annonce = $annonce;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist(){
        // si la date de création est vide on met la date du jour
        if(empty($this->createdAt)){
            $this->createdAt = new \DateTime();
        }

        if(empty($this->amout)){
            // le montant est le nombre de jours par le prix par jour
            $this->amount=$this->getDuration()*$this->annonce->getPrice();
        }
    }

    public function getDuration(){
        // $duration est un objet de type dateinterval
        $duration=$this->endDate->diff($this->startDate);
        return $duration->days;
    }

    /**
     * Retourne false si la réservation n'est pas possible et true dans le cas contraire
     *
     * @return boolean
     */
    public function isBookableDates(){

        // pointeur sur une fonction closure qui formate un jour de type DateTime en type String
        $formatDays=function($dayEnDateTime){
            return $dayEnDateTime->format('Y-m-d');
        };

        // il faut connaitre les dates impossibles pour l'annonce
        $NotAvailableDays=$this->annonce->getNotAvailableDays();    

        // transforme le tableau de jours en DateTime en tableau de string
        $NotAvailableDaysEnString= array_map($formatDays,$NotAvailableDays);

        // il faut comparer les dates choisies avec les dates impossibles
        $bookingDays = $this->getDays(); // on récupére la liste des jours de la réservation en cours

        // transforme le tableau de jours en DateTime en tableau de string
        $bookingDaysEnString= array_map($formatDays,$bookingDays);

        foreach($bookingDaysEnString as $day)
        {
            if(array_search($day,$NotAvailableDaysEnString)!== false){
                return false;
            }
        }
        return true;
    }

    /**
     * fonction qui permet de lister les jours de la réservation
     *
     * @return array
     */
    public function getDays(){
        // on construit la liste des jours de la réservation
        // sous forme de timestamp
        $joursDeLaReservation=range($this->startDate->getTimestamp(),
                                    $this->endDate->getTimestamp(),
                                    24*60*60); // temps d'une journée en milliseconde

        // on transforme ce tableau en tableau de DateTime 
        $days = array_map(function($dayTimestamp){
            return new \DateTime(date('Y-m-d',$dayTimestamp));
        },$joursDeLaReservation);
        
        return $days;
    }
}
