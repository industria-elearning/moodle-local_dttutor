# Tutor-IA (local_dttutor)

Plugin de Moodle que proporciona un asistente de IA conversacional integrado en el curso mediante un chat flotante.

## 📋 Tabla de Contenidos

- [Descripción](#descripción)
- [Características](#características)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Uso](#uso)
- [Arquitectura](#arquitectura)
- [API](#api)
- [Desarrollo](#desarrollo)
- [Solución de Problemas](#solución-de-problemas)
- [Licencia](#licencia)

## 📖 Descripción

**Tutor-IA** es un plugin local de Moodle que integra un asistente de inteligencia artificial conversacional directamente en las páginas de cursos. Los estudiantes y profesores pueden interactuar con el tutor mediante un chat flotante que aparece en la esquina de la pantalla.

El plugin utiliza Server-Sent Events (SSE) para proporcionar respuestas en tiempo real del asistente de IA, creando una experiencia fluida y natural de conversación.

## ✨ Características

- **Chat flotante**: Botón flotante con avatar personalizable en esquina inferior (derecha/izquierda)
- **Drawer lateral fijo**: Panel lateral que redistribuye el espacio de la página al abrirse (basado en aiplacement_courseassist)
- **Streaming en tiempo real**: Respuestas del AI se muestran token por token usando SSE
- **Gestión de sesiones**: Cacheo inteligente de sesiones de chat con TTL
- **Multi-contexto**: Detecta automáticamente el rol del usuario (Profesor/Estudiante)
- **Responsive**: Se adapta a diferentes tamaños de pantalla
- **Accesibilidad**: Soporte completo de teclado (Enter para enviar, Escape para cerrar)
- **Integración con Moodle**: Se cierra automáticamente cuando se abre el message drawer de Moodle
- **Footer-popover compatible**: Mueve automáticamente los botones del footer cuando el drawer está abierto
- **Avatares personalizables**: 10 avatares predefinidos para elegir

## 📋 Requisitos

- **Moodle**: 4.5 o superior
- **PHP**: 7.4 o superior
- **API Externa**: Acceso a la API de Tutor-IA (Datacurso)
- **Navegadores soportados**: Chrome, Firefox, Safari, Edge (versiones recientes)

## 🚀 Instalación

### Método 1: Instalación Manual

1. Descargar el plugin y extraer en `local/dttutor/`

```bash
cd /path/to/moodle/local
git clone <repository-url> dttutor
# o extraer el ZIP manualmente
```

2. Ejecutar el upgrade de Moodle:

```bash
php admin/cli/upgrade.php --non-interactive
```

3. Configurar el plugin (ver [Configuración](#configuración))

### Método 2: Instalación vía Docker (moodle-docker)

```bash
cd /path/to/moodle-docker
docker exec moodle-docker-webserver-1 php admin/cli/upgrade.php --non-interactive
```

### Migración desde local_datacurso

Si ya tienes el Tutor-IA en el plugin `local_datacurso`, puedes migrar las configuraciones:

```bash
php local/dttutor/cli/migrate_from_datacurso.php
```

Este script copiará automáticamente:
- URL de API
- Token de autenticación
- Estado habilitado/deshabilitado
- Avatar seleccionado
- Posición del avatar

## ⚙️ Configuración

### Configuración Básica

1. Ir a **Administración del sitio > Plugins > Plugins locales > Tutor IA**

2. **API Settings**:
   - **URL de API**: URL base de la API de Tutor-IA (ej: `https://plugins-ai-dev.datacurso.com`)
   - **Token de autenticación**: Token Bearer para autenticación con la API
   - **Habilitar Chat**: Activar/desactivar el chat globalmente

3. **Avatar Settings**:
   - **Avatar**: Seleccionar uno de los 10 avatares disponibles (01-10)
   - **Posición del avatar**: Esquina inferior derecha o izquierda

### Configuración Avanzada

#### Ajustar tiempo de cacheo de sesiones

Editar `classes/httpclient/tutoria_api.php` línea 84:

```php
// TTL por defecto: 604800 segundos (7 días) - 1 hora de margen
$ttl = ($response['session_ttl_seconds'] ?? 604800) - 3600;
```

#### Cambiar altura del drawer

Editar `styles.css` línea 41:

```css
.tutor-ia-drawer {
    top: 60px;  /* Distancia desde arriba */
    /* ... */
}
```

#### Cambiar ancho del drawer

Editar `styles.css` línea 43:

```css
.tutor-ia-drawer {
    width: 380px;  /* Ancho del drawer */
    /* ... */
}
```

**Importante**: Si cambias el ancho, también debes ajustar las líneas 82-84 para el footer-popover:

```css
body.tutor-ia-drawer-open-right .btn-footer-popover {
    right: calc(380px + 2rem) !important;  /* Cambiar 380px */
}
```

## 🎯 Uso

### Para Estudiantes y Profesores

1. **Abrir el chat**:
   - Click en el botón flotante con avatar en la esquina de la pantalla
   - O usar el atajo de teclado (si está habilitado)

2. **Escribir un mensaje**:
   - Escribir en el campo de texto
   - Presionar `Enter` para enviar (o `Shift+Enter` para nueva línea)
   - Click en el botón de enviar

3. **Ver respuesta**:
   - El indicador de escritura aparecerá
   - La respuesta del AI se mostrará token por token en tiempo real

4. **Cerrar el chat**:
   - Click en el botón X en la esquina superior derecha del drawer
   - Click en el botón flotante nuevamente
   - Presionar `Escape`

### Características del Chat

- **Auto-scroll**: El chat hace scroll automático a medida que llegan tokens
- **Límite de caracteres**: Máximo 4000 caracteres por mensaje
- **Límite de respuesta**: Máximo 10000 caracteres en la respuesta del AI
- **Typing indicator**: Animación de puntos mientras el AI procesa
- **Manejo de errores**: Mensajes claros en caso de error de conexión

## 🏗️ Arquitectura

### Componentes Principales

```
local/dttutor/
├── classes/
│   ├── external/              # Web services
│   │   ├── create_chat_message.php
│   │   └── delete_chat_session.php
│   ├── hook/                  # Hooks de Moodle
│   │   └── chat_hook.php
│   └── httpclient/            # Cliente HTTP API
│       └── tutoria_api.php
├── amd/                       # JavaScript AMD
│   ├── src/tutor_ia_chat.js
│   └── build/tutor_ia_chat.min.js
├── templates/                 # Mustache templates
│   ├── tutor_ia_drawer.mustache
│   └── tutor_ia_toggle.mustache
└── db/                        # Definiciones BD
    ├── services.php           # Web services
    ├── hooks.php              # Hooks
    ├── caches.php             # Cachés
    └── access.php             # Capacidades
```

### Flujo de Datos

```
1. Usuario escribe mensaje
   ↓
2. JavaScript llama a local_dttutor_create_chat_message
   ↓
3. Web service llama a tutoria_api->start_session()
   ↓
4. API externa crea/recupera sesión
   ↓
5. API externa encola mensaje
   ↓
6. Web service retorna stream_url
   ↓
7. JavaScript abre EventSource al stream_url
   ↓
8. Tokens llegan vía SSE (event: token)
   ↓
9. JavaScript actualiza UI en tiempo real
   ↓
10. Evento 'done' finaliza el stream
```

### Cacheo de Sesiones

```php
// Clave de caché
$cachekey = "session_{$courseid}";

// TTL: session_ttl_seconds - 1 hora (margen de seguridad)
$ttl = $response['session_ttl_seconds'] - 3600;

// Validación de sesión
$elapsed = time() - $session['created_at'];
$valid = $elapsed < ($session['session_ttl_seconds'] - 3600);
```

## 🔌 API

### Web Services

#### `local_dttutor_create_chat_message`

Crea un mensaje de chat y retorna la URL de streaming.

**Parámetros**:
- `courseid` (int, required): ID del curso
- `message` (string, required): Texto del mensaje (máx 4000 chars)
- `meta` (string, optional): Metadata en formato JSON (default: '{}')

**Retorna**:
```json
{
    "session_id": "uuid-string",
    "stream_url": "https://api.../chat/stream?session_id=...",
    "ready": true,
    "session_ttl_seconds": 604800
}
```

**Ejemplo de uso (JavaScript)**:
```javascript
Ajax.call([{
    methodname: "local_dttutor_create_chat_message",
    args: {
        courseid: 123,
        message: "¿Cuáles son los temas del curso?",
        meta: JSON.stringify({
            user_role: 'Estudiante',
            timestamp: Math.floor(Date.now() / 1000)
        })
    }
}])[0].then(data => {
    console.log(data.stream_url);
});
```

#### `local_dttutor_delete_chat_session`

Elimina una sesión de chat.

**Parámetros**:
- `sessionid` (string, required): ID de la sesión

**Retorna**:
```json
{
    "success": true,
    "message": "Session deleted"
}
```

### HTTP Client API

#### `tutoria_api::start_session(int $courseid)`

Inicia o recupera una sesión de chat.

**Retorna**: Array con session_id, ready, session_ttl_seconds

#### `tutoria_api::send_message(string $sessionid, string $content, array $meta)`

Envía un mensaje a una sesión existente.

**Retorna**: Array con status de encolado

#### `tutoria_api::get_stream_url(string $sessionid)`

Construye la URL de streaming SSE.

**Retorna**: String con URL completa

#### `tutoria_api::delete_session(string $sessionid)`

Elimina una sesión.

**Retorna**: Array con status de eliminación

### Server-Sent Events (SSE)

El stream SSE envía eventos en el siguiente formato:

```
event: token
data: {"t": "Hola", "content": "Hola"}

event: token
data: {"t": " mundo", "content": " mundo"}

event: done
data: {}
```

**Eventos**:
- `token`: Nuevo token de texto (campo `t` o `content`)
- `done`: Stream completado exitosamente
- `error`: Error en el stream (conexión interrumpida)

## 🛠️ Desarrollo

### Requisitos de Desarrollo

- Node.js >= 22.11.0 (para Grunt)
- Grunt CLI
- PHP >= 7.4
- Acceso a moodle-docker (opcional)

### Compilar JavaScript

```bash
cd /path/to/moodle
grunt amd --root=local/dttutor
```

O watch para desarrollo:

```bash
grunt watch --root=local/dttutor
```

### Testing

#### Validación de Sintaxis PHP

```bash
find local/dttutor -name "*.php" -exec php -l {} \;
```

#### Purgar Cachés

```bash
php admin/cli/purge_caches.php
```

#### Testing Manual

1. Crear un curso de prueba
2. Inscribir un usuario como estudiante
3. Entrar al curso
4. Verificar que aparece el botón flotante
5. Click en el botón para abrir drawer
6. Enviar mensaje de prueba
7. Verificar respuesta en tiempo real

### Estructura de Código

#### JavaScript (AMD Module)

```javascript
// local/dttutor/amd/src/tutor_ia_chat.js
define(['jquery', 'core/ajax', 'core/notification', 'core/pubsub'],
function($, Ajax, Notification, PubSub) {

    class TutorIAChat {
        constructor(root, uniqueId, courseId, userId) {
            // Inicialización
        }

        sendMessage() {
            // Lógica de envío
        }

        startSSE(streamUrl, sendBtn) {
            // EventSource para streaming
        }
    }

    return {
        init: function(root, uniqueId, courseId, userId) {
            return new TutorIAChat(root, uniqueId, courseId, userId);
        }
    };
});
```

#### Hook (PHP)

```php
// local/dttutor/classes/hook/chat_hook.php
class chat_hook {
    public static function before_footer_html_generation(
        before_footer_html_generation $hook
    ): void {
        // Verificar si está habilitado
        // Verificar contexto de curso
        // Renderizar templates
        // Inyectar HTML en footer
    }
}
```

### Añadir Nuevo Avatar

1. Añadir imagen PNG a `pix/avatars/` con nombre `avatar_profesor_XX.png`
2. Actualizar `settings.php` líneas 76-80:

```php
for ($i = 1; $i <= 11; $i++) {  // Cambiar 10 a 11
    $num = str_pad($i, 2, '0', STR_PAD_LEFT);
    $avatarchoices[$num] = get_string('avatar', 'local_dttutor') . ' ' . $i;
}
```

3. Purgar cachés

## 🐛 Solución de Problemas

### El botón flotante no aparece

**Causa**: Plugin deshabilitado o no en contexto de curso

**Solución**:
1. Verificar que el chat está habilitado: `Site administration > Plugins > Local plugins > Tutor IA > Enable Chat`
2. Verificar que estás en una página de curso (no en frontpage)
3. Purgar cachés: `php admin/cli/purge_caches.php`

### Click en avatar no abre drawer

**Causa**: JavaScript no se cargó o hay conflictos

**Solución**:
1. Abrir consola del navegador (F12) y buscar errores JavaScript
2. Verificar que existe `local/dttutor/amd/build/tutor_ia_chat.min.js`
3. Purgar cachés
4. Verificar que no hay otro plugin de chat activo (como `local_datacurso`)

### Error "[Conexión interrumpida]" al final de mensajes

**Causa**: EventSource dispara 'error' al cerrar conexión normalmente

**Solución**: Ya implementado en v1.0.0 - el código detecta el evento 'done' antes de mostrar error

### El drawer no mueve el footer-popover

**Causa**: CSS no cargado o width del drawer no coincide con CSS

**Solución**:
1. Verificar que existe `local/dttutor/styles.css`
2. Verificar ancho del drawer en CSS (línea 43) coincide con calc() en línea 82-84
3. Purgar cachés del navegador (Ctrl+Shift+R)

### Error "Session not ready"

**Causa**: API externa no respondió correctamente

**Solución**:
1. Verificar URL de API en settings
2. Verificar token de autenticación
3. Verificar conectividad a la API externa
4. Revisar logs de Moodle: `php admin/cli/purge_caches.php && tail -f /var/log/moodle/error.log`

### Avatares no se muestran

**Causa**: Archivos PNG no existen o path incorrecto

**Solución**:
1. Verificar que existen archivos en `local/dttutor/pix/avatars/avatar_profesor_01.png` hasta `_10.png`
2. Verificar permisos de lectura
3. Cambiar avatar en settings y guardar

### Conflicto con local_datacurso

**Causa**: Ambos plugins tienen el chat habilitado

**Solución**:
```bash
php -r "define('CLI_SCRIPT', true); require('config.php'); set_config('enablechat', '0', 'local_datacurso');"
php admin/cli/purge_caches.php
```

## 📝 Changelog

Ver [CHANGELOG.md](CHANGELOG.md) para historial de cambios.

## 🤝 Contribuciones

Para contribuir al desarrollo:

1. Fork el repositorio
2. Crear branch de feature (`git checkout -b feature/nueva-caracteristica`)
3. Commit cambios (`git commit -m 'Añadir nueva característica'`)
4. Push al branch (`git push origin feature/nueva-caracteristica`)
5. Crear Pull Request

## 📄 Licencia

Este plugin está licenciado bajo la GNU GPL v3 o posterior.

Copyright (C) 2025 Datacurso

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

## 👥 Créditos

- **Desarrollo**: Datacurso
- **Basado en**: ai/placement/courseassist (patrón de drawer)
- **Licencia**: GNU GPL v3+

## 📧 Soporte

Para soporte y preguntas:
- Email: josue@datacurso.com
- Issues: [GitHub Issues](https://github.com/datacurso/moodle-local_dttutor/issues)

## 🔗 Enlaces

- [Documentación de Moodle](https://docs.moodle.org)
- [Moodle Plugin Directory](https://moodle.org/plugins)
- [API de Tutor-IA](https://plugins-ai-dev.datacurso.com/docs)
