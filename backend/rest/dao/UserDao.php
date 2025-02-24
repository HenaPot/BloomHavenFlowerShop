<?php
require_once __DIR__ . "/BaseDao.php";

class UserDao extends BaseDao {
    public function __construct()
    {
        parent::__construct('user');
    }

    public function add_user($user)
    {
        return $this->insert('user', $user);
    }
}