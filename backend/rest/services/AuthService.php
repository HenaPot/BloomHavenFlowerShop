
<?php

require_once "C:/xampp/htdocs/WebProjekat/backend/rest/dao/AuthDao.php";

class AuthService {
    private $auth_dao;
    public function __construct() {
        $this->auth_dao = new AuthDao();
    }

    public function get_user_by_email($email){
        return $this->auth_dao->get_user_by_email($email);
    }
}
