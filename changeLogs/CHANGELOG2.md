# **CHANGELOG - 2**

## **Commit :**

feat(api): implement Sanctum bearer auth and secure v1 notes/tags endpoints

1. ### **Recap de travail effectue**
    1. Sanctum active cote backend
        1. Package Sanctum installe
        2. Config Sanctum publiee
        3. Migration tokens executee (personal_access_tokens)
        4. Variables Sanctum ajoutees dans .env.example
    2. Auth utilisateur passe de stub a logique reelle
        1. User model connecte a Sanctum via HasApiTokens
        2. Register: creation user + generation token
        3. Login: verification credentials + regeneration token
        4. Logout: revocation du token courant
    3. AuthController finalise
        1. Validation explicite des entrees
        2. Reponses JSON homognes (status, message, data)
        3. Codes HTTP explicites (201, 200, 401, 422)
    4. Routes API v1 securisees
        1. Routes publiques: register, login
        2. Routes protegees par auth:sanctum: logout, notes, tags
    5. Notes et Tags alignes sur la securite utilisateur
        1. Notes scopees par user_id
        2. Controle ownership sur suppression de note (403 si non proprietaire)
        3. Mapping erreurs metier vers HTTP (404, 403)
        4. Validation TagController harmonisee avec les autres controllers
    6. Collection Postman adaptee au contrat API reel
        1. Headers Accept/Authorization harmonises
        2. Payload note aligne (text, tag_id)
        3. Variables token, tagId, noteId automatisees
        4. prerequest register pour email unique
    7. Validation technique
        1. Run Newman execute sur la collection v1
        2. Resultat final: 10 assertions passees, 0 echec

2. ### **Fichiers crees**
    1. .../config/sanctum.php
    2. .../database/migrations/2026_04_21_191543_create_personal_access_tokens_table.php

3. ### **Fichiers modifies**
    1. .../.env.example
    2. .../composer.json
    3. .../composer.lock
    4. .../app/Models/User.php
    5. .../app/Services/AuthService.php
    6. .../app/Services/NoteService.php
    7. .../app/Http/Controllers/API/AuthController.php
    8. .../app/Http/Controllers/API/NoteController.php
    9. .../app/Http/Controllers/API/TagController.php
    10. .../routes/api.php
    11. .../postman/API-v1.postman_collection.json
    12. .../postman/local.postman_environment.json
    13. .../postman/reports/postman-report.html
    14. .../postman/reports/postman-report.json

4. ### **Tests et verification**
    1. Auth endpoints verifies (register, login, logout)
    2. Routes protegees verifiees avec Bearer token
    3. Notes/Tags verifies avec create/list/delete selon collection
    4. Newman final valide: 8 requests, 10 assertions, 0 echec

5. ### **Note de progression (Step 3 Exo 1)**
    1. Step 3 respecte: routes REST exposees, statuts HTTP explicites, format JSON homogene.
    2. Le rythme est reste coherent: on a d abord pose la securite, puis aligne les controllers/services, puis valide par tests API.
    3. Aucun saut de phase: on est reste sur l objectif Step 3 (API + auth + tests), sans basculer sur Step 4 documentation finale.

---

## **Commit :**

refactor(api): homogenize error handling + introduce FormRequest validation

1. ### **Recap de travail effectue**
    1. Error handling homogenise (pattern service-first)
        1. Services levent des exceptions domain (AuthenticationException, AuthorizationException)
        2. Controllers simplifies: ne mappent plus les erreurs manuellement
        3. Global handler (bootstrap/app.php) formate tout en JSON homogene 401/403/404/422/500
        4. `withExceptions`: 401/403 utilisent `$e->getMessage()` pour remonter le message service
    2. Middleware API configure
        1. `throttleApi()` active dans `withMiddleware`
        2. TODO CORS commente (a configurer quand front decouple)
    3. FormRequest introduit pour finaliser la separation controller/validation
        1. `RegisterRequest`: valide name/email/password/confirmed
        2. `LoginRequest`: valide email/password
        3. `StoreNoteRequest`: valide text/tag_id (avec exists:tags,id)
        4. `StoreTagRequest`: valide name
        5. Validation auto-throws `ValidationException` -> 422 via global handler
        6. Controllers ne contiennent plus aucun `Validator::make` ni `$validator->fails()`
    4. TagService aligne sur le pattern des autres services
        1. Header comment ajoute (pattern homogene)
        2. `$payload['name'] ?? ''` corrige en `$payload['name']` (validation garantit sa presence)
        3. Commentaires `/* PUBLIC METHOD */` ajoutes
    5. Architecture resultante: separation totale par responsabilite
        1. FormRequest: valide le payload entrant
        2. Controller: orchestre (injecte request validee + user id, retourne JSON)
        3. Service: logique metier, leve les exceptions
        4. Global handler: formate toutes les erreurs en JSON

2. ### **Fichiers crees**
    1. .../app/Http/Requests/RegisterRequest.php
    2. .../app/Http/Requests/LoginRequest.php
    3. .../app/Http/Requests/StoreNoteRequest.php
    4. .../app/Http/Requests/StoreTagRequest.php

3. ### **Fichiers modifies**
    1. .../bootstrap/app.php (withMiddleware + withExceptions)
    2. .../app/Services/AuthService.php (login throws AuthenticationException)
    3. .../app/Services/NoteService.php (delete void + findOrFail + AuthorizationException)
    4. .../app/Services/TagService.php (header + strict payload + pattern comments)
    5. .../app/Http/Controllers/API/AuthController.php (FormRequest injection)
    6. .../app/Http/Controllers/API/NoteController.php (FormRequest injection)
    7. .../app/Http/Controllers/API/TagController.php (FormRequest injection)
    8. .../postman/README.md (doc API + unique email + smoke test)
    9. .../postman/API-v1.postman_collection.json (descriptions compactes)

4. ### **Tests et verification**
    1. Lint PHP valide sur tous les fichiers modifies (php -l)
    2. Pattern verifie: services levent, controllers orchestrent, FormRequest valide
    3. Smoke test manuel realise (note dans postman/README.md)

5. ### **Note de progression**
    1. Separation des responsabilites complete: FormRequest / Controller / Service / GlobalHandler
    2. Aucun `Validator::make` restant dans les controllers
    3. Prochain: CORS config quand front React decouple (TODO commente dans bootstrap/app.php)

---

## **Commit :**

fix(api): configure rate limiter + add CORS env-based config

1. ### **Recap de travail effectue**
    1. RateLimiter `api` declare dans AppServiceProvider
        1. `throttleApi()` dans bootstrap/app.php attend un rate limiter nomme `api`
        2. En Laravel 11, il n'est plus cree automatiquement (ancien RouteServiceProvider)
        3. Fix: `RateLimiter::for('api', ...)` defini dans `AppServiceProvider::boot()`
        4. Limite: 60 req/min par IP
        5. Sans ce fix: 500 systematique sur toutes les routes API
    2. CORS configure via `.env` - aucun code a changer pour câbler le front
        1. `config/cors.php` cree avec `allowed_origins` lu depuis `.env`
        2. Variable `CORS_ALLOWED_ORIGINS=*` ajoutee dans `.env.example`
        3. En prod: `CORS_ALLOWED_ORIGINS=https://myapp.com` dans `.env` uniquement
        4. `supports_credentials: false` (mode Bearer token, pas SPA cookie)
        5. HandleCors est deja dans la stack Laravel globale - pas de registration manuelle

2. ### **Fichiers crees**
    1. .../config/cors.php

3. ### **Fichiers modifies**
    1. .../app/Providers/AppServiceProvider.php (RateLimiter::for api)
    2. .../.env.example (CORS_ALLOWED_ORIGINS ajoutee)
    3. .../bootstrap/app.php (TODO CORS remplace par DONE)

4. ### **Tests et verification**
    1. Newman relance: 10/10 assertions passees, 0 echec
    2. 500 sur register disparu apres fix AppServiceProvider
    3. Comportement verifie: 429 si depassement, 500 si limiter absent

5. ### **Note de progression**
    1. Step 3 techniquement complet et valide Newman
    2. CORS pret pour branchement front: juste `.env` a modifier
    3. Passage en Step 4: documentation architecture finale
