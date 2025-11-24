# GoTribe API - Estado de Validación

## ✅ Servicios Funcionando

| Servicio | Endpoint | Status | Respuesta |
|----------|----------|--------|-----------|
| **Auth** | `POST /api/auth/login` | ✅ **OK** | Token JWT válido |
| **Projects** | `GET /api/projects/currencies` | ✅ **OK** | Array con 3 monedas |

---

## 🔄 Próximas Validaciones

### 1. Projects - List All
```bash
GET https://gotribe.co/api/projects
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiI2OTIzYTg3OTIyZmE2Ni4xNjg1MjQ3OCIsImVtYWlsIjoiYWRtaW5AZ290cmliZS5jbyIsInJvbGUiOiJDUkVBVE9SIiwiaWF0IjoxNzYzOTkyMjMxLCJleHAiOjE3NjM5OTU4MzF9.bfGPf0YZVFWp371m5oS--T0K6uIZu93LVrMtnUbA2dk
```

### 2. Projects - Create New
```bash
POST https://gotribe.co/api/projects
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
Content-Type: application/json

{
    "title": "Aventura de Senderismo en la Montaña",
    "description": "Únete a nosotros para una increíble caminata",
    "activityType": "HIKING",
    "startDateTime": "2024-12-15T08:00:00",
    "meetingPoint": "Parque Nacional",
    "price": 50000,
    "currency": "COP",
    "maxGuests": 15
}
```

### 3. Users - Get Profile
```bash
GET https://gotribe.co/api/users/6923a87922fa66.16852478
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

### 4. Participations - Join Project
```bash
POST https://gotribe.co/api/participations/project/{project_id}/join
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

---

## 📊 Resumen de Estado

✅ **2/4 servicios validados**
- Auth Service: ✅ Funcionando
- Projects Service: ✅ Funcionando  
- Users Service: ⏳ Pendiente
- Participations Service: ⏳ Pendiente

---

## 🎯 Token Actual

```
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiI2OTIzYTg3OTIyZmE2Ni4xNjg1MjQ3OCIsImVtYWlsIjoiYWRtaW5AZ290cmliZS5jbyIsInJvbGUiOiJDUkVBVE9SIiwiaWF0IjoxNzYzOTkyMjMxLCJleHAiOjE3NjM5OTU4MzF9.bfGPf0YZVFWp371m5oS--T0K6uIZu93LVrMtnUbA2dk
```

**User ID**: `6923a87922fa66.16852478`  
**Role**: `CREATOR`  
**Expira**: ~45 minutos

---

## ✨ Siguiente Paso

Prueba crear un proyecto con el endpoint `POST /api/projects` usando el token actual.
