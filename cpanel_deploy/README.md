# GoTribe - Backend API

Este paquete contiene los 4 microservicios del backend de GoTribe listos para desplegar en cPanel.

## Contenido

- `/api/auth` - Servicio de autenticación
- `/api/users` - Servicio de perfiles de usuario
- `/api/projects` - Servicio de proyectos/aventuras
- `/api/participations` - Servicio de participación en eventos

## Instrucciones de Despliegue

1. **Descomprime este archivo en `public_html/`** de tu cPanel
2. **Inicializa las bases de datos** usando phpMyAdmin (ver init.sql en cada carpeta)
3. **Ajusta las URLs** en `/api/participations/.env`:
   - PROJECT_SERVICE_URL=https://tudominio.com/api/projects
   - AUTH_SERVICE_URL=https://tudominio.com/api/auth
   - CORS_ALLOWED_ORIGIN=https://tudominio.com

4. **Verifica PHP 8.2** en MultiPHP Manager de cPanel

## Bases de Datos Configuradas

- tribew_auth
- tribew_users
- tribew_projects
- tribew_participations

Usuario: tribew_eli4as
Contraseña: 8TK4Nqp8d9SX4uxa

## Frontend

El frontend Angular debe compilarse por separado con:
```
ng build --configuration production
```
Y subirse a la raíz de `public_html/`

## Soporte

Ver deployment_cpanel.md para más detalles.
