<?php
 require_once 'Database.php';
 
 class AdministratorTable {
     private $db;
     
     public function _construct($db) {
         $this->db = $db;
     }
     
     function get_admin_by_username($username) {
         $query = 'SELECT * FROM administrators
              WHERE username = :username';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $admin = $statement->fetch();
        $statement->closeCursor();
        return $admin;
     }
 }