<?php
/**
 * Menús del theme: el principal de la cabecera y los TRES del footer (uno
 * por columna).
 *
 * Se crean una sola vez con los links que hasta ahora estaban escritos en
 * header.php y footer.php, así el cliente los reordena desde Apariencia →
 * Menús sin tocar código. Mismo criterio que inc/paginas.php: idempotente,
 * anotado en una opción y sin volver a pisar lo que el cliente cambió.
 *
 * El footer va en tres menús separados —no en uno con separadores— porque
 * cada columna tiene su propio título y su propio orden: con un solo menú no
 * habría forma de mover un link de una columna a otra desde el admin.
 *
 * @package EseLatam
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Una fila del mega-submenú: miniatura + título + bajada.
 *
 * `$img` y `$desc` pueden venir vacíos —un item del menú que no sea un
 * sector, por ejemplo— y entonces la fila se pinta sin esa parte en vez de
 * dejar un hueco o un `<img>` roto.
 */
function ese_latam_nav_mega_fila(string $url, string $title, string $img = '', string $desc = ''): string {
    $html = '<li class="menu-item">';
    $html .= '<a class="nav-mega__item" href="' . esc_url($url) . '">';

    if ('' !== $img) {
        $html .= '<img class="nav-mega__thumb" src="' . esc_url($img) . '"'
            . ' alt="" width="48" height="48" loading="lazy" decoding="async">';
    }

    $html .= '<span class="nav-mega__text">';
    $html .= '<span class="nav-mega__title">' . esc_html($title) . '</span>';

    if ('' !== $desc) {
        $html .= '<span class="nav-mega__desc">' . esc_html($desc) . '</span>';
    }

    $html .= '</span></a></li>';

    return $html;
}

/**
 * Pie del panel: enlace a la página de sectores, a lo ancho de las dos
 * columnas.
 */
function ese_latam_nav_mega_pie(string $url_todos): string {
    return '<li class="menu-item nav-mega__all">'
        . '<a href="' . esc_url($url_todos) . '">' . esc_html__('Ver todos los sectores', 'ese-latam')
        . '<span class="nav-mega__all-icon" aria-hidden="true">'
        . '<svg width="14" height="12" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.7165 7.15792L9.95748 12.7276C9.77717 12.902 9.53261 13 9.2776 13C9.02259 13 8.77803 12.902 8.59772 12.7276C8.4174 12.5532 8.3161 12.3167 8.3161 12.0701C8.3161 11.8235 8.4174 11.587 8.59772 11.4126L12.7178 7.42945H0.959834C0.70527 7.42945 0.461133 7.33164 0.281129 7.15756C0.101125 6.98347 0 6.74736 0 6.50116C0 6.25496 0.101125 6.01885 0.281129 5.84476C0.461133 5.67067 0.70527 5.57287 0.959834 5.57287H12.7178L8.59932 1.58743C8.419 1.41304 8.3177 1.17652 8.3177 0.929896C8.3177 0.683272 8.419 0.44675 8.59932 0.27236C8.77963 0.0979708 9.02419 0 9.2792 0C9.5342 0 9.77877 0.0979708 9.95908 0.27236L15.7181 5.84208C15.8076 5.92843 15.8786 6.03104 15.9269 6.144C15.9753 6.25696 16.0001 6.37805 16 6.50032C15.9998 6.62259 15.9747 6.74362 15.9261 6.85647C15.8774 6.96933 15.8062 7.07177 15.7165 7.15792Z" fill="currentColor"/></svg>'
        . '</span></a></li>';
}

/**
 * Panel COMPLETO del mega-submenú (`<ul class="sub-menu nav-mega">`) con
 * todos los sectores publicados.
 *
 * Es el panel "automático": el que sale cuando el item "Sectores" del menú no
 * tiene hijos cargados a mano, y el que usa el fallback sin menú asignado
 * (ese_latam_nav_fallback). Si el cliente elige sectores como hijos del item,
 * el walker pinta esos en vez de este (ver ESE_Latam_Nav_Walker).
 *
 * @param string $url_todos Destino del pie "Ver todos los sectores".
 */
function ese_latam_nav_mega(string $url_todos): string {
    $sectores = ese_latam_sectores();
    if ([] === $sectores) {
        return '';
    }

    $html = '<ul class="sub-menu nav-mega">';

    foreach ($sectores as $sector) {
        $html .= ese_latam_nav_mega_fila(
            ese_latam_sector_url($sector),
            (string) $sector['title'],
            (string) $sector['img'],
            (string) $sector['desc']
        );
    }

    $html .= ese_latam_nav_mega_pie($url_todos);
    $html .= '</ul>';

    return $html;
}

/**
 * Datos de un sector por ID de su entrada, para las filas del panel armado a
 * mano. `null` si ese ID no es un sector publicado.
 *
 * @return array{id: int, slug: string, title: string, desc: string, img: string, url: string}|null
 */
function ese_latam_sector_por_id(int $id): ?array {
    static $indice = null;

    if (null === $indice) {
        $indice = [];
        foreach (ese_latam_sectores() as $sector) {
            $indice[(int) $sector['id']] = $sector;
        }
    }

    return $indice[$id] ?? null;
}

/**
 * ¿Este item del menú apunta a la página "Soluciones por sector"?
 *
 * @param WP_Post $item Item ya preparado por wp_setup_nav_menu_item().
 */
function ese_latam_nav_item_es_sectores(WP_Post $item): bool {
    if ('post_type' !== $item->type || 'page' !== $item->object) {
        return false;
    }

    static $id = null;
    if (null === $id) {
        $page = get_page_by_path('sectores');
        $id   = $page instanceof WP_Post ? $page->ID : 0;
    }

    return $id > 0 && (int) $item->object_id === $id;
}

/**
 * Walker del menú principal: el mega-submenú de "Sectores", con o sin hijos
 * cargados en el admin.
 *
 * Dos modos, según lo que el cliente haya hecho en Apariencia → Menús:
 *
 *  - Item "Sectores" CON hijos: se pintan esos, en ese orden, con el mismo
 *    formato de fila (miniatura + bajada). Si el hijo es una entrada del CPT
 *    `sector`, la foto y la bajada salen de su ficha; si es otra cosa (una
 *    página, un enlace suelto), queda solo el título.
 *  - Item "Sectores" SIN hijos: se inyecta el panel completo con todos los
 *    sectores publicados (ese_latam_nav_mega), que es el comportamiento de
 *    siempre y no obliga a armar el menú a mano.
 *
 * En los dos casos el `<li>` lleva la clase que el CSS y nav-submenu.ts
 * esperan (`menu-item-has-children`).
 */
class ESE_Latam_Nav_Walker extends Walker_Nav_Menu {
    /** URL del item padre del panel abierto, para el pie "Ver todos". */
    private string $url_padre = '';

    /** ¿El nivel que se está pintando es el panel de sectores? */
    private bool $en_mega = false;

    /**
     * ¿Este item es el de "Sectores" en un menú que admite submenús?
     *
     * Con `depth` 1 (el menú móvil) no hay submenús, así que no hay panel:
     * mismo criterio que aplica wp_nav_menu a los hijos reales.
     *
     * @param WP_Post|mixed $item
     * @param int           $depth
     * @param stdClass|null $args
     */
    private function es_sectores($item, $depth, $args): bool {
        if (0 !== $depth || ! $item instanceof WP_Post) {
            return false;
        }

        $max = isset($args->depth) ? (int) $args->depth : 0;
        if (0 !== $max && $max < 2) {
            return false;
        }

        return ese_latam_nav_item_es_sectores($item);
    }

    /**
     * @param string        $output
     * @param int           $depth
     * @param stdClass|null $args
     */
    public function start_lvl(&$output, $depth = 0, $args = null) {
        if (0 === $depth && $this->en_mega) {
            $output .= '<ul class="sub-menu nav-mega">';

            return;
        }

        parent::start_lvl($output, $depth, $args);
    }

    /**
     * @param string        $output
     * @param int           $depth
     * @param stdClass|null $args
     */
    public function end_lvl(&$output, $depth = 0, $args = null) {
        if (0 === $depth && $this->en_mega) {
            $output .= ese_latam_nav_mega_pie($this->url_padre) . '</ul>';

            return;
        }

        parent::end_lvl($output, $depth, $args);
    }

    /**
     * @param string        $output
     * @param WP_Post       $data_object
     * @param int           $depth
     * @param stdClass|null $args
     * @param int           $current_object_id
     */
    public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0) {
        // Fila del panel: reemplaza por completo al <li><a> del walker base.
        if (1 === $depth && $this->en_mega) {
            $sector = 'sector' === $data_object->object
                ? ese_latam_sector_por_id((int) $data_object->object_id)
                : null;

            $output .= ese_latam_nav_mega_fila(
                (string) $data_object->url,
                (string) $data_object->title,
                null !== $sector ? (string) $sector['img'] : '',
                null !== $sector ? (string) $sector['desc'] : ''
            );

            return;
        }

        if ($this->es_sectores($data_object, $depth, $args)) {
            // OJO: Walker::display_element deja `has_children` en el PROPIO
            // walker. En `$args` solo lo copia cuando los args son un array, y
            // wp_nav_menu los pasa como objeto: leerlo de ahí daba siempre
            // falso y salían los dos submenús a la vez (el de WP y el panel
            // automático).
            $this->en_mega   = (bool) $this->has_children;
            $this->url_padre = (string) $data_object->url;

            // Con hijos la clase ya la pone WordPress; sin hijos hay que
            // ponerla para que el panel automático tenga dónde colgarse.
            if (! $this->en_mega && '' !== ese_latam_nav_mega($this->url_padre)) {
                $data_object->classes   = is_array($data_object->classes) ? $data_object->classes : [];
                $data_object->classes[] = 'menu-item-has-children';
            }
        }

        parent::start_el($output, $data_object, $depth, $args, $current_object_id);
    }

    /**
     * @param string        $output
     * @param WP_Post       $data_object
     * @param int           $depth
     * @param stdClass|null $args
     */
    public function end_el(&$output, $data_object, $depth = 0, $args = null) {
        // Las filas del panel ya cerraron su propio <li> en start_el.
        if (1 === $depth && $this->en_mega) {
            return;
        }

        if ($this->es_sectores($data_object, $depth, $args)) {
            // Sin hijos en el admin, el panel automático va acá: antes que el
            // parent, que es quien cierra el `</li>`.
            if (! $this->en_mega) {
                $output .= ese_latam_nav_mega((string) $data_object->url);
            }

            $this->en_mega   = false;
            $this->url_padre = '';
        }

        parent::end_el($output, $data_object, $depth, $args);
    }
}

/**
 * Ubicación => nombre del menú y sus items, tal como se crean la primera vez.
 *
 * El NOMBRE del menú de cada columna del footer es el título que se pinta
 * arriba de esa columna (ver ese_latam_footer_columna), así que renombrar el
 * menú renombra la columna.
 *
 * Cada item se describe por su DESTINO y no por una URL suelta: `page` es el
 * slug de una página del theme (inc/paginas.php) y `archive`, el post type de
 * un archivo. Así WordPress guarda el item apuntando al objeto y el enlace
 * sigue sirviendo aunque después cambie el slug.
 *
 * @return array<string, array{name: string, items: list<array{label: string, page?: string, archive?: string}>}>
 */
function ese_latam_menus_base(): array {
    return [
        'primary' => [
            'name'  => __('Menú principal', 'ese-latam'),
            'items' => [
                ['label' => __('Productos', 'ese-latam'),       'archive' => 'producto'],
                ['label' => __('Sectores', 'ese-latam'),        'page'    => 'sectores'],
                ['label' => __('Certificaciones', 'ese-latam'), 'page'    => 'certificaciones'],
                ['label' => __('Impacto', 'ese-latam'),         'page'    => 'impacto'],
            ],
        ],
        'footer_1' => [
            'name'  => __('Soluciones', 'ese-latam'),
            'items' => [
                ['label' => __('Sectores', 'ese-latam'),        'page'    => 'sectores'],
                ['label' => __('Productos', 'ese-latam'),       'archive' => 'producto'],
                ['label' => __('Certificaciones', 'ese-latam'), 'page'    => 'certificaciones'],
                ['label' => __('Impacto', 'ese-latam'),         'page'    => 'impacto'],
            ],
        ],
        'footer_2' => [
            'name'  => __('ESE Latam', 'ese-latam'),
            'items' => [
                ['label' => __('Nosotros', 'ese-latam'),       'page'    => 'nosotros'],
                ['label' => __('Contacto', 'ese-latam'),       'page'    => 'contacto'],
                ['label' => __('Blog', 'ese-latam'),           'page'    => 'blog'],
                ['label' => __('Casos de éxito', 'ese-latam'), 'archive' => 'caso'],
                ['label' => __('Distribuidores', 'ese-latam'), 'page'    => 'distribuidores'],
            ],
        ],
        'footer_3' => [
            'name'  => __('Legal', 'ese-latam'),
            'items' => [
                ['label' => __('Políticas de privacidad', 'ese-latam'), 'page' => 'politicas-de-privacidad'],
                ['label' => __('Términos y condiciones', 'ese-latam'),  'page' => 'terminos-y-condiciones'],
            ],
        ],
    ];
}

/**
 * Traduce la descripción de un item a los argumentos de
 * wp_update_nav_menu_item(). Devuelve [] si el destino todavía no existe, y
 * ese item se saltea en vez de quedar como enlace roto.
 *
 * @param array{label: string, page?: string, archive?: string} $item
 * @return array<string, mixed>
 */
function ese_latam_menu_item_args(array $item): array {
    $base = [
        'menu-item-title'  => $item['label'],
        'menu-item-status' => 'publish',
    ];

    if (isset($item['page'])) {
        $page = get_page_by_path($item['page']);
        if (! $page instanceof WP_Post) {
            return [];
        }

        return $base + [
            'menu-item-type'      => 'post_type',
            'menu-item-object'    => 'page',
            'menu-item-object-id' => $page->ID,
        ];
    }

    if (isset($item['archive'])) {
        if (! post_type_exists($item['archive'])) {
            return [];
        }

        return $base + [
            'menu-item-type'   => 'post_type_archive',
            'menu-item-object' => $item['archive'],
        ];
    }

    return [];
}

/**
 * Crea los menús que falten y los asigna a su ubicación, una sola vez.
 *
 * Los items se cargan SOLO al crear el menú: si el cliente después borra uno,
 * no vuelve a aparecer. Y la ubicación se asigna solo si está vacía, para no
 * desplazar un menú que ya eligió. Si ya existe un menú con ese nombre, se
 * adopta tal como está en vez de duplicarlo.
 *
 * Corre en `init` con prioridad 13: después de ese_latam_asegurar_paginas()
 * (10) y de que el blog quede fijado (12), porque los items apuntan a esas
 * páginas y sin ellas creadas no habría destino que guardar.
 */
function ese_latam_asegurar_menus(): void {
    $hechos = get_option('ese_latam_menus', []);
    if (! is_array($hechos)) {
        $hechos = [];
    }

    $pendientes = array_diff_key(ese_latam_menus_base(), $hechos);
    if ([] === $pendientes) {
        return;
    }

    $ubicaciones = get_theme_mod('nav_menu_locations', []);
    if (! is_array($ubicaciones)) {
        $ubicaciones = [];
    }
    $asignar = false;

    foreach ($pendientes as $location => $datos) {
        $menu = wp_get_nav_menu_object($datos['name']);

        if (! $menu) {
            $id = wp_create_nav_menu($datos['name']);
            // Un error acá suele ser "ese nombre ya existe": otra carga lo
            // creó en el medio (el init corre en CADA request), así que se
            // adopta el que quedó en vez de abandonar la ubicación.
            $menu = wp_get_nav_menu_object(is_wp_error($id) ? $datos['name'] : $id);
        }

        if (! $menu) {
            continue;
        }

        $hechos[$location] = (int) $menu->term_id;
        // Se anota ANTES de cargar los items: si otra carga entra en paralelo,
        // encuentra la ubicación ya hecha y no vuelve a pasar por acá.
        update_option('ese_latam_menus', $hechos);

        // Y aun así los items se cargan solo en un menú VACÍO: es lo que hace
        // que dos cargas simultáneas no los dupliquen (pasó al crearlos por
        // primera vez) y que un item que el cliente borró no reaparezca.
        if (empty(wp_get_nav_menu_items($menu->term_id))) {
            foreach ($datos['items'] as $item) {
                $args = ese_latam_menu_item_args($item);
                if ([] !== $args) {
                    wp_update_nav_menu_item((int) $menu->term_id, 0, $args);
                }
            }
        }

        if (empty($ubicaciones[$location])) {
            $ubicaciones[$location] = (int) $menu->term_id;
            $asignar                = true;
        }
    }

    if ($asignar) {
        set_theme_mod('nav_menu_locations', $ubicaciones);
    }
}
add_action('init', 'ese_latam_asegurar_menus', 13);
add_action('after_switch_theme', 'ese_latam_asegurar_menus');

/**
 * Una columna de links del footer.
 *
 * Con un menú asignado a `$location`, el título de la columna es el NOMBRE
 * del menú y los links son sus items. Sin menú asignado —instalación nueva,
 * antes de que corra ese_latam_asegurar_menus()— cae a la columna escrita a
 * mano que recibe en `$fallback`, así el footer nunca queda vacío.
 *
 * @param array{title: string, links: list<array{label: string, url: string}>}|null $fallback
 */
function ese_latam_footer_columna(string $location, ?array $fallback = null): void {
    $id   = (int) (get_nav_menu_locations()[$location] ?? 0);
    $menu = $id > 0 ? wp_get_nav_menu_object($id) : false;

    if ($menu) {
        echo '<div class="footer-links__col">';
        echo '<p class="footer-heading">' . esc_html($menu->name) . '</p>';
        wp_nav_menu([
            'theme_location' => $location,
            'container'      => false,
            'depth'          => 1,
            'fallback_cb'    => false,
        ]);
        echo '</div>';

        return;
    }

    if (null === $fallback) {
        return;
    }

    echo '<div class="footer-links__col">';
    echo '<p class="footer-heading">' . esc_html($fallback['title']) . '</p>';
    echo '<ul>';
    foreach ($fallback['links'] as $link) {
        echo '<li class="menu-item"><a href="' . esc_url($link['url']) . '">' . esc_html($link['label']) . '</a></li>';
    }
    echo '</ul>';
    echo '</div>';
}
