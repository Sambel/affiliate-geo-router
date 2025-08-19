# Affiliate Geo Router - Hub de Redirection Affiliée Géolocalisée

## 📋 Description

Affiliate Geo Router est une application Laravel 12 conçue pour gérer intelligemment les redirections d'affiliation basées sur la géolocalisation. Le système détecte automatiquement le pays du visiteur et le redirige vers le lien affilié approprié, avec un système de fallback vers une URL par défaut.

### Objectifs principaux
- ✅ Redirection géolocalisée automatique (< 200ms)
- ✅ Interface d'administration complète avec Filament 3
- ✅ Analytics en temps réel des clics
- ✅ Gestion multi-opérateurs et multi-pays
- ✅ Cache Redis pour performance optimale
- ✅ Rate limiting et sécurité intégrés

## 🚀 Installation

### Prérequis
- PHP 8.2+
- MySQL 8.0+
- Redis
- Composer
- Laravel Herd (recommandé pour le développement local)

### Installation step-by-step

1. **Cloner le projet**
```bash
cd ~/Herd
git clone [votre-repo] affiliate-geo-router
cd affiliate-geo-router
```

2. **Installer les dépendances PHP**
```bash
composer install
```

3. **Configuration de l'environnement**
```bash
cp .env.example .env
```

4. **Configurer la base de données dans `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=affiliate_geo_router
DB_USERNAME=root
DB_PASSWORD=
```

5. **Configurer Redis dans `.env`**
```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

6. **Créer la base de données**
```bash
mysql -u root -e "CREATE DATABASE affiliate_geo_router CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

7. **Générer la clé d'application**
```bash
php artisan key:generate
```

8. **Lancer les migrations**
```bash
php artisan migrate
```

9. **Lancer les seeders (249 pays + opérateurs de test)**
```bash
php artisan db:seed --class=InitialDataSeeder
```

10. **Créer un utilisateur administrateur**
```bash
# Méthode simple (interactif)
php artisan admin:create

# Méthode rapide (en une ligne)
php artisan admin:create --email=admin@admin.com --password=password123

# Mettre à jour un admin existant
php artisan admin:create --email=admin@admin.com --password=nouveaumotdepasse --force
```

## 🌍 Configuration MaxMind (Géolocalisation)

### Obtenir une clé API MaxMind

1. Créer un compte **gratuit** sur [MaxMind](https://www.maxmind.com/en/geolite2/signup)
2. Dans votre compte, aller dans "My License Key"
3. Générer une nouvelle clé (cocher "No" pour geoipupdate)
4. Ajouter les credentials dans `.env`:

```env
MAXMIND_LICENSE_KEY=votre_cle_license
MAXMIND_USER_ID=votre_user_id
```

### Télécharger la base de données GeoLite2

```bash
php artisan geo:update-database
```

### Système de Fallback Géolocalisation

Le système utilise une approche en cascade :

1. **MaxMind GeoLite2** (priorité) - Précis, rapide, offline
2. **ip-api.com** (fallback gratuit) - 1000 requêtes/mois, utilisé temporairement
3. **Pays par défaut** (FR) - Si tout échoue

> ⚠️ **Important**: Sans MaxMind configuré, le système utilise ip-api.com (limite 1000 req/mois) puis retombe sur la France

### Créer le dossier pour la base GeoIP
```bash
mkdir -p storage/app/geoip
```

## 🏠 Configuration Laravel Herd

### Installation automatique
Si votre projet est dans `~/Herd/affiliate-geo-router`, Laravel Herd le détectera automatiquement.

### URLs d'accès
- **Application**: http://affiliate-geo-router.test
- **Admin Panel**: http://affiliate-geo-router.test/admin
- **Health Check**: http://affiliate-geo-router.test/up

### Vérifier le site dans Herd
1. Ouvrir les préférences Herd
2. Vérifier que le site apparaît dans la liste
3. S'assurer que PHP 8.2+ est sélectionné

## 🎮 Utilisation

### Accès Admin
- URL: http://affiliate-geo-router.test/admin
- Email: admin@admin.com
- Mot de passe: password123

### Test des redirections
Les seeders créent 3 opérateurs de test :

- http://affiliate-geo-router.test/bet365
- http://affiliate-geo-router.test/william-hill
- http://affiliate-geo-router.test/unibet

### Gestion via l'interface admin
1. **Operators** : Créer et gérer les opérateurs
2. **Countries** : Activer/désactiver les pays
3. **Affiliate Links** : Configurer les liens par pays
4. **Dashboard** : Visualiser les analytics en temps réel

## 🛠️ Commandes Artisan

### Configuration et Setup
```bash
# Créer un admin (nouvelle syntaxe avec options)
php artisan admin:create --email=admin@example.com --password=monmotdepasse --force

# Populer la base avec 249 pays + opérateurs de test
php artisan db:seed --class=InitialDataSeeder

# Créer des données de test étendues (liens affiliés multiples)
php artisan setup:test-data
```

### Géolocalisation MaxMind
```bash
# Télécharger/mettre à jour la base GeoLite2
php artisan geo:update-database

# Tester la géolocalisation
php artisan tinker --execute="
\$service = app('App\Services\GeolocationService');
echo 'IP US (8.8.8.8): ' . \$service->getCountryCode('8.8.8.8') . PHP_EOL;
echo 'IP GB (8.8.4.4): ' . \$service->getCountryCode('8.8.4.4') . PHP_EOL;
"
```

### Analytics et Maintenance
```bash
# Nettoyage des anciens logs
php artisan clicks:cleanup

# Export des analytics
php artisan clicks:export --start=2024-01-01 --end=2024-01-31 --operator=bet365 --country=FR
```

## 🔧 Troubleshooting

### Erreur 404 sur les redirections
- Vérifier que l'opérateur existe et est actif
- Vérifier le slug (minuscules, tirets)

### Géolocalisation incorrecte
- Vérifier que la base GeoLite2 est téléchargée
- Tester avec une vraie IP publique (pas localhost)
- Vérifier les logs : `tail -f storage/logs/laravel.log`

### Cache non mis à jour
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Gestion des Queues avec Laravel Horizon

**Installation et configuration :**
```bash
# Horizon est inclus dans le projet
php artisan horizon:install
```

**En développement :**
```bash
# Lancer Horizon pour le monitoring des queues
php artisan horizon
```

**Accéder au dashboard Horizon :**
- URL : http://affiliate-geo-router.test/horizon
- Monitoring en temps réel des jobs
- Métriques et statistiques

**En production (Laravel Cloud) :**
- Horizon démarre automatiquement
- Variables d'environnement : `QUEUE_CONNECTION=redis`

### Erreur Filament/Admin
```bash
php artisan filament:upgrade
php artisan vendor:publish --tag=filament-panels --force
```

### Base de données non accessible
- Vérifier que MySQL est lancé
- Vérifier les credentials dans `.env`
- Vérifier que la base existe

## 📊 Performance

### Optimisations en production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Monitoring
- Temps de redirection cible : < 200ms
- Cache TTL : 30 minutes (liens), 1 heure (géo)
- Rate limit : 100 requêtes/minute/IP

## 🔒 Sécurité

- Rate limiting activé (100 req/min)
- IPs hashées avec SHA256 + salt
- Pas de cookies de tracking
- HTTPS forcé en production
- Validation stricte des URLs
- Protection CSRF sur l'admin

## 📦 Stack Technique

- **Framework**: Laravel 12
- **Admin Interface**: Filament 3
- **Database**: MySQL 8.0+
- **Cache/Queue**: Redis + Laravel Horizon
- **Géolocalisation**: MaxMind GeoLite2 + fallback ip-api.com
- **PHP**: 8.2+
- **Déploiement**: Laravel Cloud (production)

### Nouvelles Fonctionnalités

- **🌍 249 pays supportés** avec codes ISO complets
- **⚡ Géolocalisation hybride** (MaxMind + fallback ip-api.com)
- **🎛️ Interface admin améliorée** avec liens copiables
- **📊 Laravel Horizon** pour monitoring des queues
- **🛠️ Commandes admin flexibles** (création utilisateur en une ligne)
- **🔄 Seeders réutilisables** (updateOrCreate)

## 📝 License

Propriétaire - North Star Network