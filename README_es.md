# Rollpix Google One Tap Sign-in para Magento 2

**Sponsor: [www.rollpix.com](https://www.rollpix.com)**

> **[Read in English](README.md)**

---

## Descripcion General

**Rollpix Google One Tap Sign-in** es una extension de Magento 2 disenada para proporcionar una experiencia de autenticacion sin friccion para clientes de e-commerce. Al integrar Google One Tap Sign-in y un Boton de Google Sign-In configurable, esta extension elimina la necesidad de inicios de sesion tradicionales, permitiendo a los usuarios autenticarse con un solo clic.

Este modulo mejora la experiencia del usuario, aumenta las tasas de conversion y reduce el abandono del carrito eliminando las barreras de inicio de sesion.

A diferencia de otras soluciones similares, esta extension esta construida desde cero con capacidades de personalizacion completa, garantizando flexibilidad para los propietarios de tiendas. Incluye la dependencia **google/apiclient** para verificacion segura de tokens del lado del servidor.

---

## Indice

- [Por Que Elegir Rollpix Google One Tap?](#por-que-elegir-rollpix-google-one-tap)
- [Caracteristicas](#caracteristicas)
- [Requisitos Tecnicos](#requisitos-tecnicos)
- [Instalacion](#instalacion)
- [Configuracion de Google Cloud Console](#configuracion-de-google-cloud-console)
- [Configuracion del Modulo](#configuracion-del-modulo)
- [Opciones de Configuracion](#opciones-de-configuracion)
- [Solucion de Problemas](#solucion-de-problemas)
- [Changelog](#changelog)
- [Licencia](#licencia)

---

## Por Que Elegir Rollpix Google One Tap?

### Autenticacion Sin Complicaciones
Olvidate de los largos y frustrantes formularios de inicio de sesion. Con **Google One Tap**, tus clientes pueden iniciar sesion instantaneamente con sus cuentas de Google, aumentando el engagement y la velocidad de checkout.

### Experiencia Perfecta en Todos los Dispositivos
Esta extension proporciona una experiencia de inicio de sesion consistente en escritorio, tabletas y dispositivos moviles, haciendo que la autenticacion sea sin esfuerzo.

### Reduce el Abandono de Inicio de Sesion
Los clientes a menudo olvidan sus contrasenas o abandonan el proceso de inicio de sesion debido a pasos de autenticacion largos. **One Tap Sign-in** elimina estas barreras, asegurando una mayor tasa de exito de inicio de sesion.

### Seguridad Mejorada
Este modulo soporta los **protocolos de autenticacion seguros de Google**, ayudando a proteger las credenciales de usuario de accesos no autorizados. La biblioteca **google/apiclient** incluida garantiza una validacion segura de tokens.

### Personalizacion Completa
Los administradores de la tienda tienen control total sobre la configuracion de autenticacion, diseno de UI, apariencia del boton y posicionamiento por pagina para que coincidan con su marca y requisitos.

---

## Caracteristicas

- Habilitar o deshabilitar la extension desde el backend
- **Popup de Google One Tap** con posicion configurable, inicio de sesion automatico, contexto, soporte ITP y callback de cierre
- **Boton de Google Sign-In** en login, registro, checkout y paginas con selector CSS personalizado
- **Boton estilo tarjeta** en paginas de login y checkout con titulo y contenedor con estilo
- **Tarjeta en columna lateral** en pagina de registro (al lado del formulario)
- **Posicion del boton configurable** por pagina (arriba/abajo para login, arriba/abajo/lateral para registro, arriba/abajo para checkout)
- **Personalizacion de apariencia del boton**: tema, tamano, forma, texto y alineacion del logo
- Autenticacion segura con verificacion de tokens server-side via **google/apiclient**
- Creacion automatica de cuentas de clientes para usuarios nuevos
- Rate limiting para proteccion contra ataques de fuerza bruta
- Logging de debug para solucion de problemas
- Compatibilidad con **Amasty Checkout** (One Tap + desactivacion opcional del boton cuando Amasty Social Login esta activo)
- Traducciones en espanol (Espana, Mexico, Argentina)
- Seccion de **Informacion del Modulo** en admin mostrando version, nombre del modulo y URL del repositorio
- Compatible con CSP (Content Security Policy)

---

## Requisitos Tecnicos

| Requisito | Version |
|---|---|
| **Nombre del Modulo** | `rollpix/google-one-tap` |
| **Magento** | 2.4.6 - 2.4.8 |
| **PHP** | ^8.1.0 \|\| ^8.2.0 |
| **Dependencia** | `google/apiclient` ^2.15.0 |

---

## Instalacion

### Paso 1: Configurar Acceso al Repositorio

```bash
# Opcion 1: Repositorio privado de Composer
composer config --auth http-basic.repo.rollpix.com [usuario] [contrasena]

# Opcion 2: Repositorio privado de GitHub
composer config --global github-oauth.github.com [tu-token-de-acceso-personal]
```

### Paso 2: Agregar el Repositorio

```bash
# Repositorio privado de Composer
composer config repositories.rollpix-google-one-tap composer https://repo.rollpix.com/

# O repositorio de GitHub
composer config repositories.rollpix-google-one-tap vcs https://github.com/ROLLPIX/M2-google-one-tap
```

### Paso 3: Instalar el Modulo

```bash
composer require rollpix/google-one-tap:^2.0
bin/magento module:enable Rollpix_GoogleOneTap
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Paso 4: Verificar la Instalacion

```bash
bin/magento module:status Rollpix_GoogleOneTap
```

---

## Configuracion de Google Cloud Console

### Paso 1: Crear un Proyecto en Google Cloud

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Haz clic en **"Seleccionar proyecto"** en la parte superior
3. Haz clic en **"Nuevo proyecto"**, ingresa un nombre y haz clic en **"Crear"**

### Paso 2: Habilitar la API de Google Identity

1. Ve a **APIs y servicios > Biblioteca**
2. Busca **"Google Identity Services"**
3. Haz clic en **"Habilitar"**

### Paso 3: Configurar la Pantalla de Consentimiento OAuth

1. Ve a **APIs y servicios > Pantalla de consentimiento de OAuth**
2. Selecciona **"Externo"** como tipo de usuario
3. Completa: nombre de la aplicacion, email de soporte, dominios autorizados, contacto del desarrollador
4. Haz clic en los pasos restantes y **"Volver al panel"**

### Paso 4: Crear Credenciales OAuth 2.0

1. Ve a **APIs y servicios > Credenciales**
2. Haz clic en **"Crear credenciales" > "ID de cliente de OAuth 2.0"**
3. Selecciona **"Aplicacion web"**
4. Configura:
   - **Origenes de JavaScript autorizados**: `https://tudominio.com` (y `https://www.tudominio.com`)
   - **URIs de redireccionamiento autorizados**: `https://tudominio.com/customer/account`
5. Haz clic en **"Crear"**

### Paso 5: Copiar el Client ID

Copia el **Client ID** (formato: `123456789-abc123def456.apps.googleusercontent.com`). NO necesitas el Client Secret para One Tap.

> **Importante:** Usa HTTPS en produccion. Agrega todos los dominios donde quieras usar One Tap (www y sin www).

---

## Configuracion del Modulo

### Acceder a la Configuracion

**Stores > Configuration > Rollpix > One Tap Login**

---

## Opciones de Configuracion

### 1. General

| Campo | Tipo | Descripcion |
|---|---|---|
| **Module Status** | Habilitar/Deshabilitar | Activa o desactiva completamente el modulo |

### 2. Configuracion del Popup One Tap

| Campo | Tipo | Descripcion |
|---|---|---|
| **Client ID** | Texto encriptado | Google OAuth Client ID de Cloud Console |
| **Close Prompt on Background Click** | Si/No | Cerrar el prompt al hacer clic fuera |
| **Auto Sign in** | Si/No | Auto-autenticar sin hacer clic en el prompt |
| **Position** | Select | Posicion del prompt: Top Right, Top Left, Bottom Right, Bottom Left |
| **Prompt Context** | Select | Texto del prompt: "Sign In" o "Sign Up" |
| **Prompt Parent Element ID** | Texto | ID opcional de elemento DOM para anclar el popup |
| **ITP Support (Safari)** | Si/No | Soporte para Intelligent Tracking Prevention |
| **Show Message on Dismiss** | Si/No | Mostrar notificacion cuando el usuario cierra el prompt |

### 3. Boton de Google Sign-In

| Campo | Tipo | Descripcion |
|---|---|---|
| **Enable Button** | Habilitar/Deshabilitar | Mostrar un boton de Google Sign-In en las paginas seleccionadas |
| **Show Button On** | Multiselect | Paginas: Login, Registro, Checkout, Selector CSS Personalizado |
| **Custom CSS Selector** | Texto | Selector CSS para ubicacion personalizada del boton |
| **Login Page Position** | Select | Encima del formulario de login / Debajo del formulario de login |
| **Registration Page Position** | Select | Encima / Debajo / Columna lateral (al lado del formulario) |
| **Checkout Page Position** | Select | Encima del formulario de checkout / Debajo de la seccion de email |
| **Button Theme** | Select | Contorno, Azul, Negro |
| **Button Size** | Select | Grande, Mediano, Pequeno |
| **Button Shape** | Select | Rectangular, Pastilla, Circulo, Cuadrado |
| **Button Text** | Select | Sign in with Google, Sign up with Google, Continue with Google, Signin |
| **Logo Alignment** | Select | Izquierda, Centro |

### 4. Configuracion de Seguridad

| Campo | Tipo | Descripcion |
|---|---|---|
| **Enable Debug Logging** | Si/No | Registrar informacion detallada para solucion de problemas (nunca usar en produccion) |
| **Enable Rate Limiting** | Si/No | Limitar intentos de autenticacion por IP |
| **Maximum Attempts** | Numero | Intentos maximos dentro de la ventana de tiempo (default: 10) |
| **Time Window** | Numero | Ventana de tiempo en segundos (default: 60) |

### 5. Compatibilidad

| Campo | Tipo | Descripcion |
|---|---|---|
| **Disable Button when Amasty Social Login is active** | Si/No | Evitar botones de Google duplicados cuando Amasty Social Login esta instalado |

### 6. Informacion del Modulo

Muestra el nombre del modulo, version actual (leida de `composer.json`) y URL del repositorio.

---

## Estilos de Visualizacion del Boton

### Paginas de Login y Checkout
El boton de Google Sign-In se muestra dentro de una **tarjeta con estilo** con un titulo ("Inicio de sesion rapido con tu cuenta de Google") y el boton centrado. La tarjeta proporciona una separacion visual limpia del formulario circundante.

### Pagina de Registro
- **Arriba/Abajo**: El boton aparece con una linea divisoria "O" separandolo del formulario.
- **Columna lateral**: Una tarjeta con estilo se coloca a la derecha del formulario de registro con un titulo ("Registro rapido con tu cuenta de Google") y el boton dentro. En movil, se apila debajo del formulario.

---

## Solucion de Problemas

### El prompt no aparece en el frontend

1. Verifica que el modulo este **habilitado** en la configuracion
2. Verifica que el **Client ID** este correctamente configurado
3. Limpia el cache: `bin/magento cache:flush`
4. Verifica que no hayas iniciado sesion (el prompt solo aparece para usuarios no autenticados)
5. Verifica que tu dominio este en los **origenes autorizados** en Google Cloud Console
6. Abre la consola del navegador (F12) y busca errores de JavaScript

### Error "Invalid Client ID"

1. Verifica que el Client ID copiado sea correcto (sin espacios extra)
2. Verifica que el dominio actual este en los **origenes autorizados** en Google Cloud Console
3. Limpia el cache de Magento despues de cambiar el Client ID

### El prompt aparece pero no inicia sesion

1. Revisa la consola del navegador (F12) para errores
2. Revisa los logs de Magento: `var/log/system.log` y `var/log/exception.log`
3. Verifica que la cuenta de Google tenga un email verificado
4. Verifica que el dominio use **HTTPS** en produccion

---

## Changelog

### v2.1.3
- Boton estilo tarjeta para paginas de login y checkout (contenedor con estilo y titulo)
- Eliminadas las posiciones rotas "Izquierda/Derecha del boton de login" en checkout
- Agregada posicion "Encima del formulario de checkout"
- Corregido el orden del separador en registro "arriba" via reordenamiento DOM en JS

### v2.1.2
- Correccion de posicionamiento del boton: orden del separador, estilos inline de columna lateral, selectores de checkout

### v2.1.1
- Seccion de Informacion del Modulo en admin (nombre, version, URL del repositorio)

### v2.1.0
- Posicion del boton configurable por pagina (login, registro, checkout)

### v2.0.0
- Boton de Google Sign-In en login, registro, checkout y paginas personalizadas
- Configuracion mejorada de One Tap (contexto, soporte ITP, parent del prompt, callback de cierre)
- Personalizacion de apariencia del boton (tema, tamano, forma, texto, alineacion del logo)
- Compatibilidad con Amasty Checkout y Amasty Social Login
- Rate limiting y logging de debug

### v1.x
- Popup de Google One Tap con posicion configurable e inicio de sesion automatico
- Verificacion segura de tokens server-side con google/apiclient
- Creacion automatica de cuentas de clientes
- Traducciones en espanol

---

## Caracteristicas de Seguridad

- **Verificacion de Token**: Todos los tokens de Google son verificados del lado del servidor usando `google/apiclient`
- **Email Verificado**: Solo se aceptan emails verificados por Google
- **Client ID Encriptado**: Almacenado encriptado en la base de datos usando el sistema de encriptacion de Magento
- **Rate Limiting**: Limites configurables para prevenir ataques de fuerza bruta
- **Compatible con CSP**: Whitelist de `accounts.google.com` para script-src y style-src
- **Logging de Errores**: Todos los errores se registran para auditoria

---

## Soporte

- **GitHub Issues**: https://github.com/ROLLPIX/M2-google-one-tap/issues
- **Repositorio**: https://github.com/ROLLPIX/M2-google-one-tap
- **Website**: [www.rollpix.com](https://www.rollpix.com)

---

## Licencia

- **OSL-3.0** (Open Software License 3.0)
- **AFL-3.0** (Academic Free License 3.0)

---

Construido con rendimiento, seguridad y experiencia de usuario en mente.
Magento 2.4.6 - 2.4.8
