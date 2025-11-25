<?php
class Cart 
{
    private $conn;
    private $table_name = "carrinho";

    public $id;
    public $usuario_id;
    public $produto_id;
    public $quantidade;
    public $data_adicionado;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Adicionar item ao carrinho 
    public function addToCart()
    {
        try {
            // Verificar se o item já existe no carrinho
            $query = "SELECT id, quantidade FROM " . $this->table_name . " 
                      WHERE usuario_id = ? AND produto_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->usuario_id);
            $stmt->bindParam(2, $this->produto_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Atualizar quantidade
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $new_quantity = $row['quantidade'] + $this->quantidade;
                
                $query = "UPDATE " . $this->table_name . " 
                          SET quantidade = ?, data_adicionado = NOW() 
                          WHERE id = ?";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(1, $new_quantity);
                $stmt->bindParam(2, $row['id']);
            } else {
                // Inserir novo item
                $query = "INSERT INTO " . $this->table_name . " 
                          SET usuario_id=:usuario_id, produto_id=:produto_id, 
                          quantidade=:quantidade, data_adicionado=NOW()";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":usuario_id", $this->usuario_id);
                $stmt->bindParam(":produto_id", $this->produto_id);
                $stmt->bindParam(":quantidade", $this->quantidade);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao adicionar ao carrinho: " . $e->getMessage());
            return false;
        }
    }

    // Buscar itens do carrinho do usuário
    public function getByUser($user_id)
    {
        try {
            $query = "SELECT c.*, p.nome, p.preco, p.imagem_url, p.estoque
                      FROM " . $this->table_name . " c
                      INNER JOIN produtos p ON c.produto_id = p.id
                      WHERE c.usuario_id = ? AND p.ativo = 1
                      ORDER BY c.data_adicionado DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $user_id);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar carrinho: " . $e->getMessage());
            return [];
        }
    }

    // Remover item do carrinho
    public function removeItem($user_id, $product_id)
    {
        try {
            $query = "DELETE FROM " . $this->table_name . " 
                      WHERE usuario_id = ? AND produto_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $user_id);
            $stmt->bindParam(2, $product_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao remover do carrinho: " . $e->getMessage());
            return false;
        }
    }

    // Atualizar quantidade
    public function updateQuantity($user_id, $product_id, $quantity)
    {
        try {
            if ($quantity <= 0) {
                return $this->removeItem($user_id, $product_id);
            }

            $query = "UPDATE " . $this->table_name . " 
                      SET quantidade = ?, data_adicionado = NOW() 
                      WHERE usuario_id = ? AND produto_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $quantity);
            $stmt->bindParam(2, $user_id);
            $stmt->bindParam(3, $product_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar quantidade: " . $e->getMessage());
            return false;
        }
    }

    // Limpar carrinho
    public function clearCart($user_id)
    {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE usuario_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $user_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao limpar carrinho: " . $e->getMessage());
            return false;
        }
    }
}
?>