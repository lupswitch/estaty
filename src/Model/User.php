<?php

namespace Estaty\Model;

use Estaty\Model\Location\Country;
use Estaty\Model\Property\Property;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @Entity(repositoryClass="Estaty\Repository\UserRepository")
 */
class User implements UserInterface
{
    const FACEBOOK = 'facebook';
    const GOOGLE   = 'google';
    const GITHUB   = 'github';

    private static $SUPPORTED_OAUTH_SERVICES = [
        self::FACEBOOK,
        self::GOOGLE,
        self::GITHUB,
    ];

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $name;

    /**
     * @Column(type="string", unique=true)
     * @var string
     */
    private $email;

    /**
     * @Column(type="string", nullable=true)
     * @var string
     */
    private $password;

    /**
     * @Column(type="simple_array", name="roles", nullable=true)
     * @var array
     */
    private $roles = [];

    /**
     * @Column(type="string", nullable=true, unique=true)
     * @var string
     */
    private $facebookUid;

    /**
     * @Column(type="string", nullable=true, unique=true)
     * @var string
     */
    private $googleUid;

    /**
     * @Column(type="string", nullable=true, unique=true)
     * @var string
     */
    private $githubUid;

    /**
     * @OneToMany(targetEntity="Estaty\Model\Property\Property", mappedBy="creator")
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $properties;

    /**
     * @ManyToOne(targetEntity="Estaty\Model\Location\Country")
     * @JoinColumn(name="countryId")
     * @var \Estaty\Model\Location\Country
     */
    private $country;

    public function __construct($email, $password, $name = null, array $roles = [])
    {
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setName($name);
        $this->setRoles($roles);
        $this->properties = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @codeCoverageIgnore
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields'  => ['email'],
            'message' => 'This email is already in registered! Try logging in.',
        ]));

        $metadata->addPropertyConstraints('email', [
            new Assert\NotBlank(),
            new Assert\Email(),
        ]);
        $metadata->addPropertyConstraints('password', [
            new Assert\NotBlank(),
            new Assert\Length([
                'min' => 8,
            ]),
        ]);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @var string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookUid()
    {
        return $this->facebookUid;
    }

    /**
     * @param string $facebookUid
     */
    public function setFacebookUid($facebookUid)
    {
        $this->facebookUid = $facebookUid;

        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleUid()
    {
        return $this->googleUid;
    }

    /**
     * @param string $googleUid
     */
    public function setGoogleUid($googleUid)
    {
        $this->googleUid = $googleUid;

        return $this;
    }

    /**
     * @return string
     */
    public function getGithubUid()
    {
        return $this->githubUid;
    }

    /**
     * @param string $githubUid
     */
    public function setGithubUid($githubUid)
    {
        $this->githubUid = $githubUid;

        return $this;
    }

    /**
     * @param  string $serviceName
     * @return bool
     */
    public function supportsOAuthService($serviceName)
    {
        return in_array($serviceName, self::$SUPPORTED_OAUTH_SERVICES);
    }

    public function getOAuthServiceUid($serviceName)
    {
        if (!$this->supportsOAuthService($serviceName)) {
            throw new \OutOfBoundsException(sprintf(
                '%s is not a supported OAuth service',
                $serviceName
            ));
        }

        return $this->{'get'.ucfirst($serviceName).'Uid'}();
    }

    /**
     * @param string $serviceName
     * @param string $uid
     */
    public function setOAuthServiceUid($serviceName, $uid)
    {
        if (!$this->supportsOAuthService($serviceName)) {
            throw new \OutOfBoundsException(sprintf(
                '%s is not a supported OAuth service',
                $serviceName
            ));
        }

        return $this->{'set'.ucfirst($serviceName).'Uid'}($uid);
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry(Country $country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Salt would be automaticaly generated from BCrypt
     *
     * @link http://symfony.com/doc/current/cookbook/security/entity_provider.html#the-data-model
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * For the purposes of UserInterface and the user provider
     * the unique username in this system is the email.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @codeCoverageIgnore
     */
    public function eraseCredentials()
    {

    }
}
