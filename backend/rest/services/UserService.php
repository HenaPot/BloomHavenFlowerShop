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

    public function get_user_by_id($user_id) {
        return $this->userDao->get_user_by_id($user_id);
    }

    public function update_user($user_id, $user) {
        return $this->userDao->update_user($user_id, $user);
    }

    public function delete_user($user_id) {
        $this->userDao->delete_user( $user_id);
    }
}