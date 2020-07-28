<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cv")
 * @ORM\Entity
 */
class Curriculum_Vitae
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url_cv;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @var Posts
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Posts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_posts", referencedColumnName="id")
     * })
     */
    private $Posts;

     /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;


    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get the value of url_cv
     */ 
    public function getUrl_cv()
    {
        return $this->url_cv;
    }

    /**
     * Set the value of url_cv
     *
     * @return  self
     */ 
    public function setUrl_cv($url_cv)
    {
        $this->url_cv = $url_cv;

        return $this;
    }

    /**
     * Get the value of Posts
     *
     * @return  Posts
     */ 
    public function getPosts()
    {
        return $this->Posts;
    }

    /**
     * Set the value of Posts
     *
     * @param  Posts  $Posts
     *
     * @return  self
     */ 
    public function setPosts(Posts $Posts)
    {
        $this->Posts = $Posts;

        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of description
     */ 
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */ 
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}
