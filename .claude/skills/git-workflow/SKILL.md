---
name: git-workflow
description: Workflow Git avec branches et commits propres. Utiliser pour toute operation Git.
---

# Workflow Git

## Nouvelle feature
```bash
git checkout main
git pull
git checkout -b feature/nom-de-la-feature
```

## Pendant le dev (commits reguliers)
```bash
git add .
git commit -m "type: description courte"
```

## Types de commits
- `feat:` nouvelle fonctionnalite
- `fix:` correction bug
- `refactor:` refactoring sans changer le comportement
- `test:` ajout ou modification de tests
- `docs:` documentation
- `style:` formatage, pas de changement de code

## Quand feature terminee
```bash
# Retour sur main
git checkout main

# Fusionner la feature
git merge feature/nom-de-la-feature

# Envoyer sur le serveur
git push

# Supprimer la branche locale
git branch -d feature/nom-de-la-feature
```

## Commandes utiles
```bash
# Voir l'etat actuel
git status

# Voir l'historique
git log --oneline

# Voir les changements
git diff

# Annuler changements non commites
git checkout .

# Revenir a un commit precedent
git checkout [hash]

# Voir les branches
git branch
```

## Regles
- Jamais push direct sur main
- Un commit = une chose
- Message clair
- Merger seulement apres tests + revue
- ne met pas la signature de claude et ne le met pas comme contributeur