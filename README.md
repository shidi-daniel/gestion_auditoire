# Système de Gestion des Auditoires (SGA)
**Université Protestante au Congo · Faculté des Sciences Informatiques**
**Année académique 2025–2026 · PHP Procédural**

## 🔐 Authentification Biométrique
- **Reconnaissance Faciale** : face-api.js (@vladmandic v1.7.14), 5 captures multi-angles, distance Euclidienne < 0.5
- **Empreinte Digitale** : WebAuthn/FIDO2 (navigator.credentials.create/get), authentificateur de plateforme

## 📁 Structure des Fichiers
```
├── connexion.php          ← Page d'accueil (login biométrique)
├── inscription.php        ← Enregistrement visage + empreinte
├── index.php              ← Tableau de bord (Q6 du TD)
├── formulaires.php        ← Saisie données (B4)
├── telecharger.php        ← Export/download fichiers
├── deconnexion.php        ← Déconnexion session
├── gestion_utilisateurs.php ← Admin utilisateurs
├── fonctions.php          ← Toutes fonctions PHP (Q5–Q9, B1–B2)
├── api/
│   ├── face_register.php  ← API enregistrement visage
│   ├── face_verify.php    ← API vérification visage + session
│   ├── fp_challenge.php   ← WebAuthn challenge
│   ├── fp_register.php    ← API enregistrement empreinte
│   ├── fp_verify.php      ← API vérification empreinte + session
│   └── user_delete.php    ← Suppression utilisateur
├── data/
│   ├── salles.json        ← 6 salles (AUD-L1..L4, SALLE-MACH, SALLE-MGT)
│   ├── promotions.json    ← 4 promotions L1–L4
│   ├── cours.json         ← Cours tronc commun
│   ├── options.json       ← Options L3/L4
│   ├── faces_data/        ← Descripteurs faciaux (userId.json)
│   └── fingerprints_data/ ← Credentials WebAuthn
└── output/
    ├── planning.txt        ← Planning lisible
    ├── planning.json       ← Planning rechargeable
    └── rapport_occupation.txt ← Rapport B2
```

## 🚀 Installation
1. Placer dans un répertoire servi par Apache/Nginx avec PHP ≥ 7.4
2. S'inscrire via `inscription.php` (face + empreinte)
3. Se connecter via `connexion.php`
4. Générer le planning : `index.php?action=generer`

## 📋 Fonctions TD Implémentées
| Q# | Fonction | Description |
|----|----------|-------------|
| Q5 | `charger_salles/promotions/cours/options()` | Lecture JSON avec gestion d'erreurs |
| Q6 | `salle_disponible()`, `capacite_suffisante()`, `creneau_libre_groupe()` | Contraintes métier |
| Q7 | `generer_planning()` | Algorithme best-fit sans conflit |
| Q8 | `sauvegarder_planning()` | Export TXT + JSON |
| Q9 | `charger_planning()`, `afficher_planning_html()` | Rechargement + tableau HTML |
| B1 | `detecter_conflits()` | Analyse des conflits salle/groupe |
| B2 | `generer_rapport_occupation()` | Taux d'occupation des salles |
