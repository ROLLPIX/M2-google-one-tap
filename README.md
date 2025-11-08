# Rollpix Google One Tap Sign-in para Magento 2

## Descripción General
**Rollpix Google One Tap Sign-in** es una extensión de Magento 2 diseñada para proporcionar una experiencia de autenticación sin fricción para clientes de e-commerce. Al integrar Google One Tap Sign-in, esta extensión elimina la necesidad de inicios de sesión tradicionales, permitiendo a los usuarios autenticarse con un solo clic.

Este módulo mejora la experiencia del usuario, aumenta las tasas de conversión y reduce el abandono del carrito eliminando las barreras de inicio de sesión.

A diferencia de otras soluciones similares, esta extensión está construida desde cero con capacidades de personalización completa, garantizando flexibilidad para los propietarios de tiendas. Además, incluye la dependencia **google/apiclient**, asegurando una conexión segura y eficiente a los servicios de autenticación de Google.

---

## Índice
- [¿Por Qué Elegir Rollpix Google One Tap?](#por-qué-elegir-rollpix-google-one-tap-sign-in-para-magento-2)
- [Características](#características)
- [Beneficios](#beneficios)
- [Requisitos Técnicos](#detalles-técnicos)
- [Instalación](#instalación)
- [Configuración de Google Cloud Console](#configuración-de-google-cloud-console)
- [Configuración del Módulo en Magento](#configuración-del-módulo-en-magento)
- [Opciones de Configuración](#opciones-de-configuración)

---

## ¿Por Qué Elegir Rollpix Google One Tap Sign-in para Magento 2?

### 🔹 Autenticación Sin Complicaciones
Olvídate de los largos y frustrantes formularios de inicio de sesión. Con **Google One Tap**, tus clientes pueden iniciar sesión instantáneamente con sus cuentas de Google, aumentando el engagement y la velocidad de checkout.

### 🔹 Experiencia Perfecta en Todos los Dispositivos
Esta extensión proporciona una experiencia de inicio de sesión consistente en escritorio, tabletas y dispositivos móviles, haciendo que la autenticación sea sin esfuerzo.

### 🔹 Reduce el Abandono de Inicio de Sesión
Los clientes a menudo olvidan sus contraseñas o abandonan el proceso de inicio de sesión debido a pasos de autenticación largos. **One Tap Sign-in** elimina estas barreras, asegurando una mayor tasa de éxito de inicio de sesión.

### 🔹 Seguridad Mejorada
Este módulo soporta los **protocolos de autenticación seguros de Google**, ayudando a proteger las credenciales de usuario de accesos no autorizados. La biblioteca **google/apiclient** incluida garantiza una validación segura de tokens.

### 🔹 Fácil Personalización
Los administradores de la tienda tienen control total sobre la configuración de autenticación, diseño de UI y opciones de seguridad para que coincidan con su marca y requisitos.

---

## Características

✅ Habilitar o deshabilitar la extensión desde el backend<br>
✅ Ingresar **Google Client ID** obtenido desde Google Cloud Console<br>
✅ Inicio de sesión automático sin requerir que los usuarios hagan clic en el prompt<br>
✅ Elegir la **posición del prompt** desde el panel de administración (4 opciones)<br>
✅ Configurar comportamiento al hacer clic fuera del prompt<br>
✅ Totalmente optimizado para **usuarios móviles y de escritorio**<br>
✅ Autenticación segura con **google/apiclient**<br>
✅ Creación automática de cuentas de clientes<br>
✅ Traducciones en español (España, México y Argentina)<br>
✅ Logging de errores para debugging<br>

---

## Beneficios

💡 **Experiencia de Usuario Mejorada** – Inicio de sesión más rápido con mínimo esfuerzo, aumentando la satisfacción del cliente.<br>
💳 **Reducción del Abandono del Carrito** – Un proceso de inicio de sesión sin problemas conduce a mayores conversiones.<br>
📱 **Compatible con Móviles** – Optimizado para todos los dispositivos, asegurando una experiencia de compra fluida.<br>
🔒 **Seguridad Mejorada** – Integra la **API de autenticación segura de Google** para máxima protección de datos.<br>

---

## Detalles Técnicos

**Nombre del Módulo:** `rollpix/google-one-tap`
**Versión de Magento:** Magento 2.4.6 - 2.4.8
**Compatibilidad PHP:** `^8.1.0 || ^8.2.0`
**Dependencia Requerida:** `google/apiclient` (versión `^2.15.0`)

---

## Instalación

### Paso 1: Configurar Acceso al Repositorio Privado

Este módulo se distribuye a través de un repositorio privado de Composer. Contacta al equipo de Rollpix para obtener las credenciales de acceso.

```bash
# Configurar credenciales de autenticación (solo primera vez)
# Opción 1: Para repositorio Composer privado
composer config --auth http-basic.repo.rollpix.com [usuario] [contraseña]

# Opción 2: Para repositorio privado de GitHub
composer config --global github-oauth.github.com [tu-token-de-acceso-personal]
```

### Paso 2: Configurar el Repositorio

```bash
# Agregar el repositorio privado de Rollpix a tu proyecto
composer config repositories.rollpix-google-one-tap composer https://repo.rollpix.com/
```

**Nota:** Si usas un repositorio privado de GitHub, usa en su lugar:
```bash
composer config repositories.rollpix-google-one-tap vcs https://github.com/ROLLPIX/M2-google-one-tap
```

### Paso 3: Instalar el Módulo

```bash
# Instalar el módulo
composer require rollpix/google-one-tap:^1.0

# Habilitar el módulo
bin/magento module:enable Rollpix_GoogleOneTap

# Ejecutar setup upgrade
bin/magento setup:upgrade

# Compilar código (modo producción)
bin/magento setup:di:compile

# Desplegar contenido estático
bin/magento setup:static-content:deploy -f

# Limpiar cache
bin/magento cache:flush
```

### Paso 4: Verificar la Instalación

```bash
# Verificar que el módulo está habilitado
bin/magento module:status Rollpix_GoogleOneTap
```

Deberías ver:
```
Module is enabled
```

### Solución de Problemas de Instalación

**Error de autenticación:**
- Verifica que tus credenciales sean correctas
- Asegúrate de tener acceso al repositorio privado
- Contacta a soporte@rollpix.com para obtener acceso

**Error de estabilidad mínima:**
```bash
# Si obtienes un error de minimum-stability, verifica que exista el tag de versión
composer show rollpix/google-one-tap --all
```

---

## Configuración de Google Cloud Console

Antes de configurar el módulo en Magento, necesitas obtener un **Google Client ID** desde Google Cloud Console.

### Paso 1: Crear un Proyecto en Google Cloud

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Haz clic en **"Seleccionar proyecto"** en la parte superior
3. Haz clic en **"Nuevo proyecto"**
4. Ingresa un nombre para tu proyecto (ej: "Mi Tienda Magento")
5. Haz clic en **"Crear"**

### Paso 2: Habilitar la API de Google Identity

1. En el menú lateral, ve a **"APIs y servicios" > "Biblioteca"**
2. Busca **"Google Identity Services"** o **"Google Sign-In API"**
3. Haz clic en **"Habilitar"**

### Paso 3: Configurar la Pantalla de Consentimiento OAuth

1. Ve a **"APIs y servicios" > "Pantalla de consentimiento de OAuth"**
2. Selecciona **"Externo"** como tipo de usuario
3. Haz clic en **"Crear"**
4. Completa la información requerida:
   - **Nombre de la aplicación**: Tu nombre de tienda
   - **Correo electrónico de asistencia**: Tu email
   - **Dominios autorizados**: Tu dominio (ej: `mitienda.com`)
   - **Información de contacto del desarrollador**: Tu email
5. Haz clic en **"Guardar y continuar"**
6. En **"Permisos"**, haz clic en **"Guardar y continuar"** (no necesitas agregar permisos adicionales)
7. En **"Usuarios de prueba"**, haz clic en **"Guardar y continuar"**
8. Revisa el resumen y haz clic en **"Volver al panel"**

### Paso 4: Crear Credenciales OAuth 2.0

1. Ve a **"APIs y servicios" > "Credenciales"**
2. Haz clic en **"Crear credenciales" > "ID de cliente de OAuth 2.0"**
3. Selecciona **"Aplicación web"** como tipo de aplicación
4. Configura los campos:
   - **Nombre**: "Google One Tap - Mi Tienda"
   - **Orígenes de JavaScript autorizados**:
     - `https://tudominio.com`
     - `https://www.tudominio.com` (si usas www)
     - Para desarrollo: `http://localhost` o tu dominio local
   - **URIs de redireccionamiento autorizados**:
     - `https://tudominio.com/customer/account`
     - `https://www.tudominio.com/customer/account`
5. Haz clic en **"Crear"**

### Paso 5: Copiar el Client ID

1. Aparecerá un modal con tus credenciales
2. **Copia el "ID de cliente"** (Client ID) - se ve algo así: `123456789-abc123def456.apps.googleusercontent.com`
3. **NO necesitas el "Client Secret"** para Google One Tap
4. Guarda el Client ID en un lugar seguro

### Notas Importantes:
- ✅ Asegúrate de usar **HTTPS** en producción (Google lo requiere)
- ✅ Agrega todos los dominios donde quieras usar One Tap (www y sin www)
- ✅ Para desarrollo local, puedes usar HTTP, pero debes agregarlo a los orígenes autorizados

---

## Configuración del Módulo en Magento

### Acceder a la Configuración

1. Inicia sesión en el **Panel de Administración de Magento**
2. Ve a **Stores > Configuration** (Tiendas > Configuración)
3. En el panel izquierdo, busca la sección **"Rollpix"**
4. Haz clic en **"One Tap Login"** (Inicio de sesión One Tap)

### Ubicación en el Menú
```
Stores (Tiendas)
  └─ Configuration (Configuración)
       └─ Rollpix
            └─ One Tap Login (Inicio de sesión One Tap)
```

---

## Opciones de Configuración

### 1. General

#### **Module Status** (Estado del Módulo)
- **Tipo**: Dropdown
- **Opciones**: Enable / Disable (Habilitar / Deshabilitar)
- **Descripción**: Activa o desactiva completamente el módulo Google One Tap
- **Por defecto**: Disabled (Deshabilitado)
- **Recomendación**: Habilita solo después de configurar el Client ID

### 2. Module Configurations (Configuraciones del Módulo)

#### **Client ID** (ID de Cliente)
- **Tipo**: Campo de texto (encriptado)
- **Requerido**: ✅ Sí
- **Descripción**: El Google Client ID obtenido desde Google Cloud Console
- **Formato**: `123456789-abc123def456.apps.googleusercontent.com`
- **Seguridad**: Se almacena encriptado en la base de datos
- **Ejemplo**: `987654321-xyz789abc123.apps.googleusercontent.com`

#### **Close Prompt on Background Click** (Cerrar Ventana al Hacer Clic en el Fondo)
- **Tipo**: Dropdown
- **Opciones**: Yes / No (Sí / No)
- **Descripción**: Permite al usuario cerrar el prompt haciendo clic fuera de él
- **Por defecto**: No
- **Uso**:
  - **Yes**: El prompt se cierra al hacer clic fuera (menos intrusivo)
  - **No**: El usuario debe cerrar el prompt manualmente

#### **Auto Sign in without Clicking Prompt** (Inicio de Sesión Automático)
- **Tipo**: Dropdown
- **Opciones**: Yes / No (Sí / No)
- **Descripción**: Inicia sesión automáticamente sin que el usuario haga clic en el prompt
- **Por defecto**: No
- **Uso**:
  - **Yes**: Login automático si hay una sesión de Google activa
  - **No**: El usuario debe hacer clic en el prompt para iniciar sesión
- **Nota**: Solo funciona si el usuario ya inició sesión en Google

#### **Position** (Posición)
- **Tipo**: Dropdown
- **Opciones**:
  - `Top Right` - Arriba a la derecha
  - `Top Left` - Arriba a la izquierda
  - `Bottom Right` - Abajo a la derecha
  - `Bottom Left` - Abajo a la izquierda
- **Descripción**: Posición del prompt de Google One Tap en la pantalla
- **Por defecto**: Top Right
- **Recomendación**: Top Right es la posición más común y menos intrusiva

---

## Ejemplo de Configuración Recomendada

```
┌─────────────────────────────────────────────────┐
│ General                                          │
├─────────────────────────────────────────────────┤
│ Module Status: Enable                           │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│ Module Configurations                            │
├─────────────────────────────────────────────────┤
│ Client ID: 123456...apps.googleusercontent.com  │
│ Close Prompt on Background Click: No            │
│ Auto Sign in without Clicking Prompt: No        │
│ Position: Top Right                             │
└─────────────────────────────────────────────────┘
```

---

## Solución de Problemas

### El prompt no aparece en el frontend

**Posibles causas:**
1. ✅ Verifica que el módulo esté **habilitado** en la configuración
2. ✅ Verifica que el **Client ID** esté correctamente configurado
3. ✅ Limpia el cache: `bin/magento cache:flush`
4. ✅ Verifica que no haya iniciado sesión en Magento (el prompt solo aparece para usuarios no autenticados)
5. ✅ Verifica que tu dominio esté en la lista de **orígenes autorizados** en Google Cloud Console
6. ✅ Abre la consola del navegador (F12) y busca errores de JavaScript

### Error "Invalid Client ID"

**Solución:**
1. Verifica que el Client ID copiado sea correcto (sin espacios extra)
2. Verifica que el dominio actual esté en los **orígenes autorizados** de Google Cloud Console
3. Si cambiaste el Client ID, limpia el cache de Magento

### El prompt aparece pero no inicia sesión

**Solución:**
1. Abre la consola del navegador (F12) y revisa los errores
2. Verifica los logs de Magento: `var/log/system.log` y `var/log/exception.log`
3. Verifica que la cuenta de Google tenga un email verificado
4. Verifica que el dominio esté usando **HTTPS** en producción

---

## Características de Seguridad

🔒 **Validación de Token**: Todos los tokens de Google son verificados server-side
🔒 **Email Verificado**: Solo se aceptan emails verificados por Google
🔒 **Client ID Encriptado**: El Client ID se almacena encriptado en la base de datos
🔒 **Logging de Errores**: Todos los errores se registran para auditoría
🔒 **Validación de Email**: Se valida el formato del email antes de crear la cuenta

---

## Soporte

Para reportar problemas o solicitar nuevas características, visita:
- **GitHub Issues**: https://github.com/ROLLPIX/M2-google-one-tap/issues
- **Repositorio**: https://github.com/ROLLPIX/M2-google-one-tap

---

## Licencia

- **OSL-3.0** (Open Software License 3.0)
- **AFL-3.0** (Academic Free License 3.0)

---

Esta **extensión de Magento 2** fue construida pensando en el rendimiento, la seguridad y la experiencia del usuario.
¡Si estás buscando una **solución confiable de Google One Tap Sign-in**, este es el módulo para ti! 🚀

<h3>¡Disfruta!</h3>
<h6>Magento 2.4.6 - 2.4.8</h6>
