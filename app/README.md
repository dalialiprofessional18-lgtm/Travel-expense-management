# 🚗 MarocodeMove  
## Système de Gestion des Frais de Déplacement (SGFD)

MarocodeMove est une application web dédiée à la gestion moderne, automatisée et sécurisée des frais de déplacement professionnels.  
Elle permet aux employés de soumettre leurs dépenses, aux managers de les valider, et aux administrateurs d’en assurer la gestion globale, tout en garantissant la traçabilité, la transparence et l’efficacité du processus.

---

## 📌 Contexte & Problématique

Dans de nombreuses organisations, la gestion des frais de déplacement repose encore sur des méthodes traditionnelles (documents papier, échanges par email, fichiers Excel), entraînant des retards, des erreurs, un manque de suivi et une faible visibilité sur l’état des demandes.  
Face à ces limites, la digitalisation de ce processus devient indispensable afin d’optimiser la gestion des dépenses professionnelles et d’améliorer la prise de décision.

---

## 🎯 Objectifs du Projet

- Digitaliser et centraliser la gestion des frais de déplacement  
- Automatiser le processus de validation hiérarchique  
- Assurer la traçabilité et l’historique des demandes  
- Réduire les erreurs et les délais de traitement  
- Offrir une interface simple, intuitive et ergonomique  
- Générer des rapports et documents de manière automatique  

---

## 👥 Acteurs du Système

### Employé
- Déclarer un déplacement professionnel  
- Créer et soumettre des notes de frais avec justificatifs  
- Consulter l’état d’avancement de ses demandes  

### Manager
- Consulter les demandes de son équipe  
- Valider ou rejeter les notes de frais  
- Ajouter des commentaires et assurer le suivi  

### Administrateur
- Gérer les utilisateurs et les rôles  
- Approuver ou rejeter définitivement les demandes  
- Générer des rapports, statistiques et historiques globaux  

---

## 🧱 Architecture de l’Application

L’application repose sur une architecture **MVC (Model – View – Controller)** assurant une séparation claire des responsabilités, une meilleure maintenabilité et une évolutivité du système.

app/
├── Controllers/
├── Models/
│ └── DAO/
├── Views/
├── Core/
public/
config/


---

## 🛠️ Technologies Utilisées

### Backend
- **PHP 8** : logique métier et traitement côté serveur  
- **MySQL** : base de données relationnelle  
- **DAO (Data Access Object)** : accès structuré aux données  
- **Composer** : gestion des dépendances  
- **MPDF** : génération de documents PDF  

### Frontend
- **HTML5 / CSS3**  
- **Bootstrap 5** : interface responsive et moderne  
- **JavaScript**  

### APIs & Services Externes
- **GeoNames API** : importation des pays et villes du monde  
- **Photon API** : cartographie et géolocalisation  
- **ImgBB API** : hébergement des images et justificatifs  
- **Gemini API** : assistant IA pour l’aide et l’analyse  

### Communication Temps Réel
- **WebSocket** : notifications et interactions dynamiques  

---

## 🔐 Fonctionnalités Principales

- Authentification et gestion des profils  
- Gestion des rôles (Employé, Manager, Administrateur)  
- Gestion des déplacements professionnels  
- Création et soumission des notes de frais  
- Upload et gestion des justificatifs  
- Validation hiérarchique des demandes  
- Notifications et messagerie interne  
- Tableau de bord et suivi en temps réel  
- Génération de rapports et documents PDF  
- Assistant IA intégré  

---

## 📊 Gestion de Projet

- **Méthodologie** : Agile Scrum  
- **Gestion des tâches** : Trello  
- **Contrôle de version** : Git & GitHub  

Le projet a été organisé en plusieurs sprints permettant une livraison progressive et itérative des fonctionnalités.

---

## 🚀 Installation & Lancement

### Prérequis
- PHP ≥ 8.0  
- MySQL  
- XAMPP ou WAMP  
- Composer  
- Navigateur web moderne  

### Étapes d’installation

```bash
# Cloner le dépôt
git clone https://github.com/username/marocodemove.git

# Accéder au dossier du projet
cd marocodemove

# Installer les dépendances
composer install

--- 

```markdown

- Configurer la base de données dans le fichier de configuration

- Importer le script SQL

- Démarrer le serveur Apache

- Accéder à l’application via : http://localhost/sgfd/public

## 📈 Perspectives d’Évolution

Développement d’une application mobile (Android / iOS)

Analyse intelligente des dépenses via l’IA

Gestion budgétaire avancée

Notifications push en temps réel

Tableau de bord décisionnel pour l’administration

## 👨‍💻 Réalisé par

Ali Dali & Abdelghafour Korachi
Projet académique 
📧 Email : dalialiprofessional18@gmail.com