<?php

namespace Wit\Program\Account\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="invitations",
 *     indexes={
 *         @ORM\Index(
 *             name="invitation_role",
 *             columns={"role"}
 *         )
 *    }
 * )
 * @ORM\Entity
 */
class Invitation
{
    const ROLE_MENTOR = 'ROLE_MENTOR';
    const ROLE_MENTEE = 'ROLE_MENTEE';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=256)
     * @var string
     */
    private $code;

    /**
     * We can not identify mentor or person with email
     * email can be change'able
     * @ORM\Column(type="string", length=256)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(name="role", type="string", length=256)
     * @var string
     */
    private $role;

    /**
     * When sending invitation be sure to set this value to `true`
     *
     * It can prevent invitations from being sent twice
     *
     * @ORM\Column(type="boolean", name="is_sent", options={"default"=false})
     */
    private $isSent = false;

    /**
     * This way you can easily know who didn't accepted yet
     * @ORM\Column(type="boolean", name="is_accepted", options={"default"=false})
     */
    private $isAccepted = false;

    /**
     * Inversed side will always fetch the owning side
     * @ORM\OneToOne(targetEntity="Wit\Program\Admin\EditionBundle\Entity\Person", mappedBy="invitation")
     */
    private $person;

    /**
     * Inversed side will always fetch the owning side
     * @ORM\OneToOne(targetEntity="Wit\Program\Admin\EditionBundle\Entity\Mentor", mappedBy="invitation")
     */
    private $mentor;

    public function __construct($code = null)
    {
        // generate identifier only once, here a 50 characters length code
        $this->code = $code ? $code : substr(md5(uniqid(rand(), true)), 0, 50);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getEmail()
    {
        return $this->email;
    }

    // public function setCode($var)
    // {
    //     $this->code = $var;
    // }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function isSent()
    {
        return $this->isSent;
    }

    public function send()
    {
        $this->isSent = true;
    }

    public function setRole($var)
    {
        $this->role = $var;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setIsAccepted($var)
    {
        $this->isAccepted = $var;
    }

    public function getIsAccepted()
    {
        return $this->isAccepted;
    }

    public function setPerson($var)
    {
        $this->person = $var;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function setMentor($var)
    {
        $this->mentor = $var;
    }

    public function getMentor()
    {
        return $this->mentor;
    }
}
