# 🚀 TREKLY Auth  - Microservicio de Autenticación

## 📋 Descripción

Microservicio simple de autenticación para **TREKLY** con endpoints completos de autenticación y autorización. Esta es una versión **** limpia y funcional.

## ✨ Características

- ✅ **Spring Boot 3.2.0** con Java 21
- 🔐 **8 Endpoints de Autenticación** completos
- 📚 **Documentación Swagger/OpenAPI** integrada
- 🏥 **Health Check** y actuator endpoints
- 🎯 **Respuestas JSON Mock** realistas
- 🧹 **Arquitectura Simple** sin complejidades innecesarias

## 🚀 Inicio Rápido

### 1. Compilar
```bash
./mvnw clean package -DskipTests
```

### 2. Ejecutar
```bash
java -jar target/auth-1.0.0.jar
```

### 3. Probar
- **Swagger UI**: http://localhost:8080/swagger-ui/index.html
- **Status**: http://localhost:8080/auth/
- **Health Check**: http://localhost:8080/actuator/health

## 🔗 Endpoints Disponibles

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/auth/` | Estado del servicio |
| `POST` | `/auth/register` | Registrar usuario |
| `POST` | `/auth/login` | Iniciar sesión |
| `POST` | `/auth/refresh` | Renovar token |
| `POST` | `/auth/verify-email` | Verificar email |
| `GET` | `/auth/.well-known/jwks.json` | Claves públicas JWT |
| `POST` | `/auth/forgot-password` | Solicitar reset password |
| `POST` | `/auth/reset-password` | Resetear contraseña |
| `POST` | `/auth/logout` | Cerrar sesión |

## 📁 Estructura del Proyecto

```
auth/
├── pom.xml                                    # Configuración Maven
├── src/main/java/com/trekly/auth/
│   ├── AuthApplication.java               # Aplicación principal
│   └── controller/
│       └── AuthController.java                # Endpoints REST
├── src/main/resources/
│   └── application.yml                        # Configuración
└── target/
    └── auth-1.0.0.jar                    # JAR ejecutable
```

## 🛠️ Tecnologías

- **Java 21** (Eclipse Adoptium JDK)
- **Spring Boot 3.2.0**
- **Spring Web**
- **SpringDoc OpenAPI**
- **Spring Actuator**
- **Maven 3.9.6**

## ✅ Estado

- ✅ **Compilación**: OK
- ✅ **Ejecución**: OK  
- ✅ **Endpoints**: Funcionando
- ✅ **Documentación**: Disponible
- ✅ **Tests**: N/A (endpoints mock)

---

**TREKLY Auth ** - Solución limpia y funcional 🎯