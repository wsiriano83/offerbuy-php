<?php include_once 'views/header.php'?>

<div class="h-search">
    <form action="busca.php" method="GET">
        <input type="search" name="q" placeholder="Pesquisar por..." class="search-input" />
        <input id="search-btn" type="submit" value="Procurar" />
    </form>
</div>

<section class="home">
    <div class="home-txt" data-aos="zoom-in-up">
        <h1>OfferBuy</h1>
        <p>De tudo em um.</p>
        <h2>70% de Desconto!!!</h2>
    </div>

    <div class="home-img" data-aos="zoom-in-up">
        <img src="img/camisa.png" alt="Produto em destaque">
    </div>
</section>

<section class="property" data-aos="zoom-in-up">
    <div class="center-left">
        <h2>Produtos Populares</h2>
    </div>
    <div class="property-content">
        <?php 
        $stmt = $pdo->query("SELECT * FROM produtos WHERE ativo = TRUE LIMIT 3");
        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($produtos as $produto)
        {
            echo '
            <div class="row" data-produto-id="' . $produto['id'] . '">
            <img src="' . $produto['imagem_url'] . '">
            <h5>R$' . number_format($produto['preco'], 2, ',', '.') .'</h5>
            <p>' . $produto['nome'] . '</p>
            </div>';
            
        }
        ?>
    </div>
</section>

<?php include 'includes/footer.php';?>

<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init({
        offset: 300,
        duration: 1200
    });
</script>
<script src="js/index.js"></script>