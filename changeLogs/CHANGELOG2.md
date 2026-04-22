# **CHANGELOG - Branche feature/exo1-api-auth-minimum**

## Résumé global de la branche (vue mentor)

**Objectif** : transformer le monolithe Livewire en une API REST versionnée, sécurisée par token Bearer, validée par Newman.

| Thème | Ce qui a été livré | Commits |
|---|---|---|
| Structure MVC | routes/api.php · controllers API · services · BaseApiController · contrat JSON `status/message/data` | `70f798e` `11cf32a` |
| Sécurité utilisateur | Notes et tags scopés `user_id` · ownership 403 · `HasApiTokens` sur User | `70f798e` `11cf32a` `f604dc6` |
| Auth Sanctum (token) | register → token 201 · login → token 200 · logout → révocation · 401 auto Sanctum | `f604dc6` |
| Validation | 4 FormRequest classes · zéro `Validator::make` dans les controllers · 422 via global handler | `a8a9104` |
| Gestion erreurs | Global handler centralisé · 401/403/404/422/500 · format uniforme JSON pour toutes les exceptions | `d0d0dca` `a8a9104` |
| Rate limiter | `throttleApi()` + `RateLimiter::for('api')` · 60 req/min par IP · 429 si dépassé | `a8a9104` |
| CORS | `config/cors.php` · `CORS_ALLOWED_ORIGINS` dans `.env` · zéro code à changer pour brancher le front | `a8a9104` |
| Tests Postman/Newman | Collection 8 requêtes · variables auto · email unique pré-request · Newman **10/10** assertions | `11cf32a` `f604dc6` `d0d0dca` |

**Résultat final** : Newman 10/10 assertions, 0 échec.

> Commits `70f798e` et `11cf32a` sont détaillés dans **CHANGELOG.md**.
> Ce fichier détaille les commits `f604dc6` · `d0d0dca` · `a8a9104`.

---

## 1. Authentification Sanctum (token Bearer)

**Commit** : `f604dc6`

### Ce qui a été mis en place

- Package Sanctum installé, config publiée, migration `personal_access_tokens` exécutée
- `User` model : trait `HasApiTokens` + cast `password → hashed` (plus besoin de `Hash::make`)
- `register` : crée le user et génère un token → `201`
- `login` : vérifie les credentials, supprime les anciens tokens, génère un nouveau → `200`
- `logout` : révoque uniquement le token courant → `200`
- Routes protégées par `auth:sanctum` : `401` automatique si token absent ou invalide

### Fichiers créés/modifiés

- `config/sanctum.php` (créé)
- `database/migrations/2026_04_21_191543_create_personal_access_tokens_table.php` (créé)
- `app/Models/User.php` (HasApiTokens)
- `app/Services/AuthService.php` (logique register/login/logout réelle)
- `app/Http/Controllers/API/AuthController.php` (endpoints finalisés)
- `.env.example` (variables Sanctum)
- `composer.json` / `composer.lock`

---

## 2. Gestion des erreurs homogène + FormRequest

**Commits** : `d0d0dca` · `a8a9104` (partiel)

### Ce qui a été mis en place

**Error handling centralisé** (`bootstrap/app.php` — `withExceptions`) :

| Exception | Code HTTP | Source |
|---|---|---|
| `ValidationException` | 422 | FormRequest auto-throw |
| `AuthenticationException` | 401 | Service (login) ou Sanctum |
| `AuthorizationException` | 403 | Service (delete note) |
| `ModelNotFoundException` | 404 | Service (findOrFail) |
| `Exception` | 500 | Toute erreur inattendue |

Format uniforme pour tous : `{ status, message, data }`.

**FormRequest** — 4 classes injectées automatiquement, zéro `Validator::make` dans les controllers :

| FormRequest | Règles |
|---|---|
| `RegisterRequest` | `name`, `email` unique, `password` confirmed |
| `LoginRequest` | `email`, `password` |
| `StoreNoteRequest` | `text`, `tag_id` exists:tags,id |
| `StoreTagRequest` | `name` max:255 |

**Services** : lèvent des exceptions métier, les controllers orchestrent seulement.

### Fichiers créés

- `app/Http/Requests/RegisterRequest.php`
- `app/Http/Requests/LoginRequest.php`
- `app/Http/Requests/StoreNoteRequest.php`
- `app/Http/Requests/StoreTagRequest.php`

### Fichiers modifiés

- `bootstrap/app.php` (withExceptions + withMiddleware)
- `app/Services/AuthService.php` (login throws `AuthenticationException`)
- `app/Services/NoteService.php` (delete void, findOrFail, throws `AuthorizationException`)
- `app/Services/TagService.php` (pattern homogène, strict payload)
- `app/Http/Controllers/API/AuthController.php` (FormRequest injectée)
- `app/Http/Controllers/API/NoteController.php` (FormRequest injectée)
- `app/Http/Controllers/API/TagController.php` (FormRequest injectée)
- `postman/README.md`
- `postman/API-v1.postman_collection.json`

---

## 3. Middleware : rate limiter + CORS

**Commit** : `a8a9104` (partiel)

### Ce qui a été mis en place

**Rate limiter** :
- `throttleApi()` activé dans `bootstrap/app.php` → attend un limiter nommé `api`
- En Laravel 11, ce limiter n'est plus créé automatiquement (ancien `RouteServiceProvider` supprimé)
- Fix : `RateLimiter::for('api', ...)` déclaré dans `AppServiceProvider::boot()`
- Limite : 60 req/min par IP → `429` si dépassé, `500` (MissingRateLimiterException) si absent

**CORS** :
- `HandleCors` est déjà dans la stack Laravel globale — aucune registration manuelle
- `config/cors.php` lit `CORS_ALLOWED_ORIGINS` depuis `.env`
- Pour brancher le front React : juste changer `.env`, zéro modification de code

```env
# dev
CORS_ALLOWED_ORIGINS=http://localhost:5173
# prod
CORS_ALLOWED_ORIGINS=https://myapp.com
```

### Fichiers créés

- `config/cors.php`

### Fichiers modifiés

- `app/Providers/AppServiceProvider.php` (RateLimiter::for api)
- `.env.example` (CORS_ALLOWED_ORIGINS)
- `bootstrap/app.php` (TODO → DONE)

---

## 4. Collection Postman et validation Newman

**Commits** : `11cf32a` · `f604dc6` · `d0d0dca`

### Ce qui a été mis en place

- Collection `API-v1.postman_collection.json` : 8 requêtes couvrant tout le flux
- Environnement `local.postman_environment.json` : `baseUrl`, `token`, `tagId`, `noteId`
- Pré-request `register` : génère un email unique `test.user.{timestamp}@example.com` pour éviter les collisions SQL
- Variables `token`, `tagId`, `noteId` alimentées automatiquement pendant la collection
- `postman/README.md` : doc API complète (flux, payloads, codes HTTP, email unique)

**Résultat Newman** :

```
8 requests — 10 assertions — 0 failed
```

### Fichiers créés/modifiés

- `postman/API-v1.postman_collection.json`
- `postman/local.postman_environment.json`
- `postman/README.md`
- `postman/reports/postman-report.html`
- `postman/reports/postman-report.json`


**Commit** : `f604dc6`

### Ce qui a été mis en place

- Package Sanctum installé, config publiée, migration `personal_access_tokens` exécutée
- `User` model : trait `HasApiTokens` + cast `password → hashed` (plus besoin de `Hash::make`)
- `register` : crée le user et génère un token → `201`
- `login` : vérifie les credentials, supprime les anciens tokens, génère un nouveau → `200`
- `logout` : révoque uniquement le token courant → `200`
- Routes protégées par `auth:sanctum` : `401` automatique si token absent ou invalide

### Fichiers créés/modifiés

- `config/sanctum.php` (créé)
- `database/migrations/2026_04_21_191543_create_personal_access_tokens_table.php` (créé)
- `app/Models/User.php` (HasApiTokens)
- `app/Services/AuthService.php` (logique register/login/logout réelle)
- `app/Http/Controllers/API/AuthController.php` (endpoints finalisés)
- `.env.example` (variables Sanctum)
- `composer.json` / `composer.lock`

---

## 3. Gestion des erreurs homogène + FormRequest

**Commits** : `d0d0dca` · `a8a9104` (partiel)

### Ce qui a été mis en place

**Error handling centralisé** (`bootstrap/app.php` — `withExceptions`) :

| Exception                 | Code HTTP | Source                     |
| ------------------------- | --------- | -------------------------- |
| `ValidationException`     | 422       | FormRequest auto-throw     |
| `AuthenticationException` | 401       | Service (login) ou Sanctum |
| `AuthorizationException`  | 403       | Service (delete note)      |
| `ModelNotFoundException`  | 404       | Service (findOrFail)       |
| `Exception`               | 500       | Toute erreur inattendue    |

Format uniforme pour tous : `{ status, message, data }`.

**FormRequest** — 4 classes injectées automatiquement, zéro `Validator::make` dans les controllers :

| FormRequest        | Règles                                       |
| ------------------ | -------------------------------------------- |
| `RegisterRequest`  | `name`, `email` unique, `password` confirmed |
| `LoginRequest`     | `email`, `password`                          |
| `StoreNoteRequest` | `text`, `tag_id` exists:tags,id              |
| `StoreTagRequest`  | `name` max:255                               |

**Services** : lèvent des exceptions métier, les controllers orchestrent seulement.

### Fichiers créés

- `app/Http/Requests/RegisterRequest.php`
- `app/Http/Requests/LoginRequest.php`
- `app/Http/Requests/StoreNoteRequest.php`
- `app/Http/Requests/StoreTagRequest.php`

### Fichiers modifiés

- `bootstrap/app.php` (withExceptions + withMiddleware)
- `app/Services/AuthService.php` (login throws `AuthenticationException`)
- `app/Services/NoteService.php` (delete void, findOrFail, throws `AuthorizationException`)
- `app/Services/TagService.php` (pattern homogène, strict payload)
- `app/Http/Controllers/API/AuthController.php` (FormRequest injectée)
- `app/Http/Controllers/API/NoteController.php` (FormRequest injectée)
- `app/Http/Controllers/API/TagController.php` (FormRequest injectée)
- `postman/README.md`
- `postman/API-v1.postman_collection.json`

---

## 4. Middleware : rate limiter + CORS

**Commit** : `a8a9104` (partiel)

### Ce qui a été mis en place

**Rate limiter** :

- `throttleApi()` activé dans `bootstrap/app.php` → attend un limiter nommé `api`
- En Laravel 11, ce limiter n'est plus créé automatiquement (ancien `RouteServiceProvider` supprimé)
- Fix : `RateLimiter::for('api', ...)` déclaré dans `AppServiceProvider::boot()`
- Limite : 60 req/min par IP → `429` si dépassé, `500` (MissingRateLimiterException) si absent

**CORS** :

- `HandleCors` est déjà dans la stack Laravel globale — aucune registration manuelle
- `config/cors.php` lit `CORS_ALLOWED_ORIGINS` depuis `.env`
- Pour brancher le front React : juste changer `.env`, zéro modification de code

```env
# dev
CORS_ALLOWED_ORIGINS=http://localhost:5173
# prod
CORS_ALLOWED_ORIGINS=https://myapp.com
```

### Fichiers créés

- `config/cors.php`

### Fichiers modifiés

- `app/Providers/AppServiceProvider.php` (RateLimiter::for api)
- `.env.example` (CORS_ALLOWED_ORIGINS)
- `bootstrap/app.php` (TODO → DONE)

---

## 5. Collection Postman et validation Newman

**Commits** : `11cf32a` · `f604dc6` · `d0d0dca`

### Ce qui a été mis en place

- Collection `API-v1.postman_collection.json` : 8 requêtes couvrant tout le flux
- Environnement `local.postman_environment.json` : `baseUrl`, `token`, `tagId`, `noteId`
- Pré-request `register` : génère un email unique `test.user.{timestamp}@example.com` pour éviter les collisions SQL
- Variables `token`, `tagId`, `noteId` alimentées automatiquement pendant la collection
- `postman/README.md` : doc API complète (flux, payloads, codes HTTP, email unique)

**Résultat Newman** :

```
8 requests — 10 assertions — 0 failed
```

### Fichiers créés/modifiés

- `postman/API-v1.postman_collection.json`
- `postman/local.postman_environment.json`
- `postman/README.md`
- `postman/reports/postman-report.html`
- `postman/reports/postman-report.json`

---

## Récapitulatif final

| Thème                                           | Statut |
| ----------------------------------------------- | ------ |
| Structure MVC (routes / controllers / services) | ✅     |
| Authentification Sanctum Bearer Token           | ✅     |
| Gestion des erreurs centralisée + FormRequest   | ✅     |
| Rate limiter (60 req/min) + CORS via .env       | ✅     |
| Newman 10/10 assertions                         | ✅     |
| Documentation Postman README                    | ✅     |
