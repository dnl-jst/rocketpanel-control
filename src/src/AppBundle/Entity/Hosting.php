<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="hostings")
 */
class Hosting
{
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id()
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 */
	private $hostname;

	/**
	 * @var Image
	 *
	 * @ORM\ManyToOne(targetEntity="Image")
	 * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
	 */
	private $image;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $webroot;

	/**
	 * @ORM\Column(type="string", length=255, options={"default": "none"})
	 */
	private $dnsMode = 'none';

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $dnsMaster;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $created;

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
     * Set hostname
     *
     * @param string $hostname
     *
     * @return Hosting
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get hostname
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Hosting
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set image
     *
     * @param \AppBundle\Entity\Image $image
     *
     * @return Hosting
     */
    public function setImage(\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \AppBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }


    /**
     * Set webroot
     *
     * @param string $webroot
     *
     * @return Hosting
     */
    public function setWebroot($webroot)
    {
        $this->webroot = $webroot;

        return $this;
    }

    /**
     * Get webroot
     *
     * @return string
     */
    public function getWebroot()
    {
        return $this->webroot;
    }

    /**
     * Set dnsMode
     *
     * @param string $dnsMode
     *
     * @return Hosting
     */
    public function setDnsMode($dnsMode)
    {
        $this->dnsMode = $dnsMode;

        return $this;
    }

    /**
     * Get dnsMode
     *
     * @return string
     */
    public function getDnsMode()
    {
        return $this->dnsMode;
    }

    /**
     * Set dnsMaster
     *
     * @param string $dnsMaster
     *
     * @return Hosting
     */
    public function setDnsMaster($dnsMaster)
    {
        $this->dnsMaster = $dnsMaster;

        return $this;
    }

    /**
     * Get dnsMaster
     *
     * @return string
     */
    public function getDnsMaster()
    {
        return $this->dnsMaster;
    }

}
