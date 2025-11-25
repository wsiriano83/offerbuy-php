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

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function register()
    {
        try 
        {
            // Validações básicas
            if (empty($this->nome) || empty($this->email) || empty($this->senha))
            {
                return false;
            }

            // Verificar se email já existe
            if ($this->emailExists()) {
                return false;
            }

            $query = "INSERT INTO " . $this->table_name . "
                  SET nome=:nome, email=:email, senha=:senha, telefone=:telefone, data_cadastro=NOW()";

            $stmt = $this->conn->prepare($query);
                
            $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

            $stmt->bindParam(":nome", $this->nome);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":senha", $this->senha);
            $stmt->bindParam(":telefone", $this->telefone);

            if ($stmt->execute()) 
            {   
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro no register: " . $e->getMessage());
            return false;
        }
    }

    public function login()
    {
        try 
        {
            $query = "SELECT id, nome, email, senha 
            FROM " . $this->table_name . " 
            WHERE email = :email 
            LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $this->email);
            $stmt->execute();

            if($stmt->rowCount() == 1)
            {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($this->senha, $row['senha'])) 
                {
                    $this->id = $row['id'];
                    $this->nome = $row['nome'];
                    $this->email = $row['email'];
                    return true;
                }
            } 
            return false;
        } catch (PDOException $e) {
            error_log("Erro no login: " . $e->getMessage());
            return false;   
        }
    }

    public function emailExists()
    {   
        try 
        {
            $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $this->email);
            $stmt->execute();
            return $stmt->rowCount() > 0; 

        } catch (PDOException $e) {
            error_log("Erro no emailExists: " . $e->getMessage());
            return false;
        }      
    }

    // Buscar usuário por Id - VERSÃO CORRIGIDA
    public function readOne()
    {
        try 
        {
            // Query usando ? em vez de :id
            $query = "SELECT nome, email, telefone, data_cadastro FROM usuarios WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row) {
                    $this->nome = $row['nome'];
                    $this->email = $row['email'];
                    $this->telefone = $row['telefone'];
                    $this->data_cadastro = $row['data_cadastro'];
                    return true;
                }
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro no readOne: " . $e->getMessage());
            return false;
        }
    }

    // MÉTODO UPDATE QUE ESTAVA FALTANDO - ADICIONE ESTE
    public function update()
    {
        try 
        {
            $query = "UPDATE " . $this->table_name . "
                      SET nome = :nome, telefone = :telefone
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Limpar dados
            $this->nome = htmlspecialchars(strip_tags($this->nome));
            $this->telefone = htmlspecialchars(strip_tags($this->telefone));
            
            $stmt->bindParam(":nome", $this->nome);
            $stmt->bindParam(":telefone", $this->telefone);
            $stmt->bindParam(":id", $this->id);
            
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro no update: " . $e->getMessage());
            return false;
        }
    }

    // MÉTODO ALTERNATIVO - ADICIONE ESTE TAMBÉM
    public function readOneAlternativo()
    {
        try 
        {
            $query = "SELECT nome, email, telefone, data_cadastro FROM usuarios WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row) {
                    $this->nome = $row['nome'];
                    $this->email = $row['email'];
                    $this->telefone = $row['telefone'];
                    $this->data_cadastro = $row['data_cadastro'];
                    return true;
                }
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro no readOneAlternativo: " . $e->getMessage());
            return false;
        }
    }

    // MÉTODO DE FALLBACK - JÁ EXISTIA, MANTENHA
    public function getDadosDiretamente($user_id)
    {
        try {
            $query = "SELECT nome, email, telefone, data_cadastro FROM usuarios WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $this->nome = $row['nome'];
                $this->email = $row['email'];
                $this->telefone = $row['telefone'];
                $this->data_cadastro = $row['data_cadastro'];
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erro no getDadosDiretamente: " . $e->getMessage());
            return false;
        }
    }
}
?>