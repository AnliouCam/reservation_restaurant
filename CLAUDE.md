# CLAUDE.md


## Documentation
- `docs/PLAN.md` → Plan et architecture // 
- `docs/ETAT_AVANCEMENT.md` → Progression
- toujours se referer a ces fichier pour savoir ou on en est
---

## Agents disponibles(tres important)

| Agent | Utilite | Quand l'utiliser |
|-------|---------|------------------|
| `reviewer` | Revue de code | Apres chaque feature |
| `security` | Check securite | Avant chaque push |
| `tester` | Ecrire les tests | Apres features critiques |

### Comment les utiliser

```
"Fais une revue de code"              → reviewer
"Check la securite"                   → security
"Ecris les tests pour [feature]"      → tester
```
tu dois savoir que tu dois les faire automatiquement me me demander la permission avant de le faire

```
```

Voir tous les agents : `/agents`

---

## Regles obligatoires

### Securite (toujours verifier)
- Valider tous les inputs utilisateur
- Verifier les autorisations (user voit que SES donnees)
- Jamais de secrets/mots de passe dans le code
- Hasher les mots de passe
- Proteger contre injections (SQL, XSS, etc.)

### Git
- Une branche par feature : `feature/nom`
- Commits reguliers avec messages clairs
- Jamais push direct sur main
- Merger seulement quand tests passent

### Code
- Logique metier separee (Services/Utils/Helpers)
- Pas de code duplique
- Pas de code mort ou commente
- Nommage clair et coherent

### Tests obligatoires
Tester au minimum :
- Authentification (login, register, logout)
- Autorisation (user ne voit que SES donnees)
- Fonctionnalites critiques (paiement, donnees sensibles)

---

## Entites principales

| Entite | Champs cles | Relations |
|--------|-------------|-----------|
|        |             |           |

---

## Workflow dev

```
1. Creer branche feature/xxx
2. Coder la feature
3. "Fais une revue de code"        → agent reviewer
4. "Ecris les tests pour [feature]" → agent tester
5. "Check la securite"              → agent security
6. Merger si tout passe
7. Push
```

---

## Prompts utiles

| Quand | Prompt |
|-------|--------|
| Apres feature | "Fais une revue de code" |
| Apres feature | "Ecris les tests pour [feature]" |
| Avant push | "Check la securite du code" |
| Bug | "Debug cette erreur: [erreur]" |
| Git | "Aide-moi avec Git" |

---

## Note : Sentry (erreurs en production)

### C'est quoi ?
Sentry capture les erreurs de ton app quand elle est en production.
Sans Sentry, si un user a un bug, tu ne le sais pas.

### Quand l'installer ?
- Dev local : PAS BESOIN (tu vois les erreurs dans le terminal)
- Production : OBLIGATOIRE (sinon tu vois rien)

### Comment l'installer ?

**Laravel :**
```bash
composer require sentry/sentry-laravel
```

**Next.js :**
```bash
npm install @sentry/nextjs
```

**Vue.js :**
```bash
npm install @sentry/vue
```

**Flutter :**
```bash
flutter pub add sentry_flutter
```

### Configuration
1. Cree un compte sur https://sentry.io (gratuit)
2. Cree un projet
3. Copie le DSN (URL unique)
4. Ajoute dans ton .env :
```
SENTRY_DSN=https://xxx@sentry.io/xxx
```

### Resultat
Quand une erreur arrive en prod → Sentry t'envoie une notification avec :
- Message d'erreur
- Fichier et ligne
- Stack trace
- Info sur le user/navigateur
