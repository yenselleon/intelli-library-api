# Intelli Library API

API REST para gestión de biblioteca digital con autenticación JWT y control de acceso basado en roles.

## Stack Tecnológico

| Tecnología | Versión |
|------------|---------|
| **Laravel** | 5.8 |
| **PHP** | 7.4 |
| **Base de Datos** | SQLite |
| **Autenticación** | JWT (tymon/jwt-auth) |
| **Contenedores** | Docker + Docker Compose |
| **Exportación** | maatwebsite/excel |

## Requisitos Previos

- **Docker** y **Docker Compose** (nada más)

## Instalación Rápida

```bash
git clone <repo-url>
cd intelli-library-api
docker-compose up -d --build
```

> **¡Listo!** El script `startup.sh` se encarga automáticamente de:
> - Crear `.env` desde `.env.example`
> - Generar `APP_KEY` y `JWT_SECRET`
> - Crear la base de datos SQLite
> - Ejecutar migraciones y seeders
>
> La API estará disponible en: **http://localhost:8000**

## Credenciales de Prueba

| Rol | Email | Password |
|-----|-------|----------|
| **Admin** | `admin@intelli-library.com` | `password123` |
| **User** | `test@test.com` | `password` |

## Probar con Postman

Se incluye una colección de Postman lista para importar y probar todos los endpoints.

### Pasos para usar:

1. **Importar la colección** en Postman:
   - Archivo: `docs/postman_collection.json`
   - En Postman: `File > Import > Upload Files`

2. **Ejecutar "Login (Admin)"** para obtener el token automáticamente

3. **Probar cualquier endpoint** - El token se guarda automáticamente en la variable `{{token}}`

### Variables incluidas:

| Variable | Valor |
|----------|-------|
| `base_url` | `http://localhost:8000/api` |
| `token` | Se actualiza automáticamente al hacer login |

### Endpoints disponibles en la colección:

- **Auth**: Login (Admin/User), Me, Logout, Refresh
- **Authors**: CRUD completo (lectura: autenticado, escritura: admin)
- **Books**: CRUD completo (lectura: autenticado, escritura: admin)
- **Reports**: Exportar a Excel (solo admin)
- **Users**: Listar y eliminar (solo admin)

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

## Endpoints de Autores

| Método | Endpoint | Descripción | Acceso |
|--------|----------|-------------|--------|
| GET | `/api/authors` | Listar autores | Autenticado |
| GET | `/api/authors/{id}` | Ver autor | Autenticado |
| POST | `/api/authors` | Crear autor | Admin |
| PUT | `/api/authors/{id}` | Actualizar autor | Admin |
| DELETE | `/api/authors/{id}` | Eliminar autor | Admin |

## Endpoints de Libros

| Método | Endpoint | Descripción | Acceso |
|--------|----------|-------------|--------|
| GET | `/api/books` | Listar libros | Autenticado |
| GET | `/api/books/{id}` | Ver libro | Autenticado |
| POST | `/api/books` | Crear libro | Admin |
| PUT | `/api/books/{id}` | Actualizar libro | Admin |
| DELETE | `/api/books/{id}` | Eliminar libro | Admin |

## Endpoints de Reportes (Solo Admin)

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/reports/export?entity=authors` | Exportar autores a Excel |
| GET | `/api/reports/export?entity=books` | Exportar libros a Excel |

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

## Decisiones Técnicas

- **Event-Driven Architecture**: Al crear/eliminar libros, se disparan eventos que actualizan `books_count` en el autor de forma idempotente.
- **ACL (Access Control List)**: Lectura para usuarios autenticados, escritura solo para administradores.
- **Skinny Controllers**: Validación en FormRequests, lógica de negocio en Models/Services.
- **Factory Pattern**: `ExportFactory` para generar diferentes tipos de exportaciones.

## Estructura del Proyecto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php       # Autenticación JWT
│   │   ├── UserController.php       # CRUD Usuarios
│   │   ├── AuthorController.php     # CRUD Autores
│   │   ├── BookController.php       # CRUD Libros
│   │   └── ReportController.php     # Exportación Excel
│   ├── Middleware/
│   │   └── CheckRole.php            # Verificación de roles
│   └── Requests/
│       ├── LoginRequest.php         # Validación login
│       ├── StoreAuthorRequest.php   # Validación crear autor
│       ├── StoreBookRequest.php     # Validación crear libro
│       └── ExportRequest.php        # Validación exportación
├── Events/
│   ├── BookCreated.php              # Evento al crear libro
│   ├── BookDeleted.php              # Evento al eliminar libro
│   └── BookAuthorChanged.php        # Evento al cambiar autor
├── Listeners/
│   └── RecalculateAuthorBookCount.php  # Actualiza books_count
├── Exports/
│   ├── AuthorsExport.php            # Exportar autores
│   ├── BooksExport.php              # Exportar libros
│   └── ExportFactory.php            # Factory de exports
└── Exceptions/
    └── Handler.php                  # Manejo de errores JSON
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
