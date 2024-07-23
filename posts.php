<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $selection = $_POST['selection'];
    $age_moi = isset($_POST['age']) ? $_POST['age'] : null;
    $regime_moi = isset($_POST['regime_moi']) ? $_POST['regime_moi'] : null;
    $age_conjoint = isset($_POST['conjoint_age']) ? $_POST['conjoint_age'] : null;
    $regime_conjoint = isset($_POST['regime_conjoint']) ? $_POST['regime_conjoint'] : null;
    $nombre_enfants = isset($_POST['nombre_enfants']) ? $_POST['nombre_enfants'] : 0;
    $enfants = [];

    if ($selection == "moi_enfants" || $selection == "enfant") {
        for ($i = 1; $i <= $nombre_enfants; $i++) {
            if (isset($_POST['age_enfant_' . $i])) {
                $enfants[] = $_POST['age_enfant_' . $i];
            }
        }
    }

    $adresse = $_POST['adresse'];
    $code_postal = $_POST['code_postal'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];

    // Préparer et exécuter la requête d'insertion pour les informations principales
    $stmt = $conn->prepare("INSERT INTO users (selection, age_moi, regime_moi, age_conjoint, regime_conjoint, nombre_enfants, adresse, code_postal, email, telephone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssissss", $selection, $age_moi, $regime_moi, $age_conjoint, $regime_conjoint, $nombre_enfants, $adresse, $code_postal, $email, $telephone);

    if ($stmt->execute()) {
        $last_id = $stmt->insert_id;
        // Insertion des âges des enfants, s'il y en a
        if (!empty($enfants)) {
            $stmt_enfant = $conn->prepare("INSERT INTO enfants (user_id, age) VALUES (?, ?)");
            foreach ($enfants as $age_enfant) {
                $stmt_enfant->bind_param("ii", $last_id, $age_enfant);
                $stmt_enfant->execute();
            }
            $stmt_enfant->close();
        }
        echo "Données enregistrées avec succès";
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

