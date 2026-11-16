# Créateur de site vitrine — version monétisée (FedaPay)

Ce paquet contient :
- `index.html` : le configurateur (formulaire en 4 étapes, aperçu en direct, 3 modèles, téléchargement ZIP).
- `verifier.php` : vérifie chaque paiement FedaPay côté serveur. **Doit être hébergé sur Hostinger** (PHP). GitHub Pages ne peut pas l'exécuter.
- `config.example.php` : modèle de configuration (clés et prix).

## Modèle économique
- **Gratuit** : modèle Minimal, avec la mention « Site créé avec EMPIRE CODE HUB ».
- **Premium** (5 000 FCFA par défaut, une fois par site) : 3 modèles, sans mention, plan du site et robots.txt.

Le prix et les textes se modifient dans le bloc `CONFIG` de `index.html` et dans `config.php`.

## 1. Créer votre compte FedaPay
1. Inscrivez-vous sur FedaPay et ouvrez d'abord le **mode sandbox** (test).
2. Dans le tableau de bord, récupérez :
   - la **clé publique** (commence par `pk_`) : elle va dans `index.html` ;
   - la **clé secrète** (commence par `sk_`) : elle va **uniquement** dans `config.php`. Ne la mettez jamais dans `index.html`.
3. **Autorisez votre domaine** pour Checkout.js (section « Domain Authorization » de la documentation FedaPay). Sans cela, le paiement ne s'ouvre pas sur votre site.

## 2. Configurer les fichiers
Dans `index.html`, bloc `CONFIG` :
```js
FEDAPAY_PUBLIC_KEY: 'pk_sandbox_VOTRE_CLE_PUBLIQUE',
FEDAPAY_ENV: 'sandbox',
PRICE_XOF: 5000,
VERIFY_URL: 'verifier.php',
```
Copiez `config.example.php` en `config.php` et renseignez :
- `fedapay_secret_key` : votre clé secrète ;
- `fedapay_env` : `sandbox` pour tester, `live` pour encaisser ;
- `price_xof` : le même prix que dans `index.html` ;
- `allowed_origin` : l'adresse exacte de votre site, avec https ;
- `token_secret` : une chaîne longue et aléatoire (générez-la avec `openssl rand -hex 32`).

## 3. Mettre en ligne sur Hostinger
1. hPanel → Gestionnaire de fichiers → `public_html`.
2. Téléversez `index.html`, `verifier.php` et `config.php` (pas `config.example.php`).
3. Votre configurateur est à `https://votre-domaine.com/` ; la vérification à `https://votre-domaine.com/verifier.php`.
4. Pour plus de sécurité, déplacez `config.php` au-dessus de `public_html` et adaptez la ligne `require` de `verifier.php`.

## 4. Tester avant d'encaisser
1. Gardez `FEDAPAY_ENV: 'sandbox'` et la clé `pk_sandbox_` / `sk_sandbox_`.
2. Ouvrez le configurateur, créez un site, cliquez sur « Débloquer ».
3. Effectuez le paiement de test indiqué par FedaPay.
4. Vérifiez que le Premium s'active et que le ZIP ne contient plus la mention.
5. Une même transaction ne peut débloquer le Premium qu'une seule fois (le serveur la refuse la seconde fois).

## 5. Passer en réel
- Remplacez les clés sandbox par les clés **live**, `fedapay_env` par `live`.
- Vérifiez le domaine autorisé et le montant.

## Dépannage
| Message | Cause probable |
|---|---|
| « Le module de paiement ne se charge pas » | Pas de connexion, ou domaine non autorisé chez FedaPay |
| « FedaPay ne répond pas (code 401) » | Mauvaise clé secrète, ou clé sandbox utilisée en mode live |
| « transaction déjà utilisée » | Le même paiement a déjà été validé |
| « Paiement reçu mais non confirmé » | Vérifiez `allowed_origin`, l'adresse de `verifier.php` et le journal d'erreurs Hostinger |

## Limites à connaître
- Le déblocage Premium est enregistré dans le navigateur de l'acheteur (30 jours). Un utilisateur averti peut le contourner. Pour une protection forte, le ZIP Premium doit être généré par le serveur : c'est une évolution possible.
- Le paiement suit les règles et les frais de FedaPay.
- Vérifiez les conditions de vente et la politique de remboursement avant d'ouvrir les paiements au public.
