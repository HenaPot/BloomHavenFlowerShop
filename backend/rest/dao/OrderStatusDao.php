<?php
require_once __DIR__ . "/BaseDao.php";

class OrderStatusDao extends BaseDao {
    public function __construct()
    {
        parent::__construct('status');
    }

    public function get_order_statuses() {
        $query = "SELECT * FROM `status`";
        return $this->query($query, []);
    }   
}
