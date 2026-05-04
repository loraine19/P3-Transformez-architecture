# CHANGELOG 3 — Cleanup API-only + Tests

**Date :** 2026-05-04
**Branche :** `chore/clean-api-only`
**Scope :** Suppression Livewire/Blade, nettoyage code, validation tests

---

## Ce qui a été fait

### Suppression couche Livewire / Blade

| Fichier / Dossier                       | Action                                    |
| --------------------------------------- | ----------------------------------------- |
| `app/Livewire/Notes.php`                | Supprimé — remplacé par NoteService       |
| `app/Livewire/TagForm.php`              | Supprimé — remplacé par TagService        |
| `app/Livewire/Actions/Logout.php`       | Supprimé — remplacé par AuthService       |
| `resources/views/` (30+ fichiers blade) | Supprimés — backend API-only, pas de HTML |

### Réécriture `routes/web.php`

Route unique retournant un stub JSON `{ status, message, data }` — `routes/auth.php` n'est plus inclus.

### Nettoyage du code

| Fichier                                         | Modification                                     |
| ----------------------------------------------- | ------------------------------------------------ |
| Tous les controllers + services + modèles       | Suppression `// DONE:` et commentaires de header |
| `app/Models/User.php`                           | Suppression `initials()` (méthode Blade inutile) |
| `app/Services/AuthService.php` (register/login) | Ajout `user.id` dans le payload de retour        |

### Convention Clean Architecture conservée

- `/* BLOC */` (pattern personnel) — tous conservés
- Commentaires inline explicatifs — conservés

---

## Validation — Tests

### API — Newman

| Suite           | Assertions | Résultat |
| --------------- | ---------- | -------- |
| Newman `API-v1` | 15/15      | ✅ PASS  |

### Front — Tests manuels exploratoires

| Scénario                                 | Résultat |
| ---------------------------------------- | -------- |
| Register → redirect login                | ✅       |
| Login → dashboard                        | ✅       |
| Dashboard charge notes + tags via API    | ✅       |
| Créer une note → ajout sans reload       | ✅       |
| Supprimer une note → retrait sans reload | ✅       |
| Créer un tag                             | ✅       |
| Logout → redirect `/login`               | ✅       |
| Accès `/` sans token → redirect login    | ✅       |

---

## Résultat

| Critère                     | Avant               | Après       |
| --------------------------- | ------------------- | ----------- |
| Fichiers Livewire           | 3 classes           | 0           |
| Fichiers Blade              | 30+                 | 0           |
| Routes web                  | ~10 routes Livewire | 1 stub JSON |
| Commentaires `// DONE:`     | présents            | supprimés   |
| `User::initials()`          | présente            | supprimée   |
| `user.id` dans réponse auth | absent              | présent     |
| Backend réellement API-only | ❌                  | ✅          |
| Newman API                  | —                   | 15/15 ✅    |
| Tests front manuels         | —                   | 8/8 ✅      |
