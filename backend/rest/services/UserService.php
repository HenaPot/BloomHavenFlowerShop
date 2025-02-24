<?php

require_once "C:/xampp/htdocs/WebProjekat/backend/rest/dao/UserDao.php";

class UserService{
    private $userDao;

    public function __construct()
    {
        $this->userDao = new UserDao();
    }

    public function add_user($user)
    {
        return $this->userDao->add_user($user);
    }
}