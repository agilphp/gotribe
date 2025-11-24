# GoTribe API - Pruebas de Validación

## ✅ Endpoints Probados

### 1. Auth Service - Login
**Status**: ✅ FUNCIONANDO

```bash
POST https://gotribe.co/api/auth/login
{
    "email": "admin@gotribe.co",
    "password": "Ea94490X*ASD2025*"
}
```

**Respuesta**:
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "role": "CREATOR",
    "userId": "6923a87922fa66.16852478"
}
```

---

## 🔄 Próximas Pruebas

### 2. Projects Service - Get Currencies
```bash
GET https://gotribe.co/api/projects/currencies
```

### 3. Projects Service - List Projects
```bash
GET https://gotribe.co/api/projects
Headers:
  Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

### 4. Projects Service - Create Project
```bash
POST https://gotribe.co/api/projects
Headers:
  Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
  Content-Type: application/json

Body:
{
    "title": "Aventura de Senderismo",
    "description": "Caminata por las montañas",
    "activityType": "HIKING",
    "startDateTime": "2024-12-15T08:00:00",
    "meetingPoint": "Parque Nacional",
    "price": 50000,
    "currency": "COP",
    "maxGuests": 15,
    "imageUrl": "https://example.com/hiking.jpg"
}
```

### 5. Users Service - Get User
```bash
GET https://gotribe.co/api/users/6923a87922fa66.16852478
Headers:
  Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

---

## 📝 Instrucciones para Postman

1. **Importa la colección** `GoTribe_API.postman_collection.json`
2. **Importa el environment** `GoTribe_Production.postman_environment.json`
3. **Actualiza el token** en el environment:
   - Variable: `auth_token`
   - Valor: `eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiI2OTIzYTg3OTIyZmE2Ni4xNjg1MjQ3OCIsImVtYWlsIjoiYWRtaW5AZ290cmliZS5jbyIsInJvbGUiOiJDUkVBVE9SIiwiaWF0IjoxNzYzOTkyMjMxLCJleHAiOjE3NjM5OTU4MzF9.bfGPf0YZVFWp371m5oS--T0K6uIZu93LVrMtnUbA2dk`
   - Variable: `user_id`
   - Valor: `6923a87922fa66.16852478`

4. **Ejecuta las pruebas** en este orden:
   - ✅ Login (ya probado)
   - Get Currencies
   - List Projects
   - Create Project
   - Get User

---

## 🎯 Estado Actual

| Servicio | Endpoint | Status |
|----------|----------|--------|
| Auth | POST /api/auth/login | ✅ OK |
| Auth | POST /api/auth/register | ⏳ Pendiente |
| Projects | GET /api/projects/currencies | ⏳ Pendiente |
| Projects | GET /api/projects | ⏳ Pendiente |
| Projects | POST /api/projects | ⏳ Pendiente |
| Users | GET /api/users/{id} | ⏳ Pendiente |
| Participations | POST /api/participations/project/{id}/join | ⏳ Pendiente |

---

## 🔑 Token Actual

```
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiI2OTIzYTg3OTIyZmE2Ni4xNjg1MjQ3OCIsImVtYWlsIjoiYWRtaW5AZ290cmliZS5jbyIsInJvbGUiOiJDUkVBVE9SIiwiaWF0IjoxNzYzOTkyMjMxLCJleHAiOjE3NjM5OTU4MzF9.bfGPf0YZVFWp371m5oS--T0K6uIZu93LVrMtnUbA2dk
```

**Expira**: 2025-11-24 09:50:31 UTC (en ~1 hora)

---

## ✨ Siguiente Paso

Prueba los siguientes endpoints en Postman y comparte los resultados para validar que todo funciona correctamente.
