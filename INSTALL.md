# Instalación del Plugin Tutor-IA (local_dttutor)

Guía detallada de instalación para el plugin Tutor-IA de Moodle.

## Tabla de Contenidos

- [Requisitos Previos](#requisitos-previos)
- [Instalación Nueva](#instalación-nueva)
- [Migración desde local_datacurso](#migración-desde-local_datacurso)
- [Configuración Post-Instalación](#configuración-post-instalación)
- [Verificación](#verificación)
- [Solución de Problemas](#solución-de-problemas)

## Requisitos Previos

### Sistema

- **Moodle**: Versión 4.5 o superior
- **PHP**: Versión 7.4 o superior con extensiones:
  - curl
  - json
  - mbstring
- **Servidor Web**: Apache 2.4+ o Nginx 1.18+
- **Base de Datos**: MySQL 5.7+, PostgreSQL 10+, o MariaDB 10.2+

### API Externa

- Acceso a la API de Tutor-IA (Datacurso)
- Token de autenticación válido
- URL base de la API

### Permisos

- Acceso de escritura al directorio `local/` de Moodle
- Permisos para ejecutar scripts CLI de Moodle
- Acceso a la base de datos de Moodle

## Instalación Nueva

### Paso 1: Descargar el Plugin

**Opción A: Git**
```bash
cd /path/to/moodle/local
git clone https://github.com/datacurso/moodle-local_dttutor.git dttutor
```

**Opción B: ZIP**
```bash
cd /path/to/moodle/local
wget https://github.com/datacurso/moodle-local_dttutor/archive/refs/heads/main.zip
unzip main.zip
mv moodle-local_dttutor-main dttutor
```

**Opción C: Docker (moodle-docker)**
```bash
# Desde el host
cd /path/to/moodle-docker/moodle_405/local
git clone https://github.com/datacurso/moodle-local_dttutor.git dttutor
```

### Paso 2: Verificar Estructura de Archivos

Asegúrate de que la estructura es correcta:

```bash
ls -la local/dttutor/
# Debe mostrar:
# - version.php
# - settings.php
# - classes/
# - db/
# - lang/
# - templates/
# - amd/
# - pix/
# etc.
```

### Paso 3: Ejecutar Upgrade de Moodle

**En servidor normal:**
```bash
cd /path/to/moodle
php admin/cli/upgrade.php --non-interactive
```

**En Docker:**
```bash
docker exec moodle-docker-webserver-1 php admin/cli/upgrade.php --non-interactive
```

Deberías ver:
```
-->local_dttutor
++ Success (0.24 seconds) ++
```

### Paso 4: Verificar Instalación

```bash
# Verificar que el plugin está registrado
php -r "
define('CLI_SCRIPT', true);
require('config.php');
\$plugin = \$DB->get_record('config_plugins', ['plugin' => 'local_dttutor', 'name' => 'version']);
echo 'Version: ' . \$plugin->value . PHP_EOL;
"
```

## Migración desde local_datacurso

Si ya tienes el Tutor-IA funcionando en `local_datacurso`, sigue estos pasos:

### Paso 1: Deshabilitar Chat en local_datacurso

**Opción A: Via Web**
1. Ir a **Administración del sitio > Plugins > Plugins locales > DataCurso**
2. Desmarcar **Habilitar Chat**
3. Guardar cambios

**Opción B: Via CLI**
```bash
php -r "
define('CLI_SCRIPT', true);
require('config.php');
set_config('enablechat', '0', 'local_datacurso');
echo 'Chat deshabilitado en local_datacurso' . PHP_EOL;
"
```

**Opción C: Via Docker**
```bash
docker exec moodle-docker-webserver-1 php -r "
define('CLI_SCRIPT', true);
require('config.php');
set_config('enablechat', '0', 'local_datacurso');
echo 'Chat deshabilitado en local_datacurso' . PHP_EOL;
"
```

### Paso 2: Instalar local_dttutor

Seguir los pasos de [Instalación Nueva](#instalación-nueva) (Pasos 1-3)

### Paso 3: Migrar Configuraciones

El plugin incluye un script de migración que copia automáticamente todas las configuraciones:

```bash
cd /path/to/moodle
php local/dttutor/cli/migrate_from_datacurso.php
```

El script migrará:
- URL de API (`tutoraiapiurl` → `apiurl`)
- Token de autenticación (`tutoraitoken` → `apitoken`)
- Estado habilitado/deshabilitado (`enablechat` → `enabled`)
- Avatar seleccionado (`tutoria_avatar` → `avatar`)
- Posición del avatar (`tutoria_avatar_position` → `avatar_position`)

Salida esperada:
```
Migrating Tutor-IA settings from local_datacurso to local_dttutor...

✓ Migrated tutoraiapiurl => apiurl: https://plugins-ai-dev.datacurso.com
✓ Migrated tutoraitoken => apitoken: [token]
✓ Migrated enablechat => enabled: 1
✓ Migrated tutoria_avatar => avatar: 03
✓ Migrated tutoria_avatar_position => avatar_position: right

Migration completed!
Migrated: 5 settings
Skipped: 0 settings
```

### Paso 4: Purgar Cachés

```bash
php admin/cli/purge_caches.php
```

O en Docker:
```bash
docker exec moodle-docker-webserver-1 php admin/cli/purge_caches.php
```

### Paso 5: Verificar Migración

Verificar que las configuraciones se migraron correctamente:

```bash
php -r "
define('CLI_SCRIPT', true);
require('config.php');
echo 'API URL: ' . get_config('local_dttutor', 'apiurl') . PHP_EOL;
echo 'Enabled: ' . get_config('local_dttutor', 'enabled') . PHP_EOL;
echo 'Avatar: ' . get_config('local_dttutor', 'avatar') . PHP_EOL;
echo 'Position: ' . get_config('local_dttutor', 'avatar_position') . PHP_EOL;
"
```

## Configuración Post-Instalación

### Configuración Vía Web

1. Ir a **Administración del sitio > Plugins > Plugins locales > Tutor IA**

2. Configurar **API Settings**:
   - **URL de API Tutor-IA**: Ingresar la URL base (ej: `https://plugins-ai-dev.datacurso.com`)
   - **Token de autenticación**: Ingresar el token Bearer
   - **Habilitar Chat**: Marcar la casilla

3. Configurar **Avatar Settings**:
   - **Avatar del Tutor-IA**: Seleccionar un avatar (Avatar 1-10)
   - **Posición del avatar**: Seleccionar esquina (Derecha/Izquierda)

4. Hacer click en **Guardar cambios**

### Configuración Vía CLI

```bash
php -r "
define('CLI_SCRIPT', true);
require('config.php');

// API Settings
set_config('apiurl', 'https://plugins-ai-dev.datacurso.com', 'local_dttutor');
set_config('apitoken', 'TU_TOKEN_AQUI', 'local_dttutor');
set_config('enabled', '1', 'local_dttutor');

// Avatar Settings
set_config('avatar', '01', 'local_dttutor');
set_config('avatar_position', 'right', 'local_dttutor');

echo 'Configuración completada' . PHP_EOL;
"
```

### Purgar Cachés Después de Configurar

**Siempre** purgar cachés después de cambiar configuraciones:

```bash
php admin/cli/purge_caches.php
```

## Verificación

### 1. Verificar Instalación del Plugin

**Via Web:**
1. Ir a **Administración del sitio > Plugins > Descripción de plugins**
2. Buscar "Tutor IA" o "local_dttutor"
3. Verificar que aparece con versión 1.0.0

**Via CLI:**
```bash
php -r "
define('CLI_SCRIPT', true);
require('config.php');
\$version = \$DB->get_field('config_plugins', 'value', ['plugin' => 'local_dttutor', 'name' => 'version']);
echo 'Plugin version: ' . \$version . PHP_EOL;
"
```

### 2. Verificar Web Services

```bash
php -r "
define('CLI_SCRIPT', true);
require('config.php');
\$services = \$DB->get_records('external_functions', ['component' => 'local_dttutor']);
foreach (\$services as \$service) {
    echo \$service->name . ' => ' . \$service->classname . PHP_EOL;
}
"
```

Debe mostrar:
```
local_dttutor_create_chat_message => local_dttutor\external\create_chat_message
local_dttutor_delete_chat_session => local_dttutor\external\delete_chat_session
```

### 3. Verificar Hooks

```bash
php -r "
define('CLI_SCRIPT', true);
require('config.php');
\$hooks = \core\hook\manager::get_hooks_with_callbacks();
foreach (\$hooks as \$hook => \$callbacks) {
    if (strpos(\$hook, 'before_footer') !== false) {
        echo 'Hook: ' . \$hook . PHP_EOL;
        foreach (\$callbacks as \$callback) {
            if (strpos(\$callback['callback'], 'dttutor') !== false) {
                echo '  - ' . \$callback['callback'] . PHP_EOL;
            }
        }
    }
}
"
```

### 4. Verificar Archivos AMD

```bash
ls -lh local/dttutor/amd/build/
# Debe mostrar:
# tutor_ia_chat.min.js
# tutor_ia_chat.min.js.map
```

### 5. Verificar Avatares

```bash
ls -lh local/dttutor/pix/avatars/
# Debe mostrar 9 archivos PNG (avatar_profesor_01.png hasta _10.png)
```

### 6. Prueba Funcional

1. Crear un curso de prueba o usar uno existente
2. Inscribir un usuario como estudiante
3. Iniciar sesión como ese estudiante
4. Entrar al curso
5. Verificar que aparece el botón flotante con avatar en la esquina inferior
6. Hacer click en el botón → el drawer debe abrirse
7. Escribir un mensaje → debe enviarse y recibir respuesta
8. Verificar que no aparece "[Conexión interrumpida]" al final
9. Presionar Escape → drawer se debe cerrar

## Solución de Problemas

### Problema: Plugin no se instala

**Síntoma**: Error al ejecutar upgrade.php

**Soluciones**:

1. Verificar permisos:
```bash
chmod -R 755 local/dttutor
```

2. Verificar sintaxis PHP:
```bash
find local/dttutor -name "*.php" -exec php -l {} \;
```

3. Verificar estructura de archivos:
```bash
ls local/dttutor/version.php
# Debe existir
```

4. Verificar logs de Moodle:
```bash
tail -f /var/log/apache2/error.log  # o ruta de logs de tu servidor
```

### Problema: Configuraciones no se migran

**Síntoma**: Script de migración muestra "Skipped: 5 settings"

**Solución**: Verificar que local_datacurso tiene las configuraciones:

```bash
php -r "
define('CLI_SCRIPT', true);
require('config.php');
\$configs = ['tutoraiapiurl', 'tutoraitoken', 'enablechat', 'tutoria_avatar', 'tutoria_avatar_position'];
foreach (\$configs as \$config) {
    \$value = get_config('local_datacurso', \$config);
    echo \$config . ': ' . (\$value !== false ? \$value : 'NOT SET') . PHP_EOL;
}
"
```

Si no están configuradas, configurar manualmente local_dttutor.

### Problema: Botón flotante no aparece

**Síntoma**: No se ve el avatar flotante en páginas de curso

**Soluciones**:

1. Verificar que el chat está habilitado:
```bash
php -r "
define('CLI_SCRIPT', true);
require('config.php');
echo 'Enabled: ' . get_config('local_dttutor', 'enabled') . PHP_EOL;
"
```

2. Verificar que estás en un curso (no en frontpage)

3. Purgar cachés:
```bash
php admin/cli/purge_caches.php
```

4. Purgar caché del navegador (Ctrl+Shift+R)

5. Verificar en consola del navegador (F12) si hay errores JavaScript

### Problema: Error al hacer click en avatar

**Síntoma**: Click no abre el drawer

**Soluciones**:

1. Verificar que no hay conflicto con local_datacurso:
```bash
php -r "
define('CLI_SCRIPT', true);
require('config.php');
echo 'datacurso chat: ' . get_config('local_datacurso', 'enablechat') . PHP_EOL;
echo 'dttutor enabled: ' . get_config('local_dttutor', 'enabled') . PHP_EOL;
"
```

Si ambos están en '1', deshabilitar datacurso:
```bash
php -r "
define('CLI_SCRIPT', true);
require('config.php');
set_config('enablechat', '0', 'local_datacurso');
"
```

2. Verificar que existe el JavaScript compilado:
```bash
ls -l local/dttutor/amd/build/tutor_ia_chat.min.js
```

3. Si no existe, compilar:
```bash
grunt amd --root=local/dttutor
```

4. Purgar cachés

### Problema: Error de API

**Síntoma**: Mensajes de error al enviar chat

**Soluciones**:

1. Verificar URL de API:
```bash
curl -I https://plugins-ai-dev.datacurso.com
# Debe retornar 200 OK o similar
```

2. Verificar token:
```bash
curl -H "Authorization: Bearer TU_TOKEN" https://plugins-ai-dev.datacurso.com/health
```

3. Ver logs de error de Moodle:
```bash
tail -f /path/to/moodledata/error.log
```

### Problema: Permisos

**Síntoma**: Error de permisos al intentar usar el chat

**Solución**: Verificar que el rol tiene la capacidad:

1. Ir a **Administración del sitio > Usuarios > Permisos > Definir roles**
2. Editar el rol (Estudiante/Profesor)
3. Buscar "Usar el Tutor-IA" o `local/dttutor:use`
4. Asegurarse que está en "Permitir"

O via CLI:
```bash
php -r "
define('CLI_SCRIPT', true);
require('config.php');
\$studentrole = \$DB->get_record('role', ['shortname' => 'student']);
assign_capability('local/dttutor:use', CAP_ALLOW, \$studentrole->id, context_system::instance()->id);
echo 'Capability asignada' . PHP_EOL;
"
```

## Soporte

Si encuentras problemas no cubiertos en esta guía:

1. Revisar README.md para documentación completa
2. Revisar CLAUDE.md para detalles técnicos
3. Contactar soporte: josue@datacurso.com
4. Abrir issue en GitHub (si aplica)

## Checklist de Instalación

Usa este checklist para asegurar una instalación exitosa:

- [ ] Requisitos del sistema verificados
- [ ] Plugin descargado/clonado en `local/dttutor/`
- [ ] Estructura de archivos verificada
- [ ] Upgrade ejecutado sin errores
- [ ] Plugin visible en lista de plugins
- [ ] Configuración API completada (URL + Token)
- [ ] Chat habilitado en settings
- [ ] Avatar seleccionado
- [ ] Posición configurada
- [ ] Cachés purgados
- [ ] Web services registrados
- [ ] Hooks registrados
- [ ] Archivos AMD compilados existen
- [ ] Avatares PNG existen
- [ ] Prueba funcional en curso exitosa
- [ ] No hay conflictos con local_datacurso
- [ ] No hay errores en logs
- [ ] No hay errores en consola del navegador

---

**Última actualización**: 2025-10-07
**Versión del plugin**: 1.0.0
**Moodle mínimo requerido**: 4.5
