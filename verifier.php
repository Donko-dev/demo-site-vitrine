<?php
/*
 * Confirme un paiement FedaPay côté serveur. La clé SECRÈTE reste ici, jamais dans index.html.
 * Hébergement : Hostinger (PHP 8.1+ avec cURL). GitHub Pages ne peut pas exécuter ce fichier.
 */
declare(strict_types=1);

$cfg = require __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . $cfg['allowed_origin']);
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok' => false, 'error' => 'méthode non autorisée']); exit; }

function out(int $code, array $data): void { http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit; }

$id = filter_input(INPUT_POST, 'transaction_id', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) out(400, ['ok' => false, 'error' => 'identifiant de transaction invalide']);

/* 1. Relire la transaction directement chez FedaPay (jamais faire confiance au navigateur) */
$base = $cfg['fedapay_env'] === 'live' ? 'https://api.fedapay.com/v1/' : 'https://sandbox-api.fedapay.com/v1/';
$ch = curl_init($base . 'transactions/' . $id);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $cfg['fedapay_secret_key'], 'Accept: application/json'],
]);
$body = curl_exec($ch);
$code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($body === false || $code !== 200) out(502, ['ok' => false, 'error' => 'FedaPay ne répond pas (code ' . $code . ')']);

$data = json_decode($body, true);
if (!is_array($data)) out(502, ['ok' => false, 'error' => 'réponse FedaPay illisible']);
/* Selon la version de l'API, la transaction peut être enveloppée sous une clé. */
$tx = $data['v1/transaction'] ?? $data['transaction'] ?? $data;

/* 2. Contrôles : statut approuvé et montant suffisant */
if (($tx['status'] ?? '') !== 'approved') out(402, ['ok' => false, 'error' => 'paiement non approuvé']);
if ((int) ($tx['amount'] ?? 0) < (int) $cfg['price_xof']) out(402, ['ok' => false, 'error' => 'montant insuffisant']);

/* 3. Un même paiement ne débloque qu'une seule fois */
$usedFile = __DIR__ . '/../data-private/used-transactions.json';
@mkdir(dirname($usedFile), 0700, true);
$used = is_file($usedFile) ? (json_decode((string) file_get_contents($usedFile), true) ?: []) : [];
if (in_array($id, $used, true)) out(409, ['ok' => false, 'error' => 'transaction déjà utilisée']);
$used[] = $id;
file_put_contents($usedFile, json_encode($used), LOCK_EX);

/* 4. Jeton signé, valable 30 jours */
$expires = time() + 30 * 86400;
$payload = $id . '.' . $expires;
$token = $payload . '.' . hash_hmac('sha256', $payload, $cfg['token_secret']);
out(200, ['ok' => true, 'token' => $token, 'expires' => $expires * 1000]);
