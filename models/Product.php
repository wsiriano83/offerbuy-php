<?php
class Product
{
    private $conn;
    private $table_name = "produtos";

    public $id;
    public $nome;
    public $descricao;
    public $preco;
    public $preco_original;
    public $categoria_id;
    public $imagem_url;
    public $estoque;
    public $ativo;
    public $frete_gratis;
    public $garantia_meses;
    public $data_cadastro;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Ler todos os produtos
    public function read()
    {
        $query = "SELECT p.*, c.nome as categoria_nome
                    FROM " . $this->table_name . " p 
                    LEFT JOIN categorias c ON p.categoria_id = c.id
                    WHERE p.ativo = 1
                    ORDER BY p.data_cadastro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt; 
    }

    // Ler um produto por ID
    public function readOne()
    {
        $query = "SELECT p.*, c.nome as categoria_nome
                    FROM " . $this->table_name . " p
                    LEFT JOIN categorias c ON p.categoria_id = c.id
                    WHERE p.id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row)
        {
            $this->nome = $row['nome'];
            $this->descricao = $row['descricao'];
            $this->preco = $row['preco'];
            $this->preco_original = $row['preco_original'];
            $this->categoria_id = $row['categoria_id'];
            $this->imagem_url = $row['imagem_url'];
            $this->estoque = $row['estoque'];
            $this->frete_gratis = $row['frete_gratis'];
            $this->garantia_meses = $row['garantia_meses'];
            $this->data_cadastro = $row['data_cadastro'];
            return true;
        }
        return false;
    }

    // Ler produtos por categoria
    public function readByCategoria($categoria_id)
    {
        $query = "SELECT p.*, c.nome as categoria_nome
                  FROM " . $this->table_name . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  WHERE p.ativo = 1 AND p.categoria_id = ?
                  ORDER BY p.data_cadastro DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $categoria_id);
        $stmt->execute();
        return $stmt;
    }

    // Buscar Produtos - CORRIGIDO
    public function search($keywords)
    {
        $query = "SELECT p.*, c.nome as categoria_nome
                  FROM " . $this->table_name . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  WHERE p.ativo = 1 AND (p.nome LIKE ? OR p.descricao LIKE ? OR c.nome LIKE ?)
                  ORDER BY p.data_cadastro DESC";
        
        $stmt = $this->conn->prepare($query);

        $searchKeywords = "%{$keywords}%";
        $stmt->bindParam(1, $searchKeywords);
        $stmt->bindParam(2, $searchKeywords);
        $stmt->bindParam(3, $searchKeywords);

        $stmt->execute();
        return $stmt;
    }

    // Obter variações do produto
    public function getVariations() 
    {
        $query = "SELECT * FROM variacoes_produto WHERE produto_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt;
    }

    // Obter produtos em destaque - CORRIGIDO
    public function readFeatured()
    {
        $query = "SELECT p.*, c.nome as categoria_nome
                  FROM " . $this->table_name . " p
                  LEFT JOIN categorias c ON p.categoria_id = c.id
                  WHERE p.ativo = 1
                  ORDER BY p.data_cadastro DESC
                  LIMIT 8";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;          
    }

    // Verificar estoque
    public function checkStock($product_id, $quantity = 1)
    {
        $query = "SELECT estoque FROM " . $this->table_name . " WHERE id = ? AND ativo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $product_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            return $row['estoque'] >= $quantity;
        }
        
        return false;
    }

    // Atualizar estoque
    public function updateStock($product_id, $new_stock)
    {
        $query = "UPDATE " . $this->table_name . " SET estoque = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $new_stock);
        $stmt->bindParam(2, $product_id);
        
        return $stmt->execute();
    }
}
?>