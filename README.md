# Intelli Library API

API REST para gestión de biblioteca desarrollada con Laravel 5.8.

## Tecnologías

- **Framework:** Laravel 5.8
- **PHP:** 7.4
- **Base de Datos:** SQLite (desarrollo) / PostgreSQL (producción)
- **Autenticación:** JWT (tymon/jwt-auth)
- **Contenedores:** Docker + Docker Compose

## Instalación Rápida (Docker)

```bash
# 1. Clonar el repositorio
git clone <repo-url>
cd intelli-library-api

# 2. Instalar dependencias de Composer
docker run --rm -v $(pwd):/var/www -w /var/www composer:2.2 install --ignore-platform-reqs

# 3. Iniciar la aplicación
docker-compose up -d

# 4. ¡Listo! La API estará disponible en:
# http://localhost:8000
```

> **Nota:** El contenedor automáticamente:
> - Crea el archivo `.env` desde `.env.example`
> - Genera `APP_KEY` y `JWT_SECRET`
> - Crea la base de datos SQLite
> - Ejecuta las migraciones y seeders

## Credenciales de Prueba

| Usuario | Email | Password | Role |
|---------|-------|----------|------|
| Admin | admin@intelli-library.com | password123 | admin |
| User | test@test.com | password | user |

## Endpoints de Autenticación

| Método | Endpoint | Descripción | Acceso |
|--------|----------|-------------|--------|
| POST | `/api/auth/login` | Iniciar sesión | Público |
| POST | `/api/auth/register` | Registrar usuario | Público |
| GET | `/api/auth/me` | Usuario autenticado | Autenticado |
| POST | `/api/auth/logout` | Cerrar sesión | Autenticado |
| POST | `/api/auth/refresh` | Renovar token | Autenticado |

## Endpoints de Usuarios (Solo Admin)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/users` | Listar usuarios |
| DELETE | `/api/users/{id}` | Eliminar usuario |

## Ejemplo de Uso

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@intelli-library.com","password":"password123"}'
```

### Respuesta
```json
{
  "success": true,
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600
  },
  "message": "Login successful"
}
```

### Usar Token
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer {token}"
```

## Comandos Útiles

```bash
# Ver logs del contenedor
docker-compose logs -f app

# Ejecutar comandos artisan
docker-compose exec app php artisan migrate:status

# Limpiar cache
docker-compose exec app php artisan cache:clear

# Reiniciar base de datos
docker-compose exec app php artisan migrate:fresh --seed
```

## Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php      # Autenticación JWT
│   │   └── UserController.php      # CRUD Usuarios
│   ├── Middleware/
│   │   └── CheckRole.php           # Verificación de roles
│   └── Requests/
│       ├── LoginRequest.php        # Validación login
│       └── RegisterUserRequest.php # Validación registro
├── Exceptions/
│   └── Handler.php                 # Manejo de errores JSON
└── User.php                        # Modelo con roles
```

## Códigos de Respuesta HTTP

| Código | Significado |
|--------|-------------|
| 200 | Operación exitosa |
| 201 | Recurso creado |
| 401 | No autenticado |
| 403 | Sin permisos |
| 404 | No encontrado |
| 422 | Error de validación |
| 429 | Demasiadas peticiones |
| 500 | Error del servidor |

## Licencia

MIT
