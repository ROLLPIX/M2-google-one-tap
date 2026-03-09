# Manual de Usuario - Rollpix Google One Tap Login

**Modulo: Rollpix Google One Tap Sign-in para Magento 2**

---

## Que es este modulo?

Este modulo integra **Google One Tap Sign-in** en tu tienda Magento 2. Permite que los clientes inicien sesion con su cuenta de Google de forma rapida, sin necesidad de completar formularios de login tradicionales.

El modulo tiene **dos componentes principales**:

### 1. Popup One Tap (aparece automaticamente)

Es un pequeno popup que aparece automaticamente en la esquina de la pantalla cuando un usuario no logueado visita la tienda. Muestra la cuenta de Google del usuario y le permite iniciar sesion con un solo clic.

- Aparece en **todas las paginas** de la tienda (mientras el usuario no este logueado)
- Se puede configurar en que esquina aparece (arriba-derecha, arriba-izquierda, etc.)
- Opcionalmente puede auto-loguearse sin que el usuario haga clic
- **Importante:** Google aplica un cooldown exponencial si el usuario cierra el popup: 2 horas, 1 dia, 1 semana, 4 semanas. Esto lo controla Google y no se puede modificar.

### 2. Boton de Google Sign-In (se coloca en paginas especificas)

Es un boton estandar de Google ("Iniciar sesion con Google") que se puede agregar en paginas especificas:

- **Pagina de Login** - se muestra como una tarjeta con titulo arriba o abajo del formulario de login
- **Pagina de Registro** - se puede mostrar arriba, abajo, o como columna lateral al lado del formulario
- **Pagina de Checkout** - se muestra como una tarjeta con titulo, arriba o abajo de la seccion de email
- **Selector CSS personalizado** - para ubicarlo en cualquier otro lugar del sitio

Ambos componentes (popup y boton) funcionan de forma independiente: se pueden habilitar uno, el otro, o ambos simultaneamente.

---

## Prerequisito: Configuracion en Google Cloud Console

Antes de configurar el modulo en Magento, necesitas crear las credenciales en Google Cloud Console.

### Paso 1: Crear un proyecto en Google Cloud

1. Ir a [Google Cloud Console](https://console.cloud.google.com/)
2. Hacer clic en **"Select a project"** (Seleccionar un proyecto) en la barra superior
3. Hacer clic en **"New Project"** (Nuevo proyecto)
4. Ingresar un nombre para el proyecto (ej: "MiTienda Login")
5. Hacer clic en **"Create"** (Crear)
6. Asegurarse de tener seleccionado el proyecto recien creado

### Paso 2: Configurar la Pantalla de Consentimiento OAuth

1. En el menu lateral, ir a **"APIs & Services"** > **"OAuth consent screen"**
   - Si el menu dice "APIs y servicios", buscar "Pantalla de consentimiento de OAuth"
2. Seleccionar **"External"** (Externo) como tipo de usuario y hacer clic en "Create"
3. Completar los datos obligatorios:
   - **App name** (Nombre de la app): el nombre de tu tienda
   - **User support email** (Email de soporte): tu email de contacto
   - **Authorized domains** (Dominios autorizados): agregar tu dominio (ej: `tutienda.com`)
   - **Developer contact email** (Email de contacto del desarrollador): tu email
4. Hacer clic en **"Save and Continue"** en los pasos siguientes hasta finalizar
5. En la seccion **"Publishing status"** (Estado de publicacion), hacer clic en **"Publish App"** para pasar de "Testing" a "In Production" (si no se publica, solo funcionara para las cuentas de prueba que agregues)

### Paso 3: Crear las Credenciales OAuth 2.0

1. En el menu lateral, ir a **"APIs & Services"** > **"Credentials"** (Credenciales)
2. Hacer clic en **"Create Credentials"** (Crear credenciales) > **"OAuth client ID"** (ID de cliente de OAuth)
3. En **"Application type"** (Tipo de aplicacion), seleccionar **"Web application"** (Aplicacion web)
4. Darle un nombre descriptivo (ej: "One Tap Login MiTienda")
5. En **"Authorized JavaScript origins"** (Origenes de JavaScript autorizados), agregar:
   - `https://tutienda.com`
   - `https://www.tutienda.com` (si usas www)
   - Si estas en desarrollo: `http://localhost` o la URL local correspondiente
6. En **"Authorized redirect URIs"** (URIs de redireccionamiento autorizados), agregar:
   - `https://tutienda.com/onetaplogin/checkout/response`
   - `https://www.tutienda.com/onetaplogin/checkout/response` (si usas www)
7. Hacer clic en **"Create"** (Crear)
8. **Copiar el Client ID** que se genera (tiene formato: `123456789-abc123def456.apps.googleusercontent.com`)

> **Nota:** NO necesitas el Client Secret. Solo el Client ID.

> **Importante:** El sitio DEBE usar HTTPS en produccion. Google One Tap no funciona en HTTP (excepto localhost para desarrollo).

---

## Configuracion del Modulo en Magento

### Como acceder

En el admin de Magento, ir a:

**Stores** > **Configuration** > **Rollpix** > **One Tap Login**

La configuracion esta dividida en 6 secciones.

---

### Seccion 1: General

| Campo | Opciones | Descripcion |
|---|---|---|
| **Module Status** | Enable / Disable | Activa o desactiva todo el modulo. Si esta en Disable, ni el popup ni los botones se muestran. |

---

### Seccion 2: One Tap Popup Configuration

Esta seccion controla el popup automatico que aparece en todas las paginas.

| Campo | Opciones | Descripcion |
|---|---|---|
| **Client Id** | Texto (encriptado) | El Client ID de Google que copiaste en el paso anterior. Se guarda encriptado en la base de datos. |
| **Close Prompt on Background Click** | Yes / No | Si esta en **Yes**, el popup se cierra cuando el usuario hace clic fuera de el. Si esta en **No**, el popup permanece visible hasta que el usuario lo cierre explicitamente. |
| **Auto Sign in without Clicking Prompt** | Yes / No | Si esta en **Yes**, el usuario se loguea automaticamente sin tener que hacer clic en el popup (solo funciona si el usuario tiene una unica cuenta de Google en el navegador). Si esta en **No**, el usuario debe hacer clic para confirmar. **Recomendado: No** (puede ser invasivo). |
| **Position** | Top Right / Top Left / Bottom Right / Bottom Left | Esquina de la pantalla donde aparece el popup. **Recomendado: Top Right** (es la posicion estandar de Google). |
| **Prompt Context** | Sign In / Sign Up / Use | Controla el texto que muestra el popup. **"Sign In"** dice "Inicia sesion con Google", **"Sign Up"** dice "Registrate con Google", **"Use"** dice "Usa tu cuenta de Google". **Recomendado: Sign In** para la mayoria de tiendas. |
| **Prompt Parent Element ID** | Texto (opcional) | ID de un elemento HTML donde anclar el popup. Dejar vacio para el comportamiento flotante por defecto. Solo usar si se necesita que el popup aparezca dentro de un contenedor especifico del tema. |
| **ITP Support (Safari)** | Yes / No | Habilita soporte para Intelligent Tracking Prevention de Safari. Sin esto, el popup puede no funcionar en Safari. **Recomendado: Yes**. |
| **Show Message on Dismiss** | Yes / No | Si esta en **Yes**, muestra un mensaje al usuario cuando cierra el popup ("Descartaste el inicio de sesion con Google..."). **Recomendado: No** (puede ser molesto). |

---

### Seccion 3: Google Sign-In Button

Esta seccion controla los botones de Google que aparecen en paginas especificas.

#### Configuracion general del boton

| Campo | Opciones | Descripcion |
|---|---|---|
| **Enable Google Sign-In Button** | Enable / Disable | Activa o desactiva los botones de Google. Esto es independiente del popup: puedes tener el popup activo y los botones desactivados, o viceversa. |
| **Show Button On** | Customer Login Page / Customer Registration Page / Checkout Page / Custom CSS Selector | Seleccion multiple. Elegir en que paginas mostrar el boton. Se pueden seleccionar varias manteniendo Ctrl presionado. |
| **Custom CSS Selector** | Texto | Selector CSS donde insertar el boton (ej: `#mi-contenedor`). Solo se usa si seleccionaste "Custom CSS Selector" en el campo anterior. |

#### Posicion del boton por pagina

| Campo | Opciones | Descripcion |
|---|---|---|
| **Login Page Position** | Above Login Form / Below Login Form | Donde mostrar el boton en la pagina de login. En ambos casos se muestra como una **tarjeta** con titulo "Quick sign in with your Google account". |
| **Registration Page Position** | Above Registration Form / Below Registration Form / Side Column (Next to Form) | Donde mostrar el boton en la pagina de registro. **Side Column** lo coloca como una tarjeta al costado derecho del formulario. En mobile se apila debajo automaticamente. |
| **Checkout Page Position** | Above Checkout Form / Below Email Section | Donde mostrar el boton en el checkout. Se muestra como una **tarjeta** con titulo. Compatible con checkout nativo de Magento y Amasty Checkout. |

#### Apariencia del boton

| Campo | Opciones | Descripcion |
|---|---|---|
| **Button Theme** | Outline / Filled Blue / Filled Black | Estilo visual del boton. **Outline** es fondo blanco con borde, **Filled Blue** es fondo azul, **Filled Black** es fondo negro. **Recomendado: Outline** (es el mas estandar). |
| **Button Size** | Large / Medium / Small | Tamano del boton. **Recomendado: Large** para mayor visibilidad. |
| **Button Shape** | Rectangular / Pill / Circle / Square | Forma del boton. **Rectangular** es el clasico, **Pill** tiene bordes redondeados. **Recomendado: Rectangular o Pill**. |
| **Button Text** | Sign in with Google / Sign up with Google / Continue with Google / Sign in | Texto que muestra el boton. **Recomendado: "Sign in with Google"** o **"Continue with Google"**. |
| **Logo Alignment** | Left / Center | Posicion del logo de Google dentro del boton. |

---

### Seccion 4: Security Settings

| Campo | Opciones | Descripcion |
|---|---|---|
| **Enable Debug Logging** | Yes / No | Activa logs detallados en `var/log/system.log`. **NUNCA dejar activado en produccion** - expone datos sensibles (tokens, emails, IPs). Solo activar temporalmente para diagnosticar problemas. |
| **Enable Rate Limiting** | Yes / No | Limita la cantidad de intentos de autenticacion por IP. Protege contra ataques de fuerza bruta. **Recomendado: Yes**. |
| **Maximum Attempts** | Numero | Cantidad maxima de intentos permitidos en la ventana de tiempo. **Por defecto: 10**. |
| **Time Window (seconds)** | Numero | Ventana de tiempo en segundos para contar intentos. **Por defecto: 60** (1 minuto). Con la configuracion por defecto, se permiten 10 intentos por minuto por IP. |

---

### Seccion 5: Compatibility

| Campo | Opciones | Descripcion |
|---|---|---|
| **Disable Google Button when Amasty Social Login is active** | Yes / No | Si la tienda tiene instalado Amasty Social Login (que ya incluye su propio boton de Google), esta opcion desactiva nuestro boton para evitar duplicados. El popup One Tap sigue funcionando porque Amasty no incluye esa funcionalidad. |

---

### Seccion 6: Module Information

Muestra informacion del modulo (solo lectura):
- Nombre del modulo
- Version instalada
- URL del repositorio

---

## Configuracion Recomendada para Empezar

Si es la primera vez que configuras el modulo, estos son los valores recomendados:

**General:**
- Module Status: **Enable**

**One Tap Popup:**
- Client Id: **(pegar el Client ID de Google)**
- Close Prompt on Background Click: **Yes**
- Auto Sign in: **No**
- Position: **Top Right**
- Prompt Context: **Sign In**
- Prompt Parent Element ID: **(dejar vacio)**
- ITP Support: **Yes**
- Show Message on Dismiss: **No**

**Google Sign-In Button:**
- Enable: **Enable**
- Show Button On: **Customer Login Page, Customer Registration Page, Checkout Page**
- Login Page Position: **Below Login Form**
- Registration Page Position: **Below Registration Form**
- Checkout Page Position: **Above Checkout Form**
- Button Theme: **Outline**
- Button Size: **Large**
- Button Shape: **Rectangular**
- Button Text: **Sign in with Google**
- Logo Alignment: **Left**

**Security:**
- Debug Logging: **No**
- Rate Limiting: **Yes**
- Maximum Attempts: **10**
- Time Window: **60**

**Compatibility:**
- Disable when Amasty active: **Yes** (si tienen Amasty Social Login instalado)

Despues de guardar la configuracion, **limpiar cache**: `bin/magento cache:flush`

---

## Solucion de Problemas

### El popup no aparece

1. Verificar que **Module Status** este en Enable
2. Verificar que el **Client ID** este correctamente pegado (sin espacios extra)
3. Limpiar cache: `bin/magento cache:flush`
4. Verificar que no estes logueado (el popup solo aparece para usuarios no autenticados)
5. Verificar que el dominio este en los **"Authorized JavaScript origins"** en Google Cloud Console
6. Abrir la consola del navegador (F12 > Console) y buscar errores de JavaScript
7. Si usas Safari, verificar que **ITP Support** este en Yes
8. Recordar que Google aplica un cooldown si el usuario cerro el popup antes (hasta 4 semanas)

### Error "Clave de formulario invalida" o "Invalid form key"

Verificar que el modulo este actualizado a la version 2.1.4 o superior. Este error se corrigio en esa version.

### El boton de Google no aparece en login/registro/checkout

1. Verificar que **Enable Google Sign-In Button** este en Enable
2. Verificar que la pagina este seleccionada en **"Show Button On"**
3. Limpiar cache
4. Abrir la consola del navegador y buscar errores
5. Si usas Amasty Social Login, verificar el campo de compatibilidad

### El boton aparece duplicado

Si la tienda tiene Amasty Social Login instalado, activar la opcion **"Disable Google Button when Amasty Social Login is active"** en la seccion Compatibility.

### Error "Invalid Client ID" o el popup muestra error

1. Verificar que el Client ID este correcto y completo
2. Verificar que el dominio actual este en los **"Authorized JavaScript origins"** en Google Cloud Console (agregar tanto con www como sin www)
3. Verificar que el sitio use **HTTPS** en produccion
4. Limpiar cache de Magento despues de cambiar el Client ID

### El usuario se loguea pero la pagina no se actualiza

1. Verificar que las secciones de customer data de Magento funcionen correctamente
2. Puede ser un problema de cache de full page. Verificar que Varnish/FPC este correctamente configurado
3. Activar temporalmente **Debug Logging** y revisar `var/log/system.log` para ver si la autenticacion fue exitosa

### El login funciona pero no crea cuenta nueva

1. Revisar `var/log/exception.log` para ver si hay errores de creacion de cliente
2. Verificar que la cuenta de Google tenga un email verificado
3. Verificar los permisos de la base de datos

### Como ver los logs de debug

1. Ir a **Security Settings** > **Enable Debug Logging** > **Yes**
2. Guardar y limpiar cache
3. Intentar el login con Google
4. Revisar el archivo `var/log/system.log` en el servidor
5. **Desactivar el debug logging inmediatamente despues** de diagnosticar el problema

---

## Notas Importantes

- **El modulo requiere HTTPS** en produccion. Google One Tap no funciona en HTTP (excepto localhost para desarrollo).
- **Cada cambio de configuracion requiere limpiar cache** para que tome efecto.
- **El popup tiene cooldown de Google:** si un usuario cierra el popup varias veces, Google deja de mostrarselo por un tiempo creciente (2h, 1 dia, 1 semana, 4 semanas). Esto no lo controlamos nosotros. Para resetear el cooldown durante pruebas, limpiar las cookies del navegador.
- **La configuracion es por Website**, no por Store View. Si hay multiples websites en la misma instalacion, se puede tener un Client ID diferente para cada uno.
- **El Client ID se guarda encriptado** en la base de datos. Si se migra la base de datos a otro entorno, puede ser necesario reconfigurar el Client ID si la clave de encriptacion es diferente.
