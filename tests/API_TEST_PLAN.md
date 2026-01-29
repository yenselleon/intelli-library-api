# Plan de Pruebas - Módulo Auth & Users

## Configuración Previa

### Credenciales de Prueba
| Usuario | Email | Password | Role |
|---------|-------|----------|------|
| Admin | admin@intelli-library.com | password123 | admin |
| User | test@test.com | password | user |

### Headers Requeridos
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}  // Solo para rutas protegidas
```

### Limpiar Rate Limiting
```bash
docker-compose exec app php artisan cache:clear
```

---

## 1. POST /api/auth/register

### 1.1 ✅ Camino Feliz - Registro exitoso
**Request:**
```json
POST /api/auth/register
{
    "name": "New User",
    "email": "newuser@test.com",
    "password": "secret123"
}
```
**Expected Response:** `201 Created`
```json
{
    "success": true,
    "data": {
        "id": 3,
        "name": "New User",
        "email": "newuser@test.com",
        "role": "user"
    },
    "message": "User registered successfully"
}
```

### 1.2 ❌ Error - Email duplicado
**Request:**
```json
POST /api/auth/register
{
    "name": "Duplicate",
    "email": "admin@intelli-library.com",
    "password": "secret123"
}
```
**Expected Response:** `422 Unprocessable Entity`
```json
{
    "success": false,
    "data": {
        "email": ["The email has already been taken."]
    },
    "message": "Validation failed"
}
```

### 1.3 ❌ Error - Campos faltantes
**Request:**
```json
POST /api/auth/register
{
    "name": "Test"
}
```
**Expected Response:** `422 Unprocessable Entity`
```json
{
    "success": false,
    "data": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    },
    "message": "Validation failed"
}
```

### 1.4 ❌ Error - Password muy corto
**Request:**
```json
POST /api/auth/register
{
    "name": "Test",
    "email": "short@test.com",
    "password": "123"
}
```
**Expected Response:** `422 Unprocessable Entity`
```json
{
    "success": false,
    "data": {
        "password": ["The password must be at least 6 characters."]
    },
    "message": "Validation failed"
}
```

### 1.5 ❌ Error - Rate Limiting (4+ intentos/minuto)
**Expected Response:** `429 Too Many Requests`
```json
{
    "success": false,
    "data": null,
    "message": "Too Many Attempts."
}
```

---

## 2. POST /api/auth/login

### 2.1 ✅ Camino Feliz - Login exitoso
**Request:**
```json
POST /api/auth/login
{
    "email": "admin@intelli-library.com",
    "password": "password123"
}
```
**Expected Response:** `200 OK`
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

### 2.2 ❌ Error - Credenciales inválidas
**Request:**
```json
POST /api/auth/login
{
    "email": "admin@intelli-library.com",
    "password": "wrongpassword"
}
```
**Expected Response:** `401 Unauthorized`
```json
{
    "success": false,
    "data": null,
    "message": "Invalid credentials"
}
```

### 2.3 ❌ Error - Usuario no existe
**Request:**
```json
POST /api/auth/login
{
    "email": "noexiste@test.com",
    "password": "password123"
}
```
**Expected Response:** `401 Unauthorized`
```json
{
    "success": false,
    "data": null,
    "message": "Invalid credentials"
}
```

### 2.4 ❌ Error - Campos faltantes
**Request:**
```json
POST /api/auth/login
{}
```
**Expected Response:** `422 Unprocessable Entity`
```json
{
    "success": false,
    "data": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    },
    "message": "Validation failed"
}
```

### 2.5 ❌ Error - Rate Limiting (6+ intentos/minuto)
**Expected Response:** `429 Too Many Requests`

---

## 3. GET /api/auth/me

### 3.1 ✅ Camino Feliz - Obtener usuario autenticado
**Request:**
```
GET /api/auth/me
Authorization: Bearer {valid_token}
```
**Expected Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@intelli-library.com",
        "role": "admin"
    },
    "message": "User retrieved successfully"
}
```

### 3.2 ❌ Error - Sin token
**Request:**
```
GET /api/auth/me
```
**Expected Response:** `401 Unauthorized`
```json
{
    "success": false,
    "data": null,
    "message": "Unauthenticated"
}
```

### 3.3 ❌ Error - Token inválido
**Request:**
```
GET /api/auth/me
Authorization: Bearer invalid_token_here
```
**Expected Response:** `401 Unauthorized`
```json
{
    "success": false,
    "data": null,
    "message": "Token is invalid"
}
```

### 3.4 ❌ Error - Token expirado
**Expected Response:** `401 Unauthorized`
```json
{
    "success": false,
    "data": null,
    "message": "Token has expired"
}
```

---

## 4. POST /api/auth/logout

### 4.1 ✅ Camino Feliz - Logout exitoso
**Request:**
```
POST /api/auth/logout
Authorization: Bearer {valid_token}
```
**Expected Response:** `200 OK`
```json
{
    "success": true,
    "data": null,
    "message": "Successfully logged out"
}
```

### 4.2 ❌ Error - Sin token
**Expected Response:** `401 Unauthorized`
```json
{
    "success": false,
    "data": null,
    "message": "Unauthenticated"
}
```

---

## 5. POST /api/auth/refresh

### 5.1 ✅ Camino Feliz - Refresh token
**Request:**
```
POST /api/auth/refresh
Authorization: Bearer {valid_token}
```
**Expected Response:** `200 OK`
```json
{
    "success": true,
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 3600
    },
    "message": "Token refreshed successfully"
}
```

### 5.2 ❌ Error - Token ya invalidado (después de logout)
**Expected Response:** `401 Unauthorized`
```json
{
    "success": false,
    "data": null,
    "message": "Token is invalid"
}
```

---

## 6. GET /api/users (Solo Admin)

### 6.1 ✅ Camino Feliz - Admin lista usuarios
**Request:**
```
GET /api/users
Authorization: Bearer {admin_token}
```
**Expected Response:** `200 OK`
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Admin User",
            "email": "admin@intelli-library.com",
            "role": "admin"
        },
        {
            "id": 2,
            "name": "Test User",
            "email": "test@test.com",
            "role": "user"
        }
    ],
    "message": "Users retrieved successfully"
}
```

### 6.2 ❌ Error - Usuario normal intenta listar
**Request:**
```
GET /api/users
Authorization: Bearer {user_token}
```
**Expected Response:** `403 Forbidden`
```json
{
    "success": false,
    "data": null,
    "message": "Forbidden"
}
```

### 6.3 ❌ Error - Sin token
**Expected Response:** `401 Unauthorized`
```json
{
    "success": false,
    "data": null,
    "message": "Unauthenticated"
}
```

---

## 7. DELETE /api/users/{id} (Solo Admin)

### 7.1 ✅ Camino Feliz - Admin elimina usuario
**Request:**
```
DELETE /api/users/2
Authorization: Bearer {admin_token}
```
**Expected Response:** `200 OK`
```json
{
    "success": true,
    "data": null,
    "message": "User deleted successfully"
}
```

### 7.2 ❌ Error - Admin intenta auto-eliminarse
**Request:**
```
DELETE /api/users/1
Authorization: Bearer {admin_token_id_1}
```
**Expected Response:** `403 Forbidden`
```json
{
    "success": false,
    "data": null,
    "message": "Cannot delete yourself"
}
```

### 7.3 ❌ Error - Usuario normal intenta eliminar
**Request:**
```
DELETE /api/users/1
Authorization: Bearer {user_token}
```
**Expected Response:** `403 Forbidden`
```json
{
    "success": false,
    "data": null,
    "message": "Forbidden"
}
```

### 7.4 ❌ Error - Usuario no existe
**Request:**
```
DELETE /api/users/999
Authorization: Bearer {admin_token}
```
**Expected Response:** `404 Not Found`
```json
{
    "success": false,
    "data": null,
    "message": "Resource not found"
}
```

### 7.5 ❌ Error - Sin token
**Expected Response:** `401 Unauthorized`

---

## 8. Pruebas de Endpoints No Existentes

### 8.1 ❌ Ruta no existe
**Request:**
```
GET /api/nonexistent
```
**Expected Response:** `404 Not Found`
```json
{
    "success": false,
    "data": null,
    "message": "Endpoint not found"
}
```

### 8.2 ❌ Método no permitido
**Request:**
```
PUT /api/auth/login
```
**Expected Response:** `405 Method Not Allowed`
```json
{
    "success": false,
    "data": null,
    "message": "Method not allowed for this endpoint"
}
```

---

## Resumen de Códigos HTTP

| Código | Significado | Cuándo se usa |
|--------|-------------|---------------|
| 200 | OK | Operación exitosa (GET, PUT, DELETE) |
| 201 | Created | Recurso creado (POST register) |
| 401 | Unauthorized | Sin token, token inválido/expirado, credenciales incorrectas |
| 403 | Forbidden | Sin permisos (user accede a admin route, auto-delete) |
| 404 | Not Found | Recurso o endpoint no existe |
| 405 | Method Not Allowed | Verbo HTTP incorrecto |
| 422 | Unprocessable Entity | Errores de validación |
| 429 | Too Many Requests | Rate limiting excedido |
| 500 | Internal Server Error | Error del servidor |
