<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Affectation
 *
 * @ORM\Table(name="affectation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AffectationRepository")
 */
class Affectation
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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


   /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PointVente",inversedBy="affectations")
   */
    private $pointVente;


    /**
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Ressource",inversedBy="affectations")
   */
    private $ressource;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @var User
     */
    private $user;

        /**
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\Commende", mappedBy="affectation", cascade={"persist","remove"})
   */
    private $commendes;


        /**
     * Constructor
     */
    public function __construct()
    {
        $this->date = new \DateTime();
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
     * @param string $date
     *
     * @return Affectation
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }


 
    /**
     * Add commende
     *
     * @param \AppBundle\Entity\Commende $commende
     *
     * @return PointVente
     */
    public function addCommende(\AppBundle\Entity\Commende $commende)
    {
        $this->commendes[] = $commende;

        return $this;
    }

    /**
     * Remove commende
     *
     * @param \AppBundle\Entity\Commende $commende
     */
    public function removeCommende(\AppBundle\Entity\Commende $commende)
    {
        $this->commendes->removeElement($commende);
    }

    /**
     * Get commendes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCommendes()
    {
        return $this->commendes;
    }   

    /**
     * Set pointVente
     *
     * @param \AppBundle\Entity\PointVente $pointVente
     *
     * @return Affectation
     */
    public function setPointVente(\AppBundle\Entity\PointVente $pointVente = null)
    {
        $this->pointVente = $pointVente;

        return $this;
    }

    /**
     * Get pointVente
     *
     * @return \AppBundle\Entity\PointVente
     */
    public function getPointVente()
    {
        return $this->pointVente;
    }

    /**
     * Set ressource
     *
     * @param \AppBundle\Entity\Ressource $ressource
     *
     * @return Affectation
     */
    public function setRessource(\AppBundle\Entity\Ressource $ressource = null)
    {
        $this->ressource = $ressource;

        return $this;
    }

    /**
     * Get ressource
     *
     * @return \AppBundle\Entity\Ressource
     */
    public function getRessource()
    {
        return $this->ressource;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Affectation
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
}
