<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="article")
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="preview_text")
     */
    protected $previewText;

    /**
     * @ORM\Column(type="string", name="full_text")
     */
    protected $fullText;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $author;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetimetz")
     */
    protected $date;
}