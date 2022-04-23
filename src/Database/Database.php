<?php 

namespace App\Database;


class Database {
    protected $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getPDO();
    }

    public function queryOne(string $query, array $attrs = []) {
        $req = $this->pdo->prepare($query);
        $req->execute($attrs);
        $res = $req->fetch();
        return $res;
    }

    public function queryAll(string $query, array $attrs = []) {
        $req = $this->pdo->prepare($query);
        $req->execute($attrs);
        $res = $req->fetchAll();
        return $res;
    }
}