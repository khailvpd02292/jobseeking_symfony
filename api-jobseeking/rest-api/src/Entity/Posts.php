<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="posts")
 * @ORM\Entity
 */
class Posts
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="date")
     */
    private $deadline_submission;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $entitlements;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $skill_requirements;

    /**
     * @ORM\Column(type="date")
     */
    private $datepost;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     * })
     */
    private $User;


    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }


    /**
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of deadline_submission
     */ 
    public function getDeadline_submission()
    {
        return $this->deadline_submission;
    }

    /**
     * Set the value of deadline_submission
     *
     * @return  self
     */ 
    public function setDeadline_submission($deadline_submission)
    {
        $this->deadline_submission = $deadline_submission;

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

    /**
     * Get the value of entitlements
     */ 
    public function getEntitlements()
    {
        return $this->entitlements;
    }

    /**
     * Set the value of entitlements
     *
     * @return  self
     */ 
    public function setEntitlements($entitlements)
    {
        $this->entitlements = $entitlements;

        return $this;
    }

    /**
     * Get the value of skill_requirements
     */ 
    public function getSkill_requirements()
    {
        return $this->skill_requirements;
    }

    /**
     * Set the value of skill_requirements
     *
     * @return  self
     */ 
    public function setSkill_requirements($skill_requirements)
    {
        $this->skill_requirements = $skill_requirements;

        return $this;
    }

    /**
     * Get the value of datepost
     */ 
    public function getDatepost()
    {
        return $this->datepost;
    }

    /**
     * Set the value of datepost
     *
     * @return  self
     */ 
    public function setDatepost($datepost)
    {
        $this->datepost = $datepost;

        return $this;
    }

    /**
     * Get the value of User
     *
     * @return  User
     */ 
    public function getUser()
    {
        return $this->Customer;
    }

    /**
     * Set the value of User
     *
     * @param  User  $User
     *
     * @return  self
     */ 
    public function setUser(User $User)
    {
        $this->User = $User;

        return $this;
    }
}