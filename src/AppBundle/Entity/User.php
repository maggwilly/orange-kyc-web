<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;


/**
 * /**
 * @ORM\Table(name="user_account")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
  *@ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	

    /**
     * @var string
     * @ORM\Column(name="nom", type="string", length=255,nullable=true)
     */
    private $nom;

   /**
     * @var string
     * @ORM\Column(name="ville", type="string", length=255,nullable=true)
     */
    private $ville;

   /**
     * @var string
     * @ORM\Column(name="secteur", type="string", length=255,nullable=true)
     */
    private $secteur;

   /**
     * @var string
     * @ORM\Column(name="type", type="string", length=255,nullable=true)
     */
    private $type;


   /**
     * @var string
     * @ORM\Column(name="phone", type="string", length=255,nullable=true)
     */
    private $phone;

     /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255,unique=true)
     */
    protected $username;

        /**
     * @var string
     *
     * @ORM\Column(name="password", type="string")
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;


     /**
     * @var string
     *
     * @ORM\Column(name="usernameCanonical", type="string", length=180, unique=true, nullable=false)
     */
    protected $usernameCanonical;

   /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=180)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="emailCanonical", type="string", length=180, unique=true)
     */
    protected $emailCanonical;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean",nullable=true)
     */
    protected $enabled;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string")
     */
    protected $salt;


   /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastLogin", type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmationToken", type="string", length=180,unique=true ,nullable=true)
     */
    protected $confirmationToken;

   /**
     * @var \DateTime
     *
     * @ORM\Column(name="passwordRequestedAt", type="datetime" , nullable=true)
     */
    protected $passwordRequestedAt;


    /**
     * @var bool
     *
     * @ORM\Column(name="locked", type="boolean",nullable=true)
     */
    protected $locked;

    /**
     * @var bool
     *
     * @ORM\Column(name="expired", type="boolean",nullable=true)
     */
    protected $expired;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiresAt", type="datetime",nullable=true)
     */
    protected $expiresAt;

     /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array")
     */
    protected $roles;

     /**
     * @var bool
     *
     * @ORM\Column(name="credentialsExpired", type="boolean",nullable=true)
     */
    protected $credentialsExpired;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="credentialsExpireAt", type="datetime",nullable=true)
     */
    protected $credentialsExpireAt;


            /**
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\PointVente", mappedBy="user", cascade={"persist","remove"})
   */
    private $pointVentes;

    /**
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\Ressource", mappedBy="user", cascade={"persist","remove"})
   */
    private $ressources;

    /**
     * Constructor
     */
 
 public function __construct()
    {
        parent::__construct();

    }

 /**
  * @ORM\PrePersist()
 */
 public function prePersist(){
      
 } 
  
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Client
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Client
     */
    public function setUsername($username)
    {
        $this->username = $username;
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





    public function __toString()
    {
        return $this->getUsername();
    }

      /**
     * Get parties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParties()
    {
        return $this->parties;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Client
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function setLocked($locked)
    {
        $this->locked=$locked;
        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean 
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime 
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Get credentialsExpired
     *
     * @return boolean 
     */
    public function getCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    /**
     * Get credentialsExpireAt
     *
     * @return \DateTime 
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }



        /**
     * Set ville
     *
     * @param string $ville
     * @return PointVente
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string 
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return User
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }



    /**
     * Get pointVente
     *
     * @return \AppBundle\Entity\PointVente
     */
    public function getPointVentes()
    {
        return $this->pointVentes;
    }   

    /**
     * Set secteur
     *
     * @param string $secteur
     *
     * @return User
     */
    public function setSecteur($secteur)
    {
        $this->secteur = $secteur;

        return $this;
    }

    /**
     * Get secteur
     *
     * @return string
     */
    public function getSecteur()
    {
        return $this->secteur;
    }
}
