<?php
require __DIR__ . '/db/conexao.php';

$api_url  = "https://www.dnd5eapi.co/api/spells/";

// pega a lista de magias
$json_data = file_get_contents($api_url);
$spells = json_decode($json_data);
$spellsArray = $spells->results;

// cria um array para guardar as magias completas
$fullSpells = [];

foreach ($spellsArray as $spell) {
    $detailUrl = "https://www.dnd5eapi.co" . $spell->url;
    $detailJson = file_get_contents($detailUrl);
    $detail = json_decode($detailJson);

    // seleciona só as chaves importantes
    $spellData = [
        "name" => $detail->name,
        "level" => $detail->level,
        "casting_time" => $detail->casting_time,
        "range" => $detail->range,
        "components" => implode(", ", $detail->components),
        "material" => $detail->material ?? null,
        "duration" => $detail->duration,
        "concentration" => $detail->concentration ? 1 : 0, // Salva 1 ou 0
        "ritual" => $detail->ritual ? 1 : 0,         // Salva 1 ou 0
        "school" => $detail->school->name,
        "classes" => array_map(fn($c) => $c->name, $detail->classes),
        "desc" => implode("\n", $detail->desc),
        "higher_level" => isset($detail->higher_level) ? implode("\n", $detail->higher_level) : null,
    ];

    // LÓGICA DE INSERÇÃO NO BANCO DE DADOS
    $sql = "INSERT INTO magias_ingles (`name`, `level`, casting_time, `range`, components, material, duration, concentration, ritual, school, classes, `desc`, higher_level) VALUES (:name, :level, :casting_time, :range, :components, :material, :duration, :concentration, :ritual, :school, :classes, :desc, :higher_level)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'name' => $spellData['name'],
        'level' => $spellData['level'],
        'casting_time' => $spellData['casting_time'],
        'range' => $spellData['range'],
        'components' => $spellData['components'],
        'material' => $spellData['material'],
        'duration' => $spellData['duration'],
        'concentration' => $spellData['concentration'],
        'ritual' => $spellData['ritual'],
        'school' => $spellData['school'],
        'classes' => implode(", ", $spellData['classes']),
        'desc' => $spellData['desc'],
        'higher_level' => $spellData['higher_level']
    ]);

    $fullSpells[] = $spellData;
}

// ordena pelo nível
usort($fullSpells, function ($a, $b) {
    return $a['level'] <=> $b['level'];
});

// mostra cada magia
foreach ($fullSpells as $s) {
    echo "=== " . $s["name"] . " (nível " . $s["level"] . ") ===\n";
    echo "Conjuração: " . $s["casting_time"] . "\n";
    echo "Alcance: " . $s["range"] . "\n";
    echo "Componentes: " . $s["components"];
    if ($s["material"]) echo " (" . $s["material"] . ")";
    echo "\n";
    echo "Duração: " . $s["duration"] . "\n";
    echo "Concentração: " . $s["concentration"] . " | Ritual: " . $s["ritual"] . "\n";
    echo "Escola: " . $s["school"] . "\n";
    echo "Classes: " . implode(", ", $s["classes"]) . "\n\n";
    echo $s["desc"] . "\n";
    if ($s["higher_level"]) echo "Em níveis superiores: " . $s["higher_level"] . "\n";
    echo "---------------------------------------------\n\n";
}

echo "Total de magias: " . count($fullSpells);
