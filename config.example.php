<?php
/*
 * COPIEZ ce fichier en config.php et renseignez vos valeurs.
 * Idéalement, placez config.php HORS du dossier public (au-dessus de public_html) et adaptez le chemin dans verifier.php.
 */
return [
    // Clé SECRÈTE FedaPay (sandbox pour tester, live pour encaisser). Ne la partagez jamais.
    'fedapay_secret_key' => 'sk_sandbox_REMPLACEZ_PAR_VOTRE_CLE_SECRETE',
    // 'sandbox' pour les tests, 'live' pour les vrais paiements
    'fedapay_env' => 'sandbox',
    // Prix par niveau, en FCFA (doivent correspondre à PRICES dans index.html)
    'prices' => ['debutant' => 7000, 'intermediaire' => 12000, 'pro' => 15000],
    // Adresse exacte du configurateur, avec https (ex. https://votre-domaine.com)
    'allowed_origin' => 'https://votre-domaine.com',
    // Chaîne aléatoire longue (au moins 32 caractères). Générez-la avec : openssl rand -hex 32
    'token_secret' => 'REMPLACEZ_PAR_UNE_CHAINE_ALEATOIRE_LONGUE',
];
