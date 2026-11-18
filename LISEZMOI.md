# Créateur de site professionnel — version 3

## Contenu du paquet
- `index.html` : le configurateur (6 étapes, aperçu en direct, prix affiché avant génération, paiement FedaPay).
- `verifier.php` : confirme chaque paiement auprès de FedaPay avec la clé secrète. À héberger sur **Hostinger** (PHP 8.1+ avec cURL). GitHub Pages ne peut pas l'exécuter.
- `config.example.php` : modèle de configuration (clé secrète, mode sandbox ou live, prix par niveau, adresse du site).

## Ce que fait le client
1. Choisit un type de site (14 types, du site statique au gestionnaire de données).
2. Renseigne son identité : nom, slogan, logo (300 Ko max), langue.
3. Rédige son contenu : titre, accroche, éléments (jusqu'à 12), à propos.
4. Choisit le design : 6 modèles, 8 palettes ou couleurs personnalisées, 5 polices, taille et graisse des titres, arrondis, ombres.
5. Règle la mise en page : en-tête (fixe, classique, centré), titre d'accueil, pied de page, largeur, sections affichées.
6. Renseigne son contact, voit le **prix immédiatement**, clique sur « Générer mon site », puis paie par FedaPay.

## Tarifs par type de site
| Niveau | Prix | Types de site |
|---|---|---|
| Débutant | 7 000 FCFA | Site statique, site vitrine, blog, portfolio, page de vente |
| Intermédiaire | 12 000 FCFA | Site moderne animé, catalogue produits, restaurant / menu, documentation, événement |
| Pro | 15 000 FCFA | Marketplace, réseau social, gestionnaire de données, application SaaS (maquettes d'interface) |

Les tarifs se modifient à deux endroits, qui doivent rester identiques :
- `PRICES` dans `index.html` ;
- `prices` dans `config.php`.

## Mise en place
### 1. FedaPay
1. Créez votre compte FedaPay et travaillez d'abord en **sandbox**.
2. Récupérez la clé **publique** (`pk_…`) pour `index.html`, et la clé **secrète** (`sk_…`) pour `config.php` uniquement.
3. **Autorisez votre domaine** pour Checkout.js dans FedaPay. Sans cela, la fenêtre de paiement ne s'ouvre pas.

### 2. Configuration
Dans `index.html`, bloc `CONFIG` : renseignez `FEDAPAY_PUBLIC_KEY`.
Copiez `config.example.php` en `config.php` et renseignez :
- `fedapay_secret_key`, `fedapay_env` (`sandbox` puis `live`) ;
- `prices` (identiques à `PRICES`) ;
- `allowed_origin` : l'adresse exacte de votre site, en https ;
- `token_secret` : générez-la avec `openssl rand -hex 32`.

### 3. Hostinger
Dans hPanel → Gestionnaire de fichiers → `public_html`, téléversez `index.html`, `verifier.php` et `config.php`. Pour plus de sécurité, placez `config.php` au-dessus de `public_html` et adaptez la ligne `require` de `verifier.php`.

### 4. Tests avant de passer en live
- Un paiement de test doit débloquer le téléchargement.
- Le même paiement, validé une seconde fois, doit être refusé.
- Un montant inférieur au prix du niveau doit être refusé.

## Points à confirmer avant de vendre
- **Mobile Money uniquement** : le widget FedaPay affiche les moyens de paiement activés sur votre compte. Pour n'afficher que le Mobile Money, désactivez les cartes dans votre tableau de bord FedaPay, ou demandez-le au support FedaPay. Le code ne force pas ce réglage.
- **Codes pays** (`bj`, `bf`, `ci`, `gn`, `ml`, `ne`, `sn`, `tg`) : vérifiez leur format exact dans la documentation FedaPay avant la mise en production.
- **Liste des pays** : huit pays sont proposés. Vérifiez que la liste correspond à celle de FedaPay sur votre compte.
- **Déblocage du téléchargement** : il est vérifié par le serveur, puis mémorisé dans le navigateur (30 jours). Le ZIP est généré dans le navigateur. Un utilisateur averti peut contourner cette étape. Pour une protection complète, la génération doit passer par le serveur.
- **Marketplace, réseau social, gestionnaire de données, SaaS** : le configurateur produit des maquettes d'interface, sans backend, sans base de données et sans comptes utilisateurs. Un vrai marché ou réseau social demande une architecture serveur. Présentez ces types comme des maquettes, ou réévaluez leur prix.

## Dépannage
| Message | Cause probable |
|---|---|
| « Le module de paiement ne se charge pas » | Pas de connexion, ou domaine non autorisé chez FedaPay |
| « FedaPay ne répond pas (code 401) » | Clé secrète erronée, ou clé sandbox utilisée en mode live |
| « niveau de site inconnu » | `prices` de `config.php` incomplet |
| « montant insuffisant » | Le prix de `config.php` est plus élevé que celui payé |
| « transaction déjà utilisée » | Le même paiement a déjà été validé |
