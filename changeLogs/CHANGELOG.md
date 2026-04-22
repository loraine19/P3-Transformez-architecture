# **CHANGELOG \- 1**

## **Commit :**

feat(api): setup v1 API skeleton with controllers, services and tag ownership

1. ### **Recap de travail effectué**
    1. Structure API posée
        1. Dossier controllers API cree
        2. Dossier services cree
        3. Fichier routes API cree
    2. Bootstrap API active
        1. Chargement de routes/api.php ajoute dans bootstrap/app.php
    3. Endpoints v1 cables
        1. Auth : register, login, logout
        2. Notes : list, create, delete
        3. Tags : list, create
    4. Reponses JSON uniformisées
        1. BaseApiController ajouté
        2. Format commun : status, message, data
    5. Securite tags avancée
        1. Relation user-tag ajoutée (models)
            1. Migration user_id ajoutée sur tags
            2. TagService filtre/create par user
            3. TagController prépare pour contrôle utilisateur
    6. Nettoyage
        1. Import inutile retire dans routes/web.php
        2. Commentaires DONE ajoutés dans les fichiers modifiés/créés
    7. Préparation des tests Postman
        1. Dossier postman créé
        2. Collection API-v1 prepares
        3. Environment local prepare
        4. Memo CLI ajoute pour générer un rapport Newman

2. ### **Fichiers créés**
    1. .../routes/api.php
    2. .../app/Http/Controllers/API/BaseApiController.php
    3. .../app/Http/Controllers/API/AuthController.php
    4. .../app/Http/Controllers/API/NoteController.php
    5. .../app/Http/Controllers/API/TagController.php
    6. .../app/Services/AuthService.php
    7. .../app/Services/NoteService.php
    8. .../app/Services/TagService.php
    9. .../database/migrations/2026_04_21_120000_add_user_id_to_tags_table.php
    10. .../postman/README.md
    11. .../postman/API-v1.postman_collection.json
    12. .../postman/local.postman_environment.json

3. ### **Fichiers modifiés**
    1. .../bootstrap/app.php
    2. .../routes/web.php
    3. .../app/Models/Tag.php
    4. .../app/Models/User.php
    5. .../database/factories/TagFactory.php

4. ### **Tests et vérification**
    1. php \-l OK sur fichiers modifiés/créés
    2. route:list OK sur api/v1
    3. Postman OK (stubs)
    4. JSON Postman valide

5. ### **Note de progression**
    1. Auth et Notes sont encore en logique service squelette partiel.
    2. Le check Unauthorized dans TagController peut être réactivé après les tests de progression.
