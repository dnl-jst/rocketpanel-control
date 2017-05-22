<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="hosting_aliases")
 */
class HostingAlias
{
	
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id()
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var Hosting
	 *
	 * @ORM\ManyToOne(targetEntity="Hosting")
	 * @ORM\JoinColumn(name="hosting_id", referencedColumnName="id")
	 */
	private $hosting;

	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 */
	private $hostname;

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
     * @return HostingAlias
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
     * @return HostingAlias
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
     * Set hosting
     *
     * @param \AppBundle\Entity\Hosting $hosting
     *
     * @return HostingAlias
     */
    public function setHosting(\AppBundle\Entity\Hosting $hosting = null)
    {
        $this->hosting = $hosting;

        return $this;
    }

    /**
     * Get hosting
     *
     * @return \AppBundle\Entity\Hosting
     */
    public function getHosting()
    {
        return $this->hosting;
    }

}
