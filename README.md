# Sitio web — Aprender a Vivir Contigo

Este es el sitio web completo, listo para publicar, **sin WordPress ni ningún otro programa**: son solo archivos HTML, CSS e imágenes. Incluye 16 páginas enlazadas (Inicio, Nuestro Propósito, Talleres, Descúbrete, Reconócete Módulo III, Servicios y precios, Agenda y pago, Club de Membresía, Sanando con mi Linaje, Taller de Genealogía, Test de Genealogía, el libro "Este dolor no es mío", Hablando Contigo (Blog), Contacto, y una página de agradecimiento tras el pago), contenido de autoconocimiento sin diagnóstico (perfiles de Eneatipos/Heridas y un cuestionario privado de autoindagación de genealogía) y tres integraciones gratuitas opcionales (agenda de Google Calendar, CRM de HubSpot y envío de correos con EmailJS) que puedes activar tú mismo cuando quieras, sin tocar código más allá de pegar una llave.

**Novedades de esta versión:**
- **Nueva página "Sanando con mi Linaje"** (`genealogia.html`): la puerta de entrada al taller de genealogía en una sola página — explica de qué se trata, muestra los datos del taller (fecha, horario, modalidad, cupo, costo), un adelanto de los 6 temas con enlaces a la guía completa, y **el Test de Genealogía embebido directamente** (con el mismo registro y descarga de respuestas). La guía completa (`taller-genealogia.html`) y el test como página independiente (`test-genealogia.html`) se mantienen también, por si prefieres enlazarlos por separado.
- **Nuevo blog "Hablando Contigo"** (`hablando-contigo.html`): artículos propios sobre constelaciones familiares, aceptología, heridas de la infancia, genealogía y autocuidado, organizados en tarjetas desplegables con filtro por tema. Viene con 5 publicaciones de ejemplo ya escritas, listas para publicar — ver sección 3.4 para cómo agregar publicaciones nuevas. La página de Inicio también tiene una vitrina con las 3 publicaciones más recientes.
- **Página "Sobre la marca" renombrada a "Nuestro Propósito"** en el menú, el pie de página y el título de la pestaña — el archivo sigue llamándose `sobre-la-marca.html` para no romper enlaces existentes, solo cambió el nombre visible.
- **Se eliminó por completo el test que "descifraba" el Eneatipo y la Herida de la Infancia** (el que sumaba puntos y daba un resultado con porcentaje). En su lugar, `descubrete.html` es ahora una página breve con tarjetas hacia el nuevo **Taller Reconócete — Módulo III** (`reconocete-modulo-3.html`): un contenido educativo, sin diagnóstico, con los 9 eneatipos y las 5 heridas de la infancia explicados en tres etapas (infancia, juventud, adultez), por qué se construyó cada patrón, y un ejercicio de resignificación por meditación y observación consciente para cada uno.
- **Nuevo Test de Genealogía** (`test-genealogia.html`): un cuestionario privado de autoindagación basado en las preguntas del taller "Sanando con mi Linaje", agrupado por los 6 temas del taller. No se puntúa ni da un "resultado" — las respuestas se escriben en textos libres, se quedan solo en el navegador de la persona, y al final se pueden **descargar en un archivo .txt**. Para acceder hay que registrarse antes (nombre y correo), igual que el Club de Membresía.
- **Nueva página del Taller de Genealogía** (`taller-genealogia.html`): la guía completa del taller "Sanando con mi Linaje" solo con las explicaciones (objetivo, acuerdos de grupo, cómo se construye el árbol genealógico, los 6 temas con sus ejercicios de Objetivo/Procedimiento, el cierre del taller y las Frases Sanadoras completas) — **sin ninguna de las preguntas de autoindagación**, que viven únicamente en el Test de Genealogía.
- **Nueva página de referencia del libro "Este dolor no es mío"**, de Mark Wolynn (`libro-este-dolor-no-es-mio.html`): resume las dos ideas del libro que sostienen el Tema 2 del taller (epigenética del trauma heredado y "lenguaje nuclear"), con la cita bibliográfica completa.
- Logo actualizado con el archivo de marca que enviaste (ícono de mandala/destello, en el header, favicon e ícono de Apple).
- **Agenda y pago** rediseñada: cada taller se muestra en una tarjeta con ícono distintivo, descripción completa de en qué consiste y una lista de lo que incluye.
- Cada registro (Club de Membresía o Test de Genealogía) se guarda automáticamente como contacto nuevo en un **CRM gratuito (HubSpot)**, una vez lo actives (ver sección 6).
- Nueva página **Club de Membresía** (`membresia.html`): quienes ya compraron un taller pueden registrarse para acceder a libros descargables, artículos de blog y grabaciones de talleres, y reciben un correo de bienvenida y confirmación de ingreso.

## 1. Cómo subirlo a Hostinger (5 minutos)

1. Entra a **hpanel.hostinger.com** e inicia sesión.
2. En el menú lateral busca **Archivos → Administrador de archivos**.
3. Abre la carpeta **`public_html`** de tu dominio (si ya tenías WordPress instalado ahí, primero vacíala o mueve su contenido a una subcarpeta de respaldo — publicar este sitio nuevo no funciona junto con una instalación de WordPress en la misma carpeta).
4. Verás en esta entrega un archivo comprimido `aprender-a-vivir-contigo-web.zip`. Súbelo a `public_html` con el botón **Subir** del Administrador de archivos.
5. Una vez subido, haz clic derecho sobre el .zip → **Extraer** (Extract), y elige extraer en la misma carpeta `public_html`.
6. Verifica que después de extraer, `public_html` contenga directamente `index.html`, la carpeta `assets/`, etc. (no una subcarpeta extra dentro). Si Hostinger creó una subcarpeta, mueve todo su contenido un nivel arriba, a `public_html`.
7. Ve a **SSL** en hPanel y confirma que el candado/HTTPS esté activo para el dominio (normalmente ya lo está).

Eso es todo — visita `https://tudominio.com` y el sitio ya está en vivo. No hay instalación, base de datos ni panel de administración: para cambiar un texto, edita el archivo `.html` correspondiente directamente desde el Administrador de archivos de Hostinger (botón **Editar**) o desde tu computador y vuelve a subirlo.

### Alternativa: subir por FTP
Si prefieres FTP (por ejemplo con FileZilla), los datos están en hPanel → **Archivos → Cuentas FTP**. Sube todo el contenido de esta carpeta (no la carpeta misma, sino lo que hay dentro) a `public_html`.

## 2. Qué contiene cada cosa

```
index.html                     → Inicio
sobre-la-marca.html            → Nuestro Propósito (el archivo conserva el nombre antiguo; el título visible ya dice "Nuestro Propósito")
talleres.html                  → Talleres (fichas + cómo se vive el taller)
descubrete.html                 → Teaser de autoconocimiento: tarjetas hacia Reconócete Módulo III
reconocete-modulo-3.html        → Taller Reconócete Módulo III: los 9 eneatipos y las 5 heridas, con etapas, porqué y ejercicio de resignificación (sin preguntas, sin puntaje)
genealogia.html                 → "Sanando con mi Linaje": página frontal del taller de genealogía — descripción, datos del taller, adelanto de los 6 temas y el Test de Genealogía embebido
taller-genealogia.html          → Guía completa del taller "Sanando con mi Linaje" (solo explicaciones, sin preguntas)
test-genealogia.html            → Test de Genealogía: cuestionario privado de autoindagación (con registro obligatorio), descargable en .txt
libro-este-dolor-no-es-mio.html → Referencia y resumen del libro de Mark Wolynn citado en el Tema 2 del taller de genealogía
hablando-contigo.html           → Blog "Hablando Contigo": artículos propios, con filtro por tema
terapias-grupales.html        → Tabla de precios y medios de pago
agenda-te.html                     → Agenda y pago en línea (Google Calendar + Wompi)
membresia.html                   → Club de Membresía (contenido descargable)
gracias.html                     → Página de agradecimiento tras un pago exitoso
contacto.html                    → Datos de contacto + formulario
tienda.html                      → Versión anterior de la tienda (sin usar; sustituida por agenda-te.html)

assets/css/style.css            → Todos los estilos (colores, tipografías, layout)
assets/js/main.js               → Menú móvil + formulario de contacto
assets/js/reconocete.js          → Contenido de los 9 eneatipos y las 5 heridas (Módulo III)
assets/js/reconocete-render.js   → Dibuja las tarjetas del Módulo III en reconocete-modulo-3.html
assets/js/descubrete-teaser.js   → Dibuja las tarjetas breves de descubrete.html, enlazando al Módulo III
assets/js/genealogia.js          → Contenido explicativo del taller de genealogía (sin preguntas)
assets/js/genealogia-render.js   → Dibuja taller-genealogia.html a partir de genealogia.js
assets/js/genealogia-test.js     → Preguntas del Test de Genealogía, agrupadas por tema
assets/js/genealogia-test-render.js → Dibuja test-genealogia.html y el test embebido en genealogia.html, arma el .txt descargable
assets/js/blog-posts.js          → Publicaciones del blog "Hablando Contigo" (título, categoría, fecha, resumen, párrafos)
assets/js/blog-render.js         → Dibuja las tarjetas de hablando-contigo.html y el filtro por tema
assets/js/registro.js            → Registro previo (Club de Membresía y Test de Genealogía, incluido el embebido en genealogia.html) + CRM (HubSpot) + correo de bienvenida (EmailJS)
assets/js/catalogo.js            → Lista única de talleres/sesiones (nombre, precio, enlace de calendario)
assets/js/agenda.js              → Lógica de agenda-te.html (calendario + carrito + Wompi)
assets/js/recursos.js            → Lista única del contenido del Club de Membresía
assets/js/membresia.js           → Lógica de membresia.html (muestra el contenido tras registrarse)
assets/js/tienda.js              → Lógica de la tienda anterior (ya no enlazada, se deja de respaldo)
assets/img/                      → Logo, íconos de redes/WhatsApp, diagramas
```

Todas las páginas comparten el mismo header y footer — si más adelante agregas una página nueva, copia el header/footer de cualquiera de estas para mantener el mismo menú y pie de página.

## 3. Autoconocimiento: Reconócete Módulo III y Test de Genealogía

Ninguno de los dos es un test que se puntúa ni que arroja un "resultado" — ambos son contenido de autoobservación pensado para acompañar, no para diagnosticar.

### 3.1 Taller Reconócete — Módulo III (`reconocete-modulo-3.html`)

Presenta los **9 eneatipos** y las **5 heridas de la infancia** como tarjetas desplegables (haz clic para abrir cada una). Cada tarjeta explica: cómo se comporta ese patrón en la infancia, la juventud y la adultez; por qué se construyó; y un ejercicio de resignificación por meditación y observación consciente. `descubrete.html` es la puerta de entrada: muestra tarjetas breves de cada eneatipo/herida que enlazan directo a su ficha completa en el Módulo III (por ejemplo, `reconocete-modulo-3.html#eneatipo-5` abre automáticamente esa tarjeta).

Si quieres cambiar el texto de un eneatipo o una herida, ábrelo en `assets/js/reconocete.js` — cada uno es un bloque de texto simple, fácil de ubicar con Ctrl+F.

### 3.2 Test de Genealogía (`test-genealogia.html`)

Un cuestionario privado de autoindagación basado en las preguntas del taller "Sanando con mi Linaje", agrupado por los 6 temas del taller (sexualidad y embarazo, conflictos intergeneracionales, secretos familiares, lealtades transgeneracionales, liberación de patrones, y el cierre integrador). Cada pregunta tiene un cuadro de texto libre.

- **Requiere registro previo** (nombre y correo, igual que el Club de Membresía) — el cuestionario solo se desbloquea después de registrarse. El registro se guarda en el CRM (ver sección 6.1); no se envía ningún correo con "resultados", porque el test no se puntúa.
- Las respuestas **nunca se envían a ningún servidor**: se escriben y se quedan en el navegador de la persona.
- Al final hay un botón **"Descargar mis respuestas"** que genera un archivo `.txt` con todas las preguntas y respuestas, para llevar al taller o guardar.
- La página enlaza a `libro-este-dolor-no-es-mio.html`, que explica el trasfondo del libro de Mark Wolynn citado en el Tema 2 (epigenética y "lenguaje nuclear").

Si quieres cambiar una pregunta, ábrela en `assets/js/genealogia-test.js`.

### 3.3 Guía del Taller de Genealogía (`taller-genealogia.html`)

La misma guía de facilitación del taller "Sanando con mi Linaje", pero **solo con las explicaciones**: objetivo general, acuerdos de grupo, cómo se construye el árbol genealógico, los 6 temas con sus ejercicios (Objetivo + Procedimiento), el cierre del taller y las Frases Sanadoras completas (con la meditación de diálogo imaginado). No incluye ninguna pregunta de autoindagación — esas viven solo en el Test de Genealogía (sección 3.2), para no repetir el mismo contenido dos veces. El contenido vive en `assets/js/genealogia.js`.

### 3.4 "Sanando con mi Linaje" (`genealogia.html`) — página frontal del taller

Esta página reúne en un solo lugar lo que antes estaba repartido en dos: una presentación del taller (qué es, datos de fecha/horario/modalidad/cupo/costo, un adelanto de los 6 temas) **y el Test de Genealogía embebido** al final de la misma página, con el mismo registro previo y el mismo botón de descarga en `.txt` que tiene `test-genealogia.html`. Úsala como el enlace principal para promocionar el taller (por ejemplo en redes o WhatsApp) — desde ahí la persona puede leer, inscribirse y hacer el test sin salir de la página. Los enlaces a la guía completa (`taller-genealogia.html`) y al libro de Mark Wolynn siguen disponibles al final. No necesitas editar nada aparte para que esto funcione: reutiliza automáticamente `assets/js/genealogia-test.js`, `genealogia-test-render.js` y `registro.js`.

### 3.5 Blog "Hablando Contigo" (`hablando-contigo.html`)

Un blog sencillo, sin base de datos: cada publicación es un bloque de texto dentro de `assets/js/blog-posts.js`. Viene con 5 artículos de ejemplo ya escritos (Constelaciones Familiares, Aceptología, Heridas de la infancia, Genealogía, Autocuidado y límites), listos para publicar tal cual o para que los reemplaces por los tuyos.

**Para agregar una publicación nueva:**
1. Abre `assets/js/blog-posts.js`.
2. Copia un bloque completo (desde `{` hasta `},`) y pégalo al principio o al final del arreglo `window.BLOG_POSTS`.
3. Cambia el `slug` (identificador único, sin tildes ni espacios — se usa en el enlace directo `hablando-contigo.html#post-tu-slug`), la `categoria`, el `titulo`, la `fecha`, el `resumen` (aparece como adelanto en la tarjeta cerrada) y `cuerpo` (una lista de párrafos — cada línea entre comillas es un párrafo del artículo).
4. Guarda el archivo y vuelve a subirlo a Hostinger — no hace falta tocar ningún otro archivo. Los filtros por categoría y el contador de publicaciones se actualizan solos.

Si no quieres editar el archivo tú mismo, también puedes pedirle a Claude que agregue la publicación por ti, pasándole el texto del artículo.

## 4. El formulario de contacto

Como el sitio no tiene servidor propio (es HTML puro, no WordPress), el formulario de la página Contacto arma un mensaje de WhatsApp con los datos que la persona escribió y lo abre listo para enviar — así los mensajes llegan directo a tu WhatsApp de negocio (317 643 5131), que es el mismo canal que ya usas para inscripciones. También dejamos un enlace directo a `talleres@aprenderavivircontigo.com` para quien prefiera escribir por correo.

**Si más adelante quieres que el formulario envíe un correo automático** (como hacía WPForms en la versión WordPress) sin tener que programar nada, Hostinger permite activar PHP en el hosting y hay servicios gratuitos como Web3Forms o Formspree que reciben el formulario y lo remiten a tu correo — si llegado el momento quieres esa función, dímelo y lo agrego.

## 5. Agenda y pago en línea (`agenda-te.html`) — activar Google Calendar y Wompi

La página **Agenda y pago** deja que cada persona (1) elija el día y hora exactos en tu calendario real, y (2) pague en línea para confirmar su cupo. Usa el calendario de Google directamente — no un servicio externo — así nunca hay riesgo de doble reserva con tu agenda personal o de negocio.

### 5.1 Crear un horario de "Programación de citas" por cada taller/sesión

1. Entra a **calendar.google.com** con la cuenta de Google del negocio.
2. Haz clic en **Crear → Más opciones → Programación de citas** (o el botón **+** junto a "Otros calendarios" según la versión).
3. Configura un horario por cada taller o sesión (por ejemplo "Constelación Virtual — Asunto específico"): día(s) y franja horaria disponible, duración de cada cita, y cupos si aplica.
4. Guarda y haz clic en **Compartir → Copiar enlace de reserva**. Ese enlace se ve así: `https://calendar.google.com/calendar/appointments/schedules/AcZ...`
5. Repite esto por cada taller/sesión que quieras agendar en línea.

### 5.2 Pegar cada enlace en el catálogo

Abre `assets/js/catalogo.js` y busca la entrada del taller correspondiente, por ejemplo:

```js
{ id: 'linaje', nombre: 'Sanando con mi Linaje', ..., calendarUrl: null }
```

Reemplaza `calendarUrl: null` por tu enlace real:

```js
{ id: 'linaje', nombre: 'Sanando con mi Linaje', ..., calendarUrl: 'https://calendar.google.com/calendar/appointments/schedules/AcZ...' }
```

Mientras un taller tenga `calendarUrl: null`, en `agenda-te.html` se muestra un botón de WhatsApp en su lugar ("Escríbenos para coordinar horario") en vez de un botón roto — así nunca hay una experiencia rota mientras vas activando cada uno.

**Agregar un taller nuevo en el futuro**: solo agrega una entrada nueva a la lista de `catalogo.js` (copiando el formato de las que ya existen) — aparece automáticamente tanto en `agenda-te.html` como en la tabla de precios si la agregas también ahí.

### 5.3 Activar Wompi (pagos en línea)

Ahora mismo el botón "Pagar con Wompi" está configurado con una llave de prueba, así que si alguien intenta pagar, el sitio le avisa que falta configurar la llave y lo invita a escribir por WhatsApp — nadie puede pagarte por error mientras no actives esto.

**¿Por qué Wompi?** Es la pasarela de pagos de Bancolombia, pensada para negocios colombianos. La recomiendo sobre otras (Mercado Pago, ePayco, PayU) porque: acepta tarjeta, PSE **y Nequi** en un solo checkout (ya usas Nequi, así que tus clientes no notan diferencia), su "Widget de Checkout" se activa pegando un bloque de código con tu llave pública — sin necesidad de programar un backend ni contratar un desarrollador — y no cobra mensualidad, solo una comisión por transacción aprobada.

**Cómo activarlo (10-15 minutos):**
1. Crea tu cuenta de comercio en **https://comercios.wompi.co** con los datos de Aprender a Vivir Contigo (esto lo haces tú directamente en el sitio de Wompi — verificación de identidad/negocio incluida).
2. Una vez aprobada la cuenta, ve a **Configuración → Llaves API** y copia tu **llave pública** (empieza con `pub_` — hay una de pruebas `pub_test_...` y una real `pub_prod_...`).
3. Abre `assets/js/agenda.js` (y si sigues usando la tienda anterior, también `assets/js/tienda.js`) y busca la línea:
   ```js
   var WOMPI_PUBLIC_KEY = 'pub_test_REEMPLAZAR_CON_TU_LLAVE';
   ```
   Reemplázala por tu llave real, por ejemplo `var WOMPI_PUBLIC_KEY = 'pub_prod_xxxxxxxxxxxx';` y guarda el archivo.
4. Prueba el flujo completo tú mismo (agenda una cita, agrega el taller al carrito, haz clic en "Pagar con Wompi") antes de anunciarlo a tus clientes.
5. Personaliza `gracias.html` si quieres cambiar el mensaje que ve la persona justo después de pagar (ya está lista y enlazada por defecto, e incluye una invitación a entrar al Club de Membresía).

**Nota honesta:** antes de recibir pagos reales, compara los nombres de los atributos del widget (`data-public-key`, `data-amount-in-cents`, etc.) contra la documentación oficial vigente en **docs.wompi.co** — las pasarelas de pago a veces actualizan su API y no tengo forma de verificarlo en vivo desde aquí. Si prefieres una integración con confirmación automática de pago (webhook) en vez de solo el botón, eso sí requiere un pequeño script en el servidor (Hostinger soporta PHP) — dime si lo quieres y te lo agrego como siguiente paso.

**Alternativas** si Wompi no te convence: Mercado Pago (Checkout Pro) es igual de simple de activar y muy conocido por los usuarios, pero su comisión suele ser un poco más alta; ePayco y PayU también aceptan Nequi/PSE pero su panel de configuración es menos intuitivo.

## 6. Registro obligatorio, correo automático y CRM gratuito

Esto conecta tres piezas: los formularios de registro (en `membresia.html` y en `test-genealogia.html`), un CRM gratuito donde quedan guardados todos los contactos, y el envío automático de correos. Las tres viven en un solo archivo, `assets/js/registro.js`, con instrucciones al inicio del archivo también.

**Mientras no actives nada de esto, el sitio sigue funcionando igual**: el registro simplemente no se guarda en ningún lado externo (solo en el navegador de la persona mientras dura su visita) y no se envían correos — no se rompe nada por dejarlo pendiente.

### 6.1 CRM gratuito (HubSpot) — dónde quedan guardados los registros

1. Crea tu cuenta gratuita en **https://www.hubspot.com/products/crm** (el plan gratuito no tiene límite de tiempo ni de contactos para lo que necesitas aquí).
2. Dentro de HubSpot, ve a **Marketing → Formularios** (o **Legacy tools → Form** según la versión) y crea un formulario nuevo con los campos **Nombre**, **Apellido**, **Correo electrónico** y **Teléfono**.
3. Con el formulario creado, haz clic en **Compartir → Insertar código** (Embed) — ahí verás dos códigos que necesitas: el **Portal ID** y el **Form ID** (o "Form GUID").
4. Abre `assets/js/registro.js` y busca estas dos líneas al inicio:
   ```js
   var HUBSPOT_PORTAL_ID = 'REEMPLAZAR_PORTAL_ID';
   var HUBSPOT_FORM_ID = 'REEMPLAZAR_FORM_ID';
   ```
   Reemplaza los dos valores por los tuyos y guarda el archivo.

Desde ese momento, cada persona que se registre — ya sea en el Test de Genealogía o para entrar al Club de Membresía — aparece automáticamente como un contacto nuevo en tu HubSpot. Como HubSpot guarda también la página exacta desde la que se registró ("First conversion page"), puedes filtrar en HubSpot quién se registró desde cada página sin que tengamos que programar nada adicional.

### 6.2 Correo automático (EmailJS) — bienvenida al Club de Membresía

EmailJS envía correos reales usando tu propio Gmail/Outlook como remitente, sin necesidad de servidor. Se usa **una sola plantilla**, para la bienvenida al Club de Membresía — el registro del Test de Genealogía no envía correo, porque el test no se puntúa ni da un "resultado" que enviar.

1. Crea tu cuenta gratuita en **https://www.emailjs.com** (el plan gratuito incluye 200 correos al mes, suficiente para empezar).
2. Ve a **Email Services → Add New Service** y conecta tu Gmail o el correo que uses para el negocio (`talleres@aprenderavivircontigo.com` si es una cuenta de Google Workspace, o un Gmail normal). Copia el **Service ID** que te genera.
3. Ve a **Email Templates → Create New Template** y crea una plantilla de bienvenida al club, con las variables `{{nombre}}` y `{{correo}}`, redactando el mensaje de bienvenida y confirmación de ingreso al club que quieras que reciba la persona. Copia su **Template ID**.
4. Ve a **Account → General** y copia tu **Public Key**.
5. Abre `assets/js/registro.js` y busca estas líneas:
   ```js
   var EMAILJS_SERVICE_ID = 'REEMPLAZAR_SERVICE_ID';
   var EMAILJS_TEMPLATE_BIENVENIDA_ID = 'REEMPLAZAR_TEMPLATE_BIENVENIDA_ID'; // plantilla de bienvenida al club
   var EMAILJS_PUBLIC_KEY = 'REEMPLAZAR_PUBLIC_KEY';
   ```
   Reemplaza los tres valores por los tuyos y guarda el archivo.

Con esto activo: al registrarse en `membresia.html`, la persona recibe automáticamente el correo de bienvenida y confirmación de ingreso al club. Al registrarse en `test-genealogia.html` solo se guarda el contacto en el CRM (sección 6.1) y se desbloquea el cuestionario — no se envía correo.

## 7. Club de Membresía (`membresia.html`) — contenido descargable y cómo agregarlo desde WordPress

Esta página es para las personas que ya compraron un taller o sesión: se registran con el mismo nombre y correo de su compra y quedan con acceso a libros descargables, artículos de blog y grabaciones de talleres anteriores.

**Nota honesta e importante:** como el sitio es HTML puro (sin servidor ni base de datos), este registro es una "puerta" pensada para organizar la experiencia — no es una restricción de seguridad real. Cualquier persona que conozca el enlace directo de un recurso podría abrirlo sin registrarse. Para la mayoría de negocios pequeños esto es suficiente (el objetivo es dar valor agregado a quien ya te compró, no bloquear con contraseña como un banco), pero si más adelante quieres que el contenido esté realmente protegido por usuario y contraseña, eso sí requiere un sistema con servidor y base de datos (ahí WordPress con un plugin de membresías, o una plataforma como Hotmart/Teachable, empieza a tener sentido) — dime si llegas a ese punto y lo planeamos juntos.

### 7.1 Cómo agregar contenido nuevo (usando WordPress solo como "biblioteca")

Como decidiste no usar WordPress para todo el sitio, la forma más simple de que **tú** puedas subir libros, artículos y grabaciones sin tocar código es usar un WordPress aparte, gratis, únicamente como bodega de contenido — el sitio principal sigue siendo este HTML:

1. Crea un blog gratis en **https://wordpress.com** (plan gratuito) — por ejemplo `aprenderavivircontigo.wordpress.com` — o, si prefieres, un WordPress en un subdominio de tu propio hosting (`blog.aprenderavivircontigo.com`) usando el instalador de WordPress de Hostinger (ahí si aplica instalar WordPress, porque no reemplaza tu sitio principal). No hace falta que ese blog se vea bonito ni que lo anuncies — es solo para alojar archivos y textos.
2. **Para un libro o guía en PDF**: en el escritorio de WordPress ve a **Medios → Añadir nuevo**, sube el PDF, ábrelo desde la biblioteca de medios y copia la URL del archivo (termina en `.pdf`).
3. **Para un artículo de blog**: crea una **Entrada** nueva en WordPress, escribe el artículo, publícalo, y copia el enlace de la entrada publicada.
4. **Para una grabación de taller**: sube el video como entrada en WordPress (los planes pagos permiten subir video directo) o, más simple y sin costo, súbelo a YouTube en modo **Oculto** ("Unlisted") y copia ese enlace — cualquiera de las dos opciones funciona igual de bien para este propósito.
5. Con el enlace copiado, abre `assets/js/recursos.js` en este sitio y agrega o edita una entrada, por ejemplo:
   ```js
   {
     id: 'libro-reconocete',
     tipo: 'libro',                 // 'libro', 'blog' o 'taller'
     titulo: 'Reconócete: guía introductoria al Eneagrama',
     descripcion: 'Un e-book corto para profundizar en los eneatipos del Taller Reconócete — Módulo III.',
     url: 'https://aprenderavivircontigo.wordpress.com/wp-content/uploads/.../reconocete.pdf'
   }
   ```
   Guarda el archivo y sube de nuevo el sitio a Hostinger (o solo ese archivo, reemplazando el que ya está en `public_html/assets/js/`). El nuevo recurso aparece automáticamente en `membresia.html`, agrupado según el `tipo` que le pusiste.

Mientras un recurso tenga `url: null`, se muestra como "Próximamente" en la página — así puedes ir dejando la lista completa desde ya e ir activando cada uno a medida que subas el contenido real.

### 7.2 Cómo se conecta con el CRM

Los formularios de `membresia.html` y `test-genealogia.html` usan el mismo `registro.js` (ver sección 6.1) — así que en cuanto actives HubSpot, cada persona que entre al club o haga el Test de Genealogía queda también como contacto en tu CRM, y puedes distinguir en HubSpot el origen de cada registro (por la página desde la que se registró) para, por ejemplo, armar una lista o campaña de correo solo para miembros del club o solo para quien hizo el test.

## 8. Pendientes de contenido

- **Taller "Regresando al Vientre"**: falta el folleto correcto (el que llegó tenía el contenido de "Sanando con mi Linaje" duplicado). Mientras tanto, la tarjeta dice "Fecha por confirmar" e invita a escribir por WhatsApp. Actualiza `index.html` y `talleres.html` cuando tengas los datos reales.
- **Enlaces de "Programación de citas" de Google Calendar**: los 5 talleres/sesiones en `assets/js/catalogo.js` tienen `calendarUrl: null`, así que en `agenda-te.html` el botón "Ver horarios y agendar" no aparece todavía — en su lugar se muestra el aviso "Agenda de este taller próximamente, escríbenos por WhatsApp" (a propósito, para no mostrar nunca un enlace roto). Sigue la sección 5.1–5.2 para activarlo en cada taller.
- **Enlace de Facebook**: usé `https://www.facebook.com/aprenderavivircontigo` como URL de la página — verifica que sea la URL real de tu página de Facebook y corrígela en el footer de las páginas si es distinta (búscala con Ctrl+F por "facebook.com").
- **Dato de "5 horas"** en la tabla de precios (Constelación Grupal Virtual): quedó marcado con asterisco porque el horario indicado (7 a 9 p.m.) corresponde a 2 horas, no a 5 — confirma el dato correcto con tu equipo y actualiza `terapias-grupales.html` y `assets/js/catalogo.js`.
- **Llave de Wompi**: mientras no la actives (ver sección 5.3), el botón de pago de `agenda-te.html` no procesa pagos reales — solo muestra un aviso e invita a escribir por WhatsApp.
- **Enlaces de Google Calendar**: cada taller en `assets/js/catalogo.js` tiene `calendarUrl: null` — mientras no los pegues (ver sección 5.1–5.2), `agenda-te.html` muestra un botón de WhatsApp en su lugar.
- **CRM y correos (HubSpot/EmailJS)**: mientras no completes la sección 6, los registros no se guardan en ningún CRM externo y no se envían correos automáticos — el sitio sigue funcionando, solo sin esas dos funciones activas.
- **Contenido del Club de Membresía**: los 6 recursos de ejemplo en `assets/js/recursos.js` tienen `url: null` (aparecen como "Próximamente") — súbelos siguiendo la sección 7.1 cuando tengas el contenido listo.

## 9. SEO básico ya incluido

Cada página ya tiene título y meta descripción optimizados (lo que Google muestra en los resultados de búsqueda). Si quieres afinarlos más adelante, están en las primeras líneas de cada archivo `.html`, dentro de las etiquetas `<title>` y `<meta name="description">`.

## 10. Archivos de marca

`assets/img/logo.png` es el logo (mandala dorado sobre fondo navy) usado en el header y footer, y también es la base del favicon (el ícono que aparece en la pestaña del navegador) y del ícono para dispositivos Apple. Si más adelante tienes una versión distinta del logo o fotos reales de talleres pasados, reemplaza las imágenes en `assets/img/` conservando el mismo nombre de archivo para que se actualicen automáticamente en todas las páginas.

---
Este sitio reemplaza la ruta de WordPress + Hostinger de la guía anterior por una versión más simple: sin plugins, sin actualizaciones de WordPress que mantener, y sin mensualidad de constructor de páginas — solo archivos que copias una vez y quedan funcionando. El único lugar donde WordPress vuelve a aparecer es como "bodega" opcional de contenido del Club de Membresía (sección 7), que es un uso mucho más simple que mantener un sitio completo en WordPress.
