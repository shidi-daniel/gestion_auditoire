# Analyse et Conception — SGA (Q1–Q4)
**UPC · FCI · Programmation Web PHP · 2025-2026**

## Q1 — Analyse des Besoins

### Fonctionnalités Principales
1. Charger et afficher les 6 salles disponibles avec leurs capacités
2. Charger et afficher les 4 promotions et leurs effectifs
3. Distinguer les cours de tronc commun des cours d'option (L3/L4)
4. Générer un planning hebdomadaire sans collision ni dépassement de capacité
5. Sauvegarder le planning (planning.txt / planning.json)
6. Recharger et afficher le planning sous forme de tableau HTML

### Fonctionnalités Secondaires
- B1 : Détecter automatiquement les conflits dans un planning sauvegardé
- B2 : Générer un rapport d'occupation des salles (taux %)
- B3 : Modifier manuellement une affectation avec vérification des contraintes
- B4 : Formulaires HTML/PHP de saisie des données de configuration
- Authentification biométrique (extension sécurité)

## Q2 — Identification des Données

| Entité | Champs | Types |
|--------|--------|-------|
| Salle | id (string), designation (string), capacite (int) | id: "AUD-L1", capacite: 120 |
| Promotion | id (string), libelle (string), effectif (int) | id: "L2", effectif: 95 |
| Cours | id (string), intitule (string), volume_horaire (int), type (string), promotion (string) | type: "tronc_commun" ou "option" |
| Option | id (string), libelle (string), promotion_parent (string), effectif (int) | promotion_parent: "L3" |
| Créneau | jour (string), heure_debut (int), heure_fin (int) | heure_debut: 8, heure_fin: 12 |
| Affectation | id_cours, id_groupe, id_salle, heure_debut, heure_fin, jour, effectif, type | — |

## Q3 — Structure des Fichiers JSON

### salles.json
```json
[{"id":"AUD-L1","designation":"Auditoire principal – Licence 1","capacite":120}]
```

### promotions.json
```json
[{"id":"L1","libelle":"Licence 1","effectif":110}]
```

### cours.json
```json
[{"id":"WEB-L3","intitule":"Programmation Web PHP","volume_horaire":4,"type":"tronc_commun","promotion":"L3"}]
```

### options.json
```json
[{"id":"OPT-SECU","libelle":"Sécurité Informatique","promotion_parent":"L3","effectif":25}]
```

### planning.json (généré)
```json
{"Lundi":[{"id_cours":"CL-L1","id_groupe":"L1","id_salle":"AUD-L1","heure_debut":8,"heure_fin":12,...}]}
```

## Q4 — Contraintes Métier

| Règle | Condition | Comportement en violation |
|-------|-----------|--------------------------|
| Capacité suffisante | effectif_groupe ≤ capacite_salle | Affectation rejetée, salle suivante testée |
| Salle non double-bookée | Aucune salle sur même créneau | Créneau ou salle alternative cherchée |
| Groupe disponible | Un groupe ≠ deux cours simultanés | Créneau suivant testé |
| Plage horaire | 8h–12h, 13h–17h, Lundi–Vendredi | Seulement 10 créneaux disponibles |
