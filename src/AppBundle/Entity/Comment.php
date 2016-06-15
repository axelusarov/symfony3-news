<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="comments")
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Article", inversedBy="comments")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $article;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_on;

    /**
     * @ORM\Column(type="string")
     */
    protected $created_by_name;

    /**
     * @ORM\Column(type="text", name="text")
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=1000)
     */
    protected $text;

    public function __construct()
    {
        $this->created_on = new \Datetime('now');
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return Comment
     */
    public function setCreatedOn($createdOn)
    {
        $this->created_on = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->created_on;
    }

    /**
     * Set createdByName
     *
     * @param string $createdByName
     *
     * @return Comment
     */
    public function setCreatedByName($createdByName)
    {
        $this->created_by_name = $createdByName;

        return $this;
    }

    /**
     * Get createdByName
     *
     * @return string
     */
    public function getCreatedByName()
    {
        return $this->created_by_name;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set article
     *
     * @param \AppBundle\Entity\Article $article
     *
     * @return Comment
     */
    public function setArticle(\AppBundle\Entity\Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \AppBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }
}
