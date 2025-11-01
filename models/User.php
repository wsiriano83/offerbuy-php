<?php 
class User
{
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $nome;
    public $email;
    public $senha;
    public $telefone;
    public $data_cadastro;

    public function register()
    {
        $query = "INSERT INTO " . $this->table_name . "
                  SET nome=:nome, email=:email, senha=:senha, telefone=:telefone";

        $stmt = $this->conn->prepare($query);

        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":senha", $this->senha);
        $stmt->bindParam(":telefone", $this->telefone);

        if ($stmt->execute()) 
        {
            return true;
        }
        return false;
    }

    public function login()
    {
        $query = "SELECT id, nome, email, senha FROM " . $this->table_name . " WHERE email =  :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if($stmt->rowCount() ==1)
        {
            $row = $stmt->fetch(PDO::FETCH_CLASS);

            if (password_verify($this->senha, $row['senha'])) 
            {
                $this->id = $row['id'];
                $this->nome = $row['nome'];
                return true;
            }
        }
        return false;
    }

    public function emailExists()
    {
        $query = "SELECT id FROM" . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>