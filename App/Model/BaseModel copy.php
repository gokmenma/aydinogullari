
<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/configs/config.php';


class BaseModel extends PDO
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $uploadDir = 'files/offer/';

    public function __construct($table)
    {
        global $ac;
        $this->db = $ac;
        $this->table = $table;
    }

    public function getAll()
    {
        $sql =  $this->db->prepare("SELECT * FROM $this->table");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_OBJ);
    }

    public function find($id)
    {
        $sql = $this->db->prepare("SELECT * FROM $this->table WHERE $this->primaryKey = ?");
        $sql->execute(array($id));
        return $sql->fetch(PDO::FETCH_OBJ);
    }

    public function delete($id)
    {
        $sql =  $this->db->prepare("DELETE FROM $this->table where id = ?");
        $sql->execute([$id]);
        return $sql->rowCount();
    }

    public function save($data)
    {
        if (isset($data['id']) && $data['id'] > 0) {
            return $this->update($data);
        } else {
            return $this->insert($data);
        }
    }

    public function insert($data)
    {
        $keys = array_keys($data);
        $values = array_values($data);
        $sql = $this->db->prepare("INSERT INTO $this->table (" . implode(',', $keys) . ") VALUES (" . str_repeat('?,', count($values) - 1) . "?)");
        $sql->execute($values);
        return $this->db->lastInsertId();
    }

    public function update($data)
    {
        $keys = array_keys($data);
        $values = array_values($data);
        $sql = $this->db->prepare("UPDATE $this->table SET " . implode('=?,', $keys) . "=? WHERE id = ?");
        $values[] = $data['id']; // id'yi WHERE koşuluna eklemek için values dizisine ekleyin
        $sql->execute($values);
        
    }

    public function uploadFile($file)
    {
        $uploadPath = $this->uploadDir . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $uploadPath;
        } else {
            return false;
        }
    }


    public function findFileName($id)
    {
        $sql =  $this->db->prepare("SELECT file FROM $this->table where id = ?");
        $sql->execute([$id]);
        return $sql->fetch(PDO::FETCH_OBJ);
    }
}
