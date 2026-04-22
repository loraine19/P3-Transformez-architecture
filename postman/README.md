# Postman Test Pack (API v1)

This folder now plays two roles:

- executable API tests with Postman/Newman
- minimum API documentation for Step 3

## CLI memo (run this first)

```bash
# 1) Start Laravel app
php artisan migrate
php artisan serve

# 2) Optional: install Newman reporter
npm install -g newman newman-reporter-htmlextra

# 3) Run all API v1 requests and export reports
newman run postman/API-v1.postman_collection.json \
  -e postman/local.postman_environment.json \
  -r cli,json,htmlextra \
  --reporter-json-export postman/reports/postman-report.json \
  --reporter-htmlextra-export postman/reports/postman-report.html
```

## Files in this folder

- `API-v1.postman_collection.json`: full API v1 collection
- `local.postman_environment.json`: local environment (`baseUrl`, `token`, `noteId`, `tagId`, `userEmail`, `userPassword`)
- `reports/`: generated report outputs

## Notes

- Base URL comes from `local.postman_environment.json` (`baseUrl`)
- API prefix used by collection: `/api/v1`
- Collection is aligned with Step 3 API contract (Sanctum Bearer auth + real payloads).

## Authentication flow used by protected routes

1. `Auth - Register` creates a user.
2. `Auth - Login` reads `data.token` from the JSON response.
3. The login test script stores this value in the environment variable `token`.
4. Protected requests send `Authorization: Bearer {{token}}`.

So yes: protected routes are currently tested with a Sanctum bearer token, not with a cookie.

### Where unique email is generated

It is generated in the Postman collection, inside the `Auth - Register` pre-request script:

```javascript
pm.environment.set("userEmail", `test.user.${Date.now()}@example.com`);
pm.environment.set("userPassword", "password123");
```

This avoids `users.email` unique constraint failures when you re-run the full collection.

## JSON response format

All API controllers use the same structure:

```json
{
    "status": "success|error",
    "message": "Human readable message",
    "data": {}
}
```

## Endpoints

### `POST /api/v1/register`

- Access: public
- Goal: create a user and issue a bearer token
- Expected request body:

```json
{
    "name": "Test User",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

- Main responses:
    - `201` success
    - `422` validation failed

### `POST /api/v1/login`

- Access: public
- Goal: authenticate and issue a new bearer token
- Expected request body:

```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

- Main responses:
    - `200` success
    - `401` invalid credentials
    - `422` validation failed

### `POST /api/v1/logout`

- Access: protected
- Header required:

```http
Authorization: Bearer <token>
```

- Goal: revoke the current access token
- Main responses:
    - `200` success
    - `401` missing or invalid token

### `GET /api/v1/notes`

- Access: protected
- Header required:

```http
Authorization: Bearer <token>
```

- Goal: list notes of the authenticated user
- Main responses:
    - `200` success
    - `401` missing or invalid token

### `POST /api/v1/notes`

- Access: protected
- Header required:

```http
Authorization: Bearer <token>
```

- Expected request body:

```json
{
    "text": "Created from Postman collection",
    "tag_id": 1
}
```

- Main responses:
    - `201` success
    - `401` missing or invalid token
    - `422` validation failed

### `DELETE /api/v1/notes/{noteId}`

- Access: protected
- Header required:

```http
Authorization: Bearer <token>
```

- Goal: delete one note owned by the authenticated user
- Main responses:
    - `200` success
    - `401` missing or invalid token
    - `403` note belongs to another user
    - `404` note not found

### `GET /api/v1/tags`

- Access: protected
- Header required:

```http
Authorization: Bearer <token>
```

- Goal: list tags of the authenticated user
- Main responses:
    - `200` success
    - `401` missing or invalid token

### `POST /api/v1/tags`

- Access: protected
- Header required:

```http
Authorization: Bearer <token>
```

- Expected request body:

```json
{
    "name": "postman-tag"
}
```

- Main responses:
    - `201` success
    - `401` missing or invalid token
    - `422` validation failed
