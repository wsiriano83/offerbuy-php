<?php
class Order
{
    private $conn;
    private $table_name = "pedidos";

    public $id;
    public $usuario_id;
    public $numero_pedido;
    public $status;
    public $subtotal;
    public $frete;
    public $endereco_entrega_id;
    public $metodo_pagamento;
    public $data_pedido;
    public $usuario_nome;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Criar novo pedido - CORRIGIDO para sua estrutura
    public function create()
    {
        try {
            // Validar dados obrigatórios
            if (empty($this->usuario_id) || empty($this->metodo_pagamento)) {
                throw new Exception("Dados obrigatórios não fornecidos: usuario_id e metodo_pagamento são obrigatórios");
            }

            // Gerar número do pedido único
            $this->numero_pedido = 'OB' . date('YmdHis') . mt_rand(1000, 9999);
            
            // Definir status padrão se não fornecido
            if (empty($this->status)) {
                $this->status = 'confirmado';
            }

            // Definir valores padrão se não fornecidos
            if (empty($this->subtotal)) $this->subtotal = 0;
            if (empty($this->frete)) $this->frete = 0;

            $query = "INSERT INTO " . $this->table_name . " 
                      SET usuario_id=:usuario_id, 
                          numero_pedido=:numero_pedido,
                          subtotal=:subtotal, 
                          frete=:frete,
                          status=:status,
                          metodo_pagamento=:metodo_pagamento";
            
            // Adicionar endereço de entrega se disponível
            if (!empty($this->endereco_entrega_id)) {
                $query .= ", endereco_entrega_id=:endereco_entrega_id";
            }
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":usuario_id", $this->usuario_id);
            $stmt->bindParam(":numero_pedido", $this->numero_pedido);
            $stmt->bindParam(":subtotal", $this->subtotal);
            $stmt->bindParam(":frete", $this->frete);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":metodo_pagamento", $this->metodo_pagamento);
            
            if (!empty($this->endereco_entrega_id)) {
                $stmt->bindParam(":endereco_entrega_id", $this->endereco_entrega_id);
            }
            
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                error_log("SUCESSO: Pedido #{$this->id} criado para usuário {$this->usuario_id}");
                return true;
            }
            
            $error = $stmt->errorInfo();
            throw new Exception("Erro ao executar query: " . $error[2]);
            
        } catch (PDOException $e) {
            error_log("Erro PDO ao criar pedido: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Erro ao criar pedido: " . $e->getMessage());
            return false;
        }
    }

    // Buscar pedidos do usuário - CORRIGIDO (calcula total)
    public function getByUser($user_id)
    {
        try {
            $query = "SELECT p.*, u.nome as usuario_nome
                      FROM " . $this->table_name . " p
                      LEFT JOIN usuarios u ON p.usuario_id = u.id
                      WHERE p.usuario_id = ?
                      ORDER BY p.data_pedido DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $user_id);
            $stmt->execute();
            
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Adicionar total calculado para cada pedido
            foreach ($pedidos as &$pedido) {
                $pedido['total'] = $pedido['subtotal'] + $pedido['frete'];
            }
            
            return $pedidos;
        } catch (PDOException $e) {
            error_log("Erro ao buscar pedidos do usuário {$user_id}: " . $e->getMessage());
            return [];
        }
    }

    // Buscar um pedido específico - CORRIGIDO
    public function readOne()
    {
        try {
            $query = "SELECT p.*, u.nome as usuario_nome 
                      FROM " . $this->table_name . " p
                      LEFT JOIN usuarios u ON p.usuario_id = u.id 
                      WHERE p.id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $this->id);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $this->usuario_id = $row['usuario_id'];
                $this->numero_pedido = $row['numero_pedido'];
                $this->data_pedido = $row['data_pedido'];
                $this->subtotal = $row['subtotal'];
                $this->frete = $row['frete'];
                $this->status = $row['status'];
                $this->metodo_pagamento = $row['metodo_pagamento'];
                $this->endereco_entrega_id = $row['endereco_entrega_id'];
                $this->usuario_nome = $row['usuario_nome'];
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Erro ao buscar pedido #{$this->id}: " . $e->getMessage());
            return false;
        }
    }

    // Buscar todos os pedidos (para admin) - CORRIGIDO
    public function readAll()
    {
        try {
            $query = "SELECT p.*, u.nome as usuario_nome 
                      FROM " . $this->table_name . " p
                      LEFT JOIN usuarios u ON p.usuario_id = u.id 
                      ORDER BY p.data_pedido DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Adicionar total calculado
            foreach ($pedidos as &$pedido) {
                $pedido['total'] = $pedido['subtotal'] + $pedido['frete'];
            }
            
            return $pedidos;
        } catch (PDOException $e) {
            error_log("Erro ao buscar todos os pedidos: " . $e->getMessage());
            return [];
        }
    }

    // Buscar itens de um pedido específico
    public function getItens($pedido_id)
    {
        try {
            $query = "SELECT pi.*, p.nome as produto_nome, p.imagem_url, p.descricao
                      FROM pedido_itens pi 
                      LEFT JOIN produtos p ON pi.produto_id = p.id 
                      WHERE pi.pedido_id = ?
                      ORDER BY pi.id ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $pedido_id);
            $stmt->execute();
            
            $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log para debug
            if (empty($itens)) {
                error_log("AVISO: Pedido #{$pedido_id} não possui itens");
            } else {
                error_log("SUCESSO: Encontrados " . count($itens) . " itens para pedido #{$pedido_id}");
            }
            
            return $itens;
        } catch (PDOException $e) {
            error_log("Erro ao buscar itens do pedido #{$pedido_id}: " . $e->getMessage());
            return [];
        }
    }

    // Buscar pedidos do usuário com itens
    public function getByUserWithItens($user_id)
    {
        try {
            // Primeiro busca os pedidos
            $pedidos = $this->getByUser($user_id);
            
            // Para cada pedido, busca os itens
            foreach ($pedidos as &$pedido) {
                $pedido['itens'] = $this->getItens($pedido['id']);
            }
            
            error_log("SUCESSO: Retornados " . count($pedidos) . " pedidos com itens para usuário {$user_id}");
            return $pedidos;
        } catch (PDOException $e) {
            error_log("Erro ao buscar pedidos com itens para usuário {$user_id}: " . $e->getMessage());
            return [];
        }
    }

    // Adicionar item ao pedido
    public function addItem($pedido_id, $produto_id, $variacao_id, $quantidade, $preco_unitario)
    {
        try {
            // Validar dados obrigatórios
            if (empty($pedido_id) || empty($produto_id) || empty($quantidade) || empty($preco_unitario)) {
                throw new Exception("Dados obrigatórios não fornecidos para addItem");
            }

            $query = "INSERT INTO pedido_itens 
                      SET pedido_id=:pedido_id, 
                          produto_id=:produto_id,
                          variacao_id=:variacao_id,
                          quantidade=:quantidade, 
                          preco_unitario=:preco_unitario";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":pedido_id", $pedido_id);
            $stmt->bindParam(":produto_id", $produto_id);
            $stmt->bindParam(":variacao_id", $variacao_id);
            $stmt->bindParam(":quantidade", $quantidade);
            $stmt->bindParam(":preco_unitario", $preco_unitario);
            
            if ($stmt->execute()) {
                error_log("SUCESSO: Item adicionado ao pedido #{$pedido_id} - Produto: {$produto_id}, Qtd: {$quantidade}");
                return true;
            } else {
                $error = $stmt->errorInfo();
                error_log("Erro ao adicionar item ao pedido #{$pedido_id}: " . $error[2]);
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erro PDO ao adicionar item ao pedido #{$pedido_id}: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Erro ao adicionar item ao pedido #{$pedido_id}: " . $e->getMessage());
            return false;
        }
    }

    // Método para criar pedido completo com itens (TRANSACTION SAFE) - CORRIGIDO
    public function createCompleteOrder($usuario_id, $metodo_pagamento, $subtotal, $frete, $itens, $endereco_entrega_id = null)
    {
        try {
            // Validar dados
            if (empty($usuario_id) || empty($metodo_pagamento) || empty($itens)) {
                throw new Exception("Dados incompletos para criar pedido completo");
            }

            // Iniciar transação
            $this->conn->beginTransaction();

            // Criar pedido
            $this->usuario_id = $usuario_id;
            $this->metodo_pagamento = $metodo_pagamento;
            $this->subtotal = $subtotal;
            $this->frete = $frete;
            $this->status = 'confirmado';
            $this->endereco_entrega_id = $endereco_entrega_id;

            if (!$this->create()) {
                throw new Exception("Falha ao criar pedido");
            }

            $pedido_id = $this->id;
            $itens_salvos = 0;

            // Adicionar itens
            foreach ($itens as $item) {
                if (!$this->addItem(
                    $pedido_id,                      // pedido_id
                    $item['produto_id'],            // produto_id
                    $item['variacao_id'] ?? null,   // variacao_id
                    $item['quantidade'],            // quantidade
                    $item['preco']                  // preco_unitario
                )) {
                    throw new Exception("Falha ao adicionar item {$item['produto_id']} ao pedido");
                }
                $itens_salvos++;
            }

            // Verificar se pelo menos um item foi salvo
            if ($itens_salvos === 0) {
                throw new Exception("Nenhum item foi salvo no pedido");
            }

            // Commit da transação
            $this->conn->commit();
            
            error_log("SUCESSO: Pedido completo #{$pedido_id} criado com {$itens_salvos} itens");
            return true;

        } catch (Exception $e) {
            // Rollback em caso de erro
            $this->conn->rollBack();
            error_log("ERRO ao criar pedido completo: " . $e->getMessage());
            return false;
        }
    }
}
?>