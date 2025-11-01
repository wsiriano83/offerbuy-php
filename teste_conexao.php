<?php
echo "<h1>🧪 Teste de Conexão XAMPP</h1>";

// Inclui o arquivo de configuração do banco
include_once 'config/database.php';

// Cria uma instância do Database
$database = new Database();
$db = $database->getConnection();

if($db) {
    echo "✅ <strong>Conexão com MySQL bem-sucedida!</strong><br><br>";
    
    // Teste 1: Verificar tabelas no banco
    try {
        $stmt = $db->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if(count($tables) > 0) {
            echo "📊 <strong>Tabelas encontradas:</strong><br>";
            foreach($tables as $table) {
                echo "&nbsp;&nbsp;• " . $table . "<br>";
            }
        } else {
            echo "ℹ️ Nenhuma tabela encontrada no banco 'offerbuy'<br>";
        }
        
    } catch (PDOException $e) {
        echo "❌ Erro ao listar tabelas: " . $e->getMessage() . "<br>";
    }
    
    // Teste 2: Informações do servidor
    try {
        $version = $db->query("SELECT VERSION() as version")->fetch();
        echo "<br>🔧 <strong>Versão do MySQL:</strong> " . $version['version'] . "<br>";
    } catch (PDOException $e) {
        echo "❌ Erro ao obter versão: " . $e->getMessage() . "<br>";
    }
    
} else {
    echo "❌ <strong>Falha na conexão com o banco de dados!</strong><br>";
    echo "Verifique:<br>";
    echo "1. ✅ MySQL está rodando no XAMPP<br>";
    echo "2. ✅ Banco 'offerbuy' existe<br>";
    echo "3. ✅ Senha do MySQL está correta<br>";
}

echo "<hr>";
echo "<h3>📋 Próximos passos:</h3>";
echo "1. Se viu 'Conexão bem-sucedida', está tudo ok!<br>";
echo "2. Se viu erro, verifique o XAMPP e o banco de dados<br>";
echo "3. Acesse: <a href='http://localhost/offerbuy-php/'>http://localhost/offerbuy-php/</a> para ver o site<br>";
?>