<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magias</title>

</head>

<body>

    <div class="mb-3">
        <input type="text" id="filtro-nome" class="form-control" placeholder="Filtrar por nome...">
    </div>

    <section class="container mt-4">
        <?php
        // Inclui a conexão com o banco de dados.
        // A variável $pdo deve estar disponível aqui.
        require_once '../src/db/conexao.php';

        try {
            // Consulta o banco de dados para pegar todas as magias
            // A ordenação por nível já é feita no SQL
            $sqlAllSpells = "SELECT * FROM magias_ingles ORDER BY level ASC";
            $stmt = $pdo->prepare($sqlAllSpells);
            $stmt->execute();

            // Pega todos os resultados em um array associativo
            $fullSpells = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Trata a falha na conexão ou na consulta
            echo "<div class='alert alert-danger'>Erro ao carregar as magias do banco de dados: " . $e->getMessage() . "</div>";
            $fullSpells = []; // Garante que a variável ainda seja um array
        }

        ?>

        <h2 class="mb-4">Total de Magias: <?php echo count($fullSpells); ?></h2>

        <div class="row">
            <?php
            // agora o foreach usa o array $fullSpells já preenchido
            foreach ($fullSpells as $s) {
                echo "<div class='col-lg-4 col-md-4 col-sm-6 mb-4 spell-card' data-name='" . strtolower($s["name"]) . "'>";
                echo "<div class='card h-100'>";
                echo "<div class='card-body d-flex flex-column'>";

                // Título e Subtítulo
                echo "<h5 class='card-title text-center'><strong>" . $s["name"] . "</strong></h5>";
                 echo "<h6 class='card-subtitle mb-2 text-muted text-center'>Nível " . $s["level"] . " - " . $s["school"] . "</h6>";

                // Detalhes na ordem solicitada
                echo "<p class='card-text'><strong>Conjuração:</strong> " . $s["casting_time"] . "</p>";
                echo "<p class='card-text'><strong>Alcance:</strong> " . $s["range"] . "</p>";
                echo "<p class='card-text'><strong>Componentes:</strong> " . $s["components"];
                if ($s["material"]) echo " (" . $s["material"] . ")";
                echo "</p>";
                echo "<p class='card-text'><strong>Duração:</strong> " . $s["duration"] . "</p>";

                // Detalhes adicionais

                echo "<p class='card-text'><strong>Concentração:</strong> " . ($s["concentration"] ? "Sim" : "Não") . " | <strong>Ritual:</strong> " . ($s["ritual"] ? "Sim" : "Não") . "</p>";
                echo "<p class='card-text'><strong>Classes:</strong> " . $s["classes"] . "</p>";

                // Descrição e Em Níveis Superiores
                echo "<p class='card-text mt-3'><strong>Descrição:</strong><br>" . str_replace("\n", "<br>", $s["desc"]) . "</p>";

                if ($s["higher_level"]) {
                    echo "<p class='card-text'><strong>Em níveis superiores:</strong><br>" . str_replace("\n", "<br>", $s["higher_level"]) . "</p>";
                }

                echo "</div>"; // card-body
                echo "</div>"; // card
                echo "</div>"; // col
            }
            ?>
        </div>
    </section>

    <script>
        const filtroInput = document.getElementById('filtro-nome');
        const spellCards = document.querySelectorAll('.spell-card');

        filtroInput.addEventListener('keyup', function(event) {
            const searchTerm = event.target.value.toLowerCase();

            spellCards.forEach(function(card) {
                const spellName = card.getAttribute('data-name');
                if (spellName.includes(searchTerm)) {
                    card.style.display = ''; // Mostra o card
                } else {
                    card.style.display = 'none'; // Esconde o card
                }
            });
        });
    </script>
</body>

</html>