# Rollpix Google One Tap Sign-in para Magento 2

## Descripción General
**Rollpix Google One Tap Sign-in** es una extensión de Magento 2 diseñada para proporcionar una experiencia de autenticación sin fricción para clientes de e-commerce. Al integrar Google One Tap Sign-in, esta extensión elimina la necesidad de inicios de sesión tradicionales, permitiendo a los usuarios autenticarse con un solo clic.

Este módulo mejora la experiencia del usuario, aumenta las tasas de conversión y reduce el abandono del carrito eliminando las barreras de inicio de sesión.

A diferencia de otras soluciones similares, esta extensión está construida desde cero con capacidades de personalización completa, garantizando flexibilidad para los propietarios de tiendas. Además, incluye la dependencia **google/apiclient**, asegurando una conexión segura y eficiente a los servicios de autenticación de Google.

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

## ¿Qué Pueden Hacer los Administradores de Tienda con Esta Extensión?

🔧 **Configuración de Autenticación:**
- Habilitar o deshabilitar **Google One Tap Sign-in**
- Configurar opciones de seguridad y políticas de autenticación

🎨 **Personalización de UI y Diseño:**
- Ajustar la apariencia y posición del prompt de inicio de sesión One Tap
- Hacer coincidir la UI de One Tap con la marca de la tienda

👥 **Gestión de Usuarios:**
- Crear automáticamente nuevas cuentas de clientes
- Gestionar cuentas de usuario existentes con autenticación Google sin problemas
- Restablecer contraseñas y configurar roles de usuario

🔐 **Características de Seguridad:**
- Monitorear la actividad de inicio de sesión para intentos fallidos y sospechosos
- Prevenir accesos no autorizados con las medidas de seguridad de autenticación de Google

---

## Características

✅ Habilitar o deshabilitar la extensión desde el backend<br>
✅ Ingresar **Google Client ID** obtenido durante el registro<br>
✅ Inicio de sesión automático sin requerir que los usuarios hagan clic en el prompt<br>
✅ Elegir la **posición del prompt** desde el panel de administración<br>
✅ Totalmente optimizado para **usuarios móviles y de escritorio**<br>
✅ Autenticación segura con **google/apiclient**<br>

---

## Beneficios

💡 **Experiencia de Usuario Mejorada** – Inicio de sesión más rápido con mínimo esfuerzo, aumentando la satisfacción del cliente.<br>
💳 **Reducción del Abandono del Carrito** – Un proceso de inicio de sesión sin problemas conduce a mayores conversiones.<br>
📱 **Compatible con Móviles** – Optimizado para todos los dispositivos, asegurando una experiencia de compra fluida.<br>
🔒 **Seguridad Mejorada** – Integra la **API de autenticación segura de Google** para máxima protección de datos.<br>

---

## Detalles Técnicos

**Nombre del Módulo:** `rollpix/google-one-tap`
**Versión de Magento:** Magento 2.x
**Compatibilidad PHP:** `^8.1.0 || ^8.2.0`
**Dependencia Requerida:** `google/apiclient` (versión `^2.15.0`)

---

## Instalación

```sh
composer require rollpix/google-one-tap:dev-main
bin/magento module:enable Rollpix_GoogleOneTap
bin/magento setup:upgrade
bin/magento cache:flush
```

Para más detalles, visita el [repositorio de GitHub](https://github.com/ROLLPIX/M2-google-one-tap).

---

Esta **extensión de Magento 2** fue construida pensando en el rendimiento, la seguridad y la experiencia del usuario.<br>
¡Si estás buscando una **solución confiable de Google One Tap Sign-in**, este es el módulo para ti! 🚀

<h3>¡Disfruta!</h3>
<h6>Magento 2.4.6 - 2.4.8</h6>
