<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commende
 *
 * @ORM\Table(name="commende")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommendeRepository")
  *@ORM\HasLifecycleCallbacks()
 */
class Commende
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="week", type="integer", nullable=true)
     */
    private $week;

        /**
     * @var int
     *
     * @ORM\Column(name="month_number", type="integer", nullable=true)
     */
    private $monthNumber;

        /**
     * @var int
     *
     * @ORM\Column(name="week_text", type="string", length=255, nullable=true)
     */
    private $weekText;


    /**
     * @var int
     *
     * @ORM\Column(name="type_insident", type="string", length=255, nullable=true)
     */
    private $typeInsident;

    /**
     * @var int
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="month", type="string", length=255, nullable=true)
     */
    private $month;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @var User
     */
    protected $user;

        /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Affectation",inversedBy="commendes")
   */
    private $affectation;

    /**
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\Ligne", mappedBy="commende", cascade={"persist","remove"})
   */
    private $lignes;


    /**
     * Constructor
     */
 public function __construct($produits = null, \AppBundle\Entity\Affectation $affectation = null)
    {
         $this->date= new \DateTime();
         $this->lignes = new \Doctrine\Common\Collections\ArrayCollection();
         $this->affectation = $affectation;
         if($produits)
        foreach ($produits as $key => $produit) {
            $this->addLigne(new Ligne($produit));
        }
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Commende
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

    /**
* @ORM\PrePersist
*/
 public function prePersist(){
    $this->week =$this->date->format("W");
    $this->month =$this->date->format("M");
    $this->monthNumber =$this->date->format("m");
     $year=$this->date->format("Y");
    $date = new \DateTime();
    $date->setISODate($year, $this->week);
    $startDate=$date->format('d/m');
    $date->modify('+6 days');
    $endDate=$date->format('d/m');
    $this->weekText=$startDate.' - '.$endDate;
    foreach ($this->lignes as $key => $ligne) {
        $ligne->setCommende($this);
    }
  }


    /**
     * Set week
     *
     * @param integer $week
     *
     * @return Commende
     */
    public function setWeek($week)
    {
        $this->week = $week;

        return $this;
    }

    /**
     * Get week
     *
     * @return integer
     */
    public function getWeek()
    {
        return $this->week;
    }


    /**
     * Set weekText
     *
     * @param string $weekText
     *
     * @return Commende
     */
    public function setWeekText($weekText)
    {
        $this->weekText = $weekText;

        return $this;
    }

    /**
     * Get weekText
     *
     * @return string
     */
    public function getWeekText()
    {
        return $this->weekText;
    }

    /**
     * Set month
     *
     * @param integer $month
     *
     * @return Commende
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Commende
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }



    /**
     * Add ligne
     *
     * @param \AppBundle\Entity\Ligne $ligne
     *
     * @return Commende
     */
    public function addLigne(\AppBundle\Entity\Ligne $ligne)
    {
        $ligne->setCommende($this);
        $this->lignes[] = $ligne;

        return $this;
    }

    /**
     * Remove ligne
     *
     * @param \AppBundle\Entity\Ligne $ligne
     */
    public function removeLigne(\AppBundle\Entity\Ligne $ligne)
    {
        $this->lignes->removeElement($ligne);
    }

    /**
     * Get lignes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLignes()
    {
        return $this->lignes;
    }

    /**
     * Set typeInsident
     *
     * @param string $typeInsident
     *
     * @return Commende
     */
    public function setTypeInsident($typeInsident)
    {
        $this->typeInsident = $typeInsident;

        return $this;
    }

    /**
     * Get typeInsident
     *
     * @return string
     */
    public function getTypeInsident()
    {
        return $this->typeInsident;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Commende
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set monthNumber
     *
     * @param integer $monthNumber
     *
     * @return Commende
     */
    public function setMonthNumber($monthNumber)
    {
        $this->monthNumber = $monthNumber;

        return $this;
    }

    /**
     * Get monthNumber
     *
     * @return integer
     */
    public function getMonthNumber()
    {
        return $this->monthNumber;
    }

    /**
     * Set affectation
     *
     * @param \AppBundle\Entity\Affectation $affectation
     *
     * @return Commende
     */
    public function setAffectation(\AppBundle\Entity\Affectation $affectation = null)
    {
        $this->affectation = $affectation;

        return $this;
    }

    /**
     * Get affectation
     *
     * @return \AppBundle\Entity\Affectation
     */
    public function getAffectation()
    {
        return $this->affectation;
    }
}
