<?php
namespace Source\User;

/**
 * @Table(clients)
 */
class Client
{
    /**
     * @Column(id)
     * @Type(integer)
     * @PrimaryKey
     * @Autoincrement
     */
    private $id;

    /**
     * @Column(name)
     * @Type(varchar)
     * @Length(32)
     * @NotNull
     */
    private $name;

    /**
     * @Column(surname)
     * @Type(varchar)
     * @Length(32)
     * @NotNull
     */
    private $surname;

    /**
     * @Column(phone)
     * @Type(varchar)
     * @Length(32)
     * @Default(+48 000-000-000)
     */
    private $phone;

    /**
     * @Column(email)
     * @Type(varchar)
     * @Length(32)
     * @Unique
     */
    private $email;

    public function __construct($name, $surname, $phone, $email)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->phone = $phone;
        $this->email = $email;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }
}
