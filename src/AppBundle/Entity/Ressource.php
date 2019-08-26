<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ressource
 *
 * @ORM\Table(name="ressource")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RessourceRepository")
 */
class Ressource
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, unique=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="sexe", type="string", length=255)
     */
    private $sexe;

        /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User",inversedBy="ressources")
     * @var User
     */
    private $user;

        /**
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\Affectation", mappedBy="ressource", cascade={"persist","remove"})
   */
    private $affectations;

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
     * @return Ressource
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
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Ressource
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
     * @return Ressource
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
     * Constructor
     */
    public function __construct()
    {
        $this->affectations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Ressource
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
     * Add affectation
     *
     * @param \AppBundle\Entity\Affectation $affectation
     *
     * @return Ressource
     */
    public function addAffectation(\AppBundle\Entity\Affectation $affectation)
    {
        $this->affectations[] = $affectation;

        return $this;
    }

    /**
     * Remove affectation
     *
     * @param \AppBundle\Entity\Affectation $affectation
     */
    public function removeAffectation(\AppBundle\Entity\Affectation $affectation)
    {
        $this->affectations->removeElement($affectation);
    }

    /**
     * Get affectations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAffectations()
    {
        return $this->affectations;
    }
}
