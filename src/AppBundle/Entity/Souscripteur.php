<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Souscripteur
 *
 * @ORM\Table(name="souscripteur")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SouscripteurRepository")
  *@ORM\HasLifecycleCallbacks()
 */
class Souscripteur
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="sexe", type="string", length=255, nullable=true)
     */
    private $sexe;

    /**
     * @var string
     *
     * @ORM\Column(name="age", type="string", length=255, nullable=true)
     */
    private $age;

    /**
     * @var string
     *
     * @ORM\Column(name="csp", type="string", length=255, nullable=true)
     */
    private $csp;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;
    /**
     * @var string
     *
     * @ORM\Column(name="assuranceactuelle", type="string", length=255, nullable=true)
     */
    private $assuranceactuelle;

            /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Produit")
   */
    private $produit;

    /**
     * @var int
     *
     * @ORM\Column(name="week", type="integer", nullable=true)
     */
    private $week;

        /**
     * @var int
     *
     * @ORM\Column(name="week_text", type="string", length=255, nullable=true)
     */
    private $weekText;

    /**
     * @var int
     *
     * @ORM\Column(name="month", type="integer", nullable=true)
     */
    private $month;

        /**
     * @ORM\ManyToOne(targetEntity="User")
     * @var User
     */
    protected $user;



    /**
* @ORM\PrePersist
*/
 public function prePersist(){
    $this->week =$this->date->format("W");
    $this->month =$this->date->format("M");

     $year=$this->date->format("Y");
    $date = new \DateTime();
    $date->setISODate($year, $this->week);
    $startDate=$date->format('d/m');
    $date->modify('+6 days');
    $endDate=$date->format('d/m');
    $this->weekText=$startDate.' - '.$endDate;
  }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Souscripteur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Souscripteur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Souscripteur
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set sexe
     *
     * @param string $sexe
     *
     * @return Souscripteur
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get sexe
     *
     * @return string
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set age
     *
     * @param string $age
     *
     * @return Souscripteur
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return string
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set csp
     *
     * @param string $csp
     *
     * @return Souscripteur
     */
    public function setCsp($csp)
    {
        $this->csp = $csp;

        return $this;
    }

    /**
     * Get csp
     *
     * @return string
     */
    public function getCsp()
    {
        return $this->csp;
    }

    /**
     * Set assuranceactuelle
     *
     * @param string $assuranceactuelle
     *
     * @return Souscripteur
     */
    public function setAssuranceactuelle($assuranceactuelle)
    {
        $this->assuranceactuelle = $assuranceactuelle;

        return $this;
    }

    /**
     * Get assuranceactuelle
     *
     * @return string
     */
    public function getAssuranceactuelle()
    {
        return $this->assuranceactuelle;
    }



    /**
     * Set produit
     *
     * @param \AppBundle\Entity\Produit $produit
     *
     * @return Souscripteur
     */
    public function setProduit(\AppBundle\Entity\Produit $produit = null)
    {
        $this->produit = $produit;

        return $this;
    }

    /**
     * Get produit
     *
     * @return \AppBundle\Entity\Produit
     */
    public function getProduit()
    {
        return $this->produit;
    }

       /**
     * Set date
     *
     * @param \DateTime $date
     * @return Prospect
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    } 

   public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }
  
}
