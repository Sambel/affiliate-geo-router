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

9. **Lancer les seeders pour les données de test**
```bash
php artisan db:seed --class=InitialDataSeeder
```

10. **Créer un utilisateur administrateur**
```bash
php artisan admin:create
# Email par défaut: admin@admin.com
# Mot de passe: password123
```

## 🌍 Configuration MaxMind (Géolocalisation)

### Obtenir une clé API MaxMind

1. Créer un compte sur [MaxMind](https://www.maxmind.com/en/geolite2/signup)
2. Générer une license key dans votre compte
3. Ajouter les credentials dans `.env`:

```env
MAXMIND_LICENSE_KEY=votre_cle_license
MAXMIND_USER_ID=votre_user_id
```

### Télécharger la base de données GeoLite2

```bash
php artisan geo:update-database
```

> ⚠️ Note: Sans la base GeoLite2, le système utilisera le pays par défaut (FR)

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

### Mise à jour base GeoIP
```bash
php artisan geo:update-database
```

### Nettoyage des anciens logs
```bash
php artisan clicks:cleanup
```

### Export des analytics
```bash
# Export complet
php artisan clicks:export

# Export avec filtres
php artisan clicks:export --start=2024-01-01 --end=2024-01-31 --operator=bet365 --country=FR
```

### Créer un admin
```bash
php artisan admin:create
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

### Queue jobs non exécutés
Lancer le worker :
```bash
php artisan queue:work redis
```

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
- **Admin**: Filament 3
- **Database**: MySQL 8.0+
- **Cache/Queue**: Redis
- **Géolocalisation**: MaxMind GeoLite2
- **PHP**: 8.2+

## 📝 License

Propriétaire - North Star Network