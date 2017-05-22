<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="dns_records")
 */
class DnsRecord
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
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;

	/**
	 * @ORM\Column(type="string", length=10)
	 */
	private $type;

	/**
	 * @ORM\Column(type="text")
	 */
	private $content;

	/**
	 * @ORM\Column(type="integer", options={"default": 600})
	 */
	private $ttl = 600;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $prio;

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
     * Set name
     *
     * @param string $name
     *
     * @return DnsRecord
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return DnsRecord
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
     * Set content
     *
     * @param string $content
     *
     * @return DnsRecord
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set ttl
     *
     * @param integer $ttl
     *
     * @return DnsRecord
     */
    public function setTtl($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Get ttl
     *
     * @return integer
     */
    public function getTtl()
    {
        return $this->ttl;
    }

    /**
     * Set prio
     *
     * @param integer $prio
     *
     * @return DnsRecord
     */
    public function setPrio($prio)
    {
        $this->prio = $prio;

        return $this;
    }

    /**
     * Get prio
     *
     * @return integer
     */
    public function getPrio()
    {
        return $this->prio;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return DnsRecord
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
     * @return DnsRecord
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
