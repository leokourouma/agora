# 🏛️ Agora Project - POC

POC d'un réseau social dynamique avec mode sombre binaire (Light/Dark) conçu avec **SvelteKit** (Svelte 5) et un backend **PHP/MySQL**.

## 🚀 Lancement Rapide (Zéro Configuration)

Ce projet est entièrement dockerisé pour garantir un environnement identique sur n'importe quelle machine.

```bash
# 1. Cloner le projet
git clone git@github.com:leokourouma/agora.git
cd agora

# 2. Lancer l'infrastructure (Frontend, Backend, DB)
docker-compose up --build
Le projet sera accessible sur :

Frontend : http://localhost:5173

Backend API : http://localhost:8888

🐳 Architecture Docker
Le projet utilise Docker Compose pour orchestrer trois services :

App (SvelteKit) : Le frontend tournant sous Node.js.

API (PHP) : Le serveur backend gérant la logique des posts et commentaires.

Database (MySQL) : Persistance des données.

💾 Initialisation et Seeding
La base de données est automatiquement initialisée et peuplée lors du premier lancement :

Le script /docker/mysql/init.sql crée les tables nécessaires.

Données de test (Seed) : Des utilisateurs, posts et commentaires sont injectés par défaut pour tester immédiatement le mode sombre et les interactions.

🛠️ Spécificités Techniques
Mode Sombre Binaire
Le système de thème est conçu pour être radical (binaire) afin d'éviter les styles hybrides :

Contrôle : Géré par src/lib/theme.svelte.js via document.documentElement.classList.

Priorité CSS : Utilise des sélecteurs html:not(.dark) et html.dark dans src/app.css pour forcer les couleurs des composants internes (PostCard, Composer).

Intégration API
Format des appels : Pour les requêtes POST réussies, assurez-vous de ne pas inclure le champ Items dans le corps de l'appel API, conformément aux tests de validation du POC.

CORS : Le backend PHP autorise les requêtes provenant de l'origine du frontend Docker.

📁 Structure du Projet
Plaintext
.
├── src/
│   ├── lib/
│   │   ├── components/  # PostCard, CommentThread, VoteControl
│   │   └── theme.svelte.js # Store de thème réactif (Svelte 5)
│   └── app.css          # Variables CSS et styles forcés Dark/Light
├── php/                 # Scripts PHP du backend
├── docker/              # Dockerfiles et scripts SQL de seed
└── docker-compose.yml   # Orchestration des services

---

### Prochaines étapes suggérées :
* **Vérification Docker** : Est-ce que tu as déjà ton fichier `docker-compose.yml` de prêt ou tu veux que je te donne une version qui lie parfaitement ton dossier `php/` et ton `src/` ?
* **Git Push** : Une fois le README enregistré, n'oublie pas de `git add README.md`, `git co