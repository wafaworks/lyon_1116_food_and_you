<?php
namespace AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ContactCompany
{
    /**
     * @Assert\NotBlank(
     *      message = "app.contact_company.firstName.required"
     * )
     */
    private $firstName;

    /**
     * @Assert\NotBlank(
     *      message = "app.contact_company.lastName.required"
     * )
     */
    private $lastName;

    /**
     * @Assert\NotBlank(
     *      message = "app.contact_company.email.required"
     * )
     *
     * @Assert\Email(
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @Assert\NotBlank(
     *      message = "app.contact_company.phone.required"
     * )
     */
    private $phone;

    /**
     * @Assert\NotBlank(
     *      message = "app.contact_company.capacity.required"
     * )
     */
    private $capacity;

    /**
     * @Assert\NotBlank(
     *      message = "app.contact_company.notice.required"
     * )
     */
    private $notice;

    /**
     * @Assert\NotBlank(
     *      message = "app.contact_company.company.required"
     * )
     */
    private $company;

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param mixed $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     * @return mixed
     */
    public function getNotice()
    {
        return $this->notice;
    }

    /**
     * @param mixed $notice
     */
    public function setNotice($notice)
    {
        $this->notice = $notice;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }
}