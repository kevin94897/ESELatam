<?php
/**
 * Campos del CPT "producto": la ficha completa (litrajes, colores,
 * características, certificados) registrada por PHP, como el resto del theme.
 *
 * Sustituye a inc/acf-productos.php. Tres diferencias con aquella versión:
 *
 *   1. **Validaciones.** Lo que la ficha necesita para verse bien va marcado
 *      como obligatorio, los repetidores tienen mínimos y máximos, y los
 *      archivos limitan su tipo. Antes se podía guardar un producto sin
 *      litrajes y la tarjeta salía con un guion.
 *   2. **Relaciones en vez de texto suelto.** Los sellos y los sectores ya no
 *      se escriben a mano: se eligen de sus módulos, así que el nombre y el
 *      logo salen siempre iguales y se pueden recorrer en los dos sentidos.
 *   3. **Agrupación por pestañas** numeradas como los pasos del configurador
 *      ("1. Modelos", "2. Litraje", "3. Selección de color", "4. Fotos"),
 *      para que el cliente reconozca qué controla cada campo en el frontend.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

add_action('acf/init', static function (): void {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    $campo = 'ese_latam_campo_def';
    $tab   = 'ese_latam_campo_tab';

    // Formato de todas las fotos del producto: se pintan sobre el hero azul
    // ajustadas al alto (hasta 460 px), así que el fondo tiene que ser
    // transparente y el lienzo, pegado al contorno del producto.
    $formato_foto = __('PNG o WebP con fondo transparente · 1200×1200 px · máx. 300 KB. Se muestra entera, ajustada al alto: recorta el lienzo al contorno del producto para que no se vea pequeño.', 'ese-latam');

    acf_add_local_field_group([
        'key'      => 'group_producto_ficha',
        'title'    => __('Ficha del producto', 'ese-latam'),
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'producto']]],
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => __('El nombre del producto sale del título y la categoría, de “Categorías de producto”. La imagen destacada es la foto de respaldo cuando un color no tiene foto propia.', 'ese-latam'),
        'fields'                => [

            /* ---------------- General ---------------- */
            $tab('prod_general', __('General', 'ese-latam')),
            $campo('textarea', 'descripcion_corta', __('Descripción corta bajo el nombre', 'ese-latam'), [
                'instructions' => __('Párrafo bajo el nombre del producto, en el panel izquierdo del hero de la ficha. Entre 80 y 320 caracteres. Si eliges un modelo con descripción propia, esta se reemplaza.', 'ese-latam'),
                'rows'         => 3,
                'required'     => 1,
                'maxlength'    => 320,
            ]),
            $campo('link', 'enlace_compra', __('Botón “Comprar” (texto y enlace)', 'ese-latam'), [
                'instructions' => __('Botón principal del hero de la ficha, junto a “Ficha técnica”. Escribe el texto del botón y la página a la que lleva. Vacío: dice “Comprar” y lleva a la página de Contacto.', 'ese-latam'),
                'wrapper'      => ['width' => '60'],
            ]),
            $campo('file', 'ficha_tecnica', __('Ficha técnica descargable (PDF)', 'ese-latam'), [
                'instructions'  => __('Se descarga desde el botón “Ficha técnica” del hero, junto a “Comprar”. Solo PDF, máx. 5 MB. Vacía: el botón se ve atenuado y no se puede pulsar.', 'ese-latam'),
                'return_format' => 'url',
                'mime_types'    => 'pdf',
                'wrapper'       => ['width' => '40'],
            ]),

            /* ---------------- 1. Modelos ---------------- */
            $tab('prod_modelos', __('1. Modelos', 'ese-latam')),
            $campo('message', '', __('Cómo funcionan los modelos', 'ese-latam'), [
                'key'      => 'field_prodmodelos_msg',
                'message'  => __('Cada producto del catálogo agrupa a los <strong>modelos</strong> de su familia: “Papeleras” contiene Open Dinova, Campus Goool, Venta… En la ficha aparecen como una primera lista de opciones, y al elegir uno se muestran solo sus capacidades, sus colores y sus fotos. Con un único modelo la lista no se muestra y la ficha pasa directo a elegir capacidad y color.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('repeater', 'modelos', __('Modelos de la familia', 'ese-latam'), [
                'instructions' => __('Botones “Elige el modelo” del panel del hero. Un renglón por modelo, hasta 24. El orden es el de la lista en la ficha; el primero es el que se muestra al abrir. Vacío: la ficha no muestra la lista de modelos.', 'ese-latam'),
                'layout'       => 'table',
                'min'          => 0,
                'max'          => 24,
                'button_label' => __('Añadir modelo', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'nombre', __('Nombre del modelo', 'ese-latam'), [
                        'key'          => 'field_modelo_nombre',
                        'instructions' => __('Texto del botón del modelo. Hasta 60 caracteres. No repitas nombres: es el que eliges luego en las pestañas 2, 3 y 4.', 'ese-latam'),
                        'required'     => 1,
                        'maxlength'    => 60,
                        'wrapper'      => ['width' => '35'],
                    ]),
                    $campo('text', 'descripcion', __('Descripción propia del modelo', 'ese-latam'), [
                        'key'          => 'field_modelo_desc',
                        'instructions' => __('Reemplaza a la descripción corta del producto cuando se elige este modelo. Hasta 320 caracteres. Vacía: se mantiene la descripción corta.', 'ese-latam'),
                        'maxlength'    => 320,
                        'wrapper'      => ['width' => '65'],
                    ]),
                ],
            ]),

            /* ---------------- 2. Litraje ---------------- */
            $tab('prod_litraje', __('2. Litraje', 'ese-latam')),
            $campo('repeater', 'litrajes', __('Capacidades del producto (litraje)', 'ese-latam'), [
                'instructions' => __('Pastillas “Elige la capacidad” del panel del hero. Entre 1 y 12. Marca una como predeterminada: es la que se muestra al abrir la ficha y la que sale en la tarjeta del catálogo. Si no marcas ninguna, se usa la primera.', 'ese-latam'),
                'layout'       => 'table',
                'min'          => 1,
                'max'          => 12,
                'required'     => 1,
                'button_label' => __('Añadir litraje', 'ese-latam'),
                'sub_fields'   => [
                    $campo('select', 'modelo', __('Modelo de la capacidad', 'ese-latam'), [
                        'key'          => 'field_litraje_modelo',
                        'instructions' => __('A qué modelo de la pestaña “1. Modelos” pertenece. Vacío: vale para todos los modelos.', 'ese-latam'),
                        'choices'      => [],
                        'allow_null'   => 1,
                        'wrapper'      => ['width' => '30'],
                    ]),
                    $campo('text', 'valor', __('Texto de la pastilla de capacidad', 'ese-latam'), [
                        'key'          => 'field_litraje_valor',
                        'instructions' => __('Como se lee en la pastilla: 120L. Hasta 12 caracteres.', 'ese-latam'),
                        'required'     => 1,
                        'maxlength'    => 12,
                    ]),
                    $campo('true_false', 'predeterminado', __('¿Capacidad que se muestra al abrir?', 'ese-latam'), [
                        'key'          => 'field_litraje_default',
                        'instructions' => __('Actívalo en una sola fila por modelo. Sin ninguna activa, se usa la primera.', 'ese-latam'),
                        'ui'           => 1,
                    ]),
                ],
            ]),

            /* ---------------- 3. Colores ---------------- */
            $tab('prod_colores', __('3. Selección de color', 'ese-latam')),
            $campo('repeater', 'colores', __('Colores disponibles del producto', 'ese-latam'), [
                'instructions' => __('Cada color es un círculo de “Selecciona el color”, bajo las pastillas de capacidad. La foto cambia al elegirlo. Hasta 12. Vacío: la ficha no muestra el selector de color.', 'ese-latam'),
                'layout'       => 'block',
                'min'          => 0,
                'max'          => 12,
                'button_label' => __('Añadir color', 'ese-latam'),
                'sub_fields'   => [
                    $campo('select', 'modelo', __('Modelo del color', 'ese-latam'), [
                        'key'          => 'field_color_modelo',
                        'instructions' => __('A qué modelo de la pestaña “1. Modelos” pertenece. Vacío: vale para todos los modelos.', 'ese-latam'),
                        'choices'      => [],
                        'allow_null'   => 1,
                        'wrapper'      => ['width' => '30'],
                    ]),
                    $campo('text', 'nombre', __('Nombre del color', 'ese-latam'), [
                        'key'          => 'field_color_nombre',
                        'instructions' => __('Como se lee en la ficha al elegir el color: Verde, Gris antracita… Hasta 40 caracteres.', 'ese-latam'),
                        'required'     => 1,
                        'maxlength'    => 40,
                        'wrapper'      => ['width' => '50'],
                    ]),
                    $campo('color_picker', 'color', __('Tono del círculo de color', 'ese-latam'), [
                        'key'          => 'field_color_hex',
                        'instructions' => __('Relleno del círculo que se pulsa para elegir el color. Usa el tono más parecido al del producto.', 'ese-latam'),
                        'required'     => 1,
                        'wrapper'      => ['width' => '50'],
                    ]),
                    $campo('image', 'imagen', __('Foto por defecto del color', 'ese-latam'), [
                        'key'           => 'field_color_img',
                        'instructions'  => __('Se usa cuando la capacidad elegida no tiene foto propia en la pestaña “4. Fotos”.', 'ese-latam') . ' ' . $formato_foto . ' ' . __('Vacía: se usa la imagen destacada del producto.', 'ese-latam'),
                        'return_format' => 'url',
                        'preview_size'  => 'medium',
                        'mime_types'    => 'png,webp',
                    ]),
                ],
            ]),

            /* ---------------- 4. Fotos ---------------- */
            $tab('prod_fotos', __('4. Fotos', 'ese-latam')),
            $campo('message', '', __('Cómo se elige la foto de la ficha', 'ese-latam'), [
                'key'      => 'field_prodfotos_msg',
                'message'  => __('Una fila por foto. Al elegir un color y una capacidad en la ficha se busca aquí la combinación exacta; si no existe, se usa la <strong>foto por defecto</strong> de ese color. Los desplegables de modelo, color y capacidad se llenan solos con lo que hayas cargado en las pestañas 1, 2 y 3: <strong>guarda el producto</strong> después de añadir un modelo, un color o un litraje para verlos aquí.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('repeater', 'fotos', __('Fotos por color y capacidad', 'ese-latam'), [
                'instructions' => __('La foto grande que flota sobre la isla, a la derecha del hero. La del primer color en la capacidad predeterminada es también la foto de la tarjeta del catálogo. Vacío: se usa la foto por defecto de cada color.', 'ese-latam'),
                'layout'       => 'table',
                'button_label' => __('Añadir foto', 'ese-latam'),
                'sub_fields'   => [
                    $campo('select', 'modelo', __('Modelo de la foto', 'ese-latam'), [
                        'key'          => 'field_foto_modelo',
                        'instructions' => __('A qué modelo de la pestaña “1. Modelos” pertenece. Vacío: vale para todos los modelos.', 'ese-latam'),
                        'choices'      => [],
                        'allow_null'   => 1,
                        'wrapper'      => ['width' => '25'],
                    ]),
                    $campo('select', 'color', __('Color de la foto', 'ese-latam'), [
                        'key'          => 'field_foto_color',
                        'instructions' => __('Uno de los colores de la pestaña “3. Selección de color”.', 'ese-latam'),
                        'required'     => 1,
                        'choices'      => [],
                    ]),
                    $campo('select', 'litraje', __('Capacidad de la foto', 'ese-latam'), [
                        'key'          => 'field_foto_litraje',
                        'instructions' => __('Una de las capacidades de la pestaña “2. Litraje”.', 'ese-latam'),
                        'required'     => 1,
                        'choices'      => [],
                    ]),
                    $campo('image', 'imagen', __('Foto del producto en esa combinación', 'ese-latam'), [
                        'key'           => 'field_foto_img',
                        'instructions'  => $formato_foto,
                        'required'      => 1,
                        'return_format' => 'url',
                        'preview_size'  => 'thumbnail',
                        'mime_types'    => 'png,webp',
                    ]),
                ],
            ]),

            /* ---------------- Características ---------------- */
            $tab('prod_caracteristicas', __('Características', 'ese-latam')),
            $campo('repeater', 'caracteristicas', __('Características en “Datos clave”', 'ese-latam'), [
                'instructions' => __('La tarjeta “Datos clave” bajo el hero muestra la capacidad, los colores y las dos primeras características (salta las que digan “volumen”). Hasta 8 filas. Con menos de dos, la plantilla completa con “Material: HDPE de alta densidad” y “Origen: Ingeniería europea”. Una fila cuya etiqueta contenga “Material” alimenta además la tarjeta del catálogo; sin ella, la tarjeta muestra un guion.', 'ese-latam'),
                'layout'       => 'table',
                'min'          => 0,
                'max'          => 8,
                'button_label' => __('Añadir característica', 'ese-latam'),
                'sub_fields'   => [
                    $campo('text', 'etiqueta', __('Nombre de la característica', 'ese-latam'), [
                        'key'          => 'field_caract_etiqueta',
                        'instructions' => __('La palabra en gris sobre el dato: Material, Origen… Hasta 30 caracteres.', 'ese-latam'),
                        'required'     => 1,
                        'maxlength'    => 30,
                    ]),
                    $campo('text', 'valor', __('Valor de la característica', 'ese-latam'), [
                        'key'          => 'field_caract_valor',
                        'instructions' => __('El dato en grande: HDPE de alta densidad. Hasta 60 caracteres.', 'ese-latam'),
                        'required'     => 1,
                        'maxlength'    => 60,
                    ]),
                ],
            ]),

            /* ---------------- Relaciones ---------------- */
            $tab('prod_relaciones', __('Relaciones', 'ese-latam')),
            $campo('message', '', __('Por qué sellos y sectores se eligen', 'ese-latam'), [
                'key'      => 'field_prodrel_msg',
                'message'  => __('Los sellos y los sectores no se escriben acá: se eligen de sus módulos. Así el logo y el texto de cada sello salen siempre iguales, y cambiar el nombre de un sector lo actualiza en todas las fichas a la vez.', 'ese-latam'),
                'esc_html' => 0,
            ]),
            $campo('relationship', 'certificaciones', __('Certificaciones del producto', 'ese-latam'), [
                'instructions'  => __('Los sellos de la tarjeta “Certificaciones”, junto a “Datos clave” bajo el hero, en el orden en que se elijan. Cada uno lleva a la página de Certificaciones. Hasta 8. Vacío: esa tarjeta no se muestra.', 'ese-latam'),
                'post_type'     => ['certificacion'],
                'filters'       => ['search'],
                'return_format' => 'id',
                'max'           => 8,
            ]),
        ],
    ]);
});

/**
 * Llena los desplegables de las pestañas "2. Litraje", "3. Selección de
 * color" y "4. Fotos" con los modelos, colores y litrajes que tenga cargados
 * ESE producto.
 *
 * Los subcampos de un repetidor pasan por el mismo filtro que el resto, así
 * que basta con reconocerlos por su key. Si el producto todavía no tiene
 * colores o litrajes guardados, el desplegable queda vacío y el aviso de la
 * pestaña explica que hay que guardar primero.
 */
add_filter('pcf/prepare_field', static function ($field) {
    if (! is_array($field) || ! isset($field['key'])) {
        return $field;
    }
    $modelos = ['field_litraje_modelo', 'field_color_modelo', 'field_foto_modelo'];
    $otros   = ['field_foto_color', 'field_foto_litraje'];

    if (! in_array($field['key'], $modelos, true) && ! in_array($field['key'], $otros, true)) {
        return $field;
    }

    $post_id = (int) get_the_ID();
    if ($post_id <= 0 || 'producto' !== get_post_type($post_id)) {
        return $field;
    }

    $opciones = [];

    if (in_array($field['key'], $modelos, true)) {
        foreach ((array) ese_latam_campo('modelos', $post_id, []) as $modelo) {
            $nombre = trim((string) ($modelo['nombre'] ?? ''));
            if ('' !== $nombre) {
                $opciones[$nombre] = $nombre;
            }
        }
    } elseif ('field_foto_color' === $field['key']) {
        foreach ((array) ese_latam_campo('colores', $post_id, []) as $color) {
            $nombre = trim((string) ($color['nombre'] ?? ''));
            if ('' !== $nombre) {
                $opciones[$nombre] = $nombre;
            }
        }
    } else {
        foreach ((array) ese_latam_campo('litrajes', $post_id, []) as $litraje) {
            $valor = trim((string) ($litraje['valor'] ?? ''));
            if ('' !== $valor) {
                $opciones[$valor] = $valor;
            }
        }
    }

    $field['choices'] = $opciones;

    return $field;
});
