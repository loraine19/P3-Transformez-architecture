# Postman Test Pack (API v1)

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
- `local.postman_environment.json`: local environment (`baseUrl`, `token`, `noteId`)
- `reports/`: generated report outputs

## Notes

- Base URL used by default: `http://127.0.0.1:8000`
- API prefix used by collection: `/api/v1`
- Current backend is partially in stub mode, so some requests are only structure checks.
