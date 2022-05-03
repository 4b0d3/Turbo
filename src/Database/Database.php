<?php 

namespace App\Database;


class Database {
    protected $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getPDO();
    }

    public function query(string $query, array $attrs = [])
    {
        $req = $this->pdo->prepare($query);
        $res = $req->execute($attrs);
        return $res;
    }

    public function queryOne(string $query, array $attrs = [])
    {
        $req = $this->pdo->prepare($query);
        $req->execute($attrs);
        $res = $req->fetch(\PDO::FETCH_ASSOC);
        return $res;
    }

    public function queryAll(string $query, array $attrs = []) 
    {
        $req = $this->pdo->prepare($query);
        $req->execute($attrs);
        $res = $req->fetchAll(\PDO::FETCH_ASSOC);
        return $res;
    }
}