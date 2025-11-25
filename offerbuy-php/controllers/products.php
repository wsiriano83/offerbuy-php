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
            $this->id = $row['id'];
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

    // Criar produto
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET nome=:nome, descricao=:descricao, preco=:preco, preco_original=:preco_original, 
                  imagem_url=:imagem_url, frete_gratis=:frete_gratis, garantia_meses=:garantia_meses, 
                  estoque=:estoque, categoria_id=:categoria_id, ativo=1";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpar dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->preco = htmlspecialchars(strip_tags($this->preco));
        $this->preco_original = htmlspecialchars(strip_tags($this->preco_original));
        $this->imagem_url = htmlspecialchars(strip_tags($this->imagem_url));
        $this->frete_gratis = htmlspecialchars(strip_tags($this->frete_gratis));
        $this->garantia_meses = htmlspecialchars(strip_tags($this->garantia_meses));
        $this->estoque = htmlspecialchars(strip_tags($this->estoque));
        $this->categoria_id = htmlspecialchars(strip_tags($this->categoria_id));
        
        // Vincular parâmetros
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":preco", $this->preco);
        $stmt->bindParam(":preco_original", $this->preco_original);
        $stmt->bindParam(":imagem_url", $this->imagem_url);
        $stmt->bindParam(":frete_gratis", $this->frete_gratis);
        $stmt->bindParam(":garantia_meses", $this->garantia_meses);
        $stmt->bindParam(":estoque", $this->estoque);
        $stmt->bindParam(":categoria_id", $this->categoria_id);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
    
    // Atualizar produto
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nome=:nome, descricao=:descricao, preco=:preco, preco_original=:preco_original, 
                  imagem_url=:imagem_url, frete_gratis=:frete_gratis, garantia_meses=:garantia_meses, 
                  estoque=:estoque, categoria_id=:categoria_id 
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpar dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->preco = htmlspecialchars(strip_tags($this->preco));
        $this->preco_original = htmlspecialchars(strip_tags($this->preco_original));
        $this->imagem_url = htmlspecialchars(strip_tags($this->imagem_url));
        $this->frete_gratis = htmlspecialchars(strip_tags($this->frete_gratis));
        $this->garantia_meses = htmlspecialchars(strip_tags($this->garantia_meses));
        $this->estoque = htmlspecialchars(strip_tags($this->estoque));
        $this->categoria_id = htmlspecialchars(strip_tags($this->categoria_id));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // Vincular parâmetros
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":preco", $this->preco);
        $stmt->bindParam(":preco_original", $this->preco_original);
        $stmt->bindParam(":imagem_url", $this->imagem_url);
        $stmt->bindParam(":frete_gratis", $this->frete_gratis);
        $stmt->bindParam(":garantia_meses", $this->garantia_meses);
        $stmt->bindParam(":estoque", $this->estoque);
        $stmt->bindParam(":categoria_id", $this->categoria_id);
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Deletar produto (soft delete)
    public function delete() {
        $query = "UPDATE " . $this->table_name . " SET ativo = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>