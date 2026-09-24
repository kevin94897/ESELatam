<?php
/**
 * Fotos de producto a tamaño uniforme para la grilla del catálogo.
 *
 * Los renders de la biblioteca de medios vienen escalados a su tamaño FÍSICO
 * (tools/preparar-imagenes.cjs: raíz cúbica del volumen sobre una línea de
 * base común). Eso es lo correcto en la ficha, donde al cambiar de 80L a
 * 360L el contenedor tiene que crecer; pero en la grilla del catálogo cada
 * tarjeta muestra UN producto y con ese criterio un Batterymax se veía
 * diminuto al lado de un contenedor de 4 ruedas que desbordaba su tarjeta.
 *
 * Acá se genera, una sola vez por adjunto, una copia "-catalogo.webp" con el
 * producto recortado a su contenido y reencuadrado en un lienzo cuadrado con
 * márgenes fijos, apoyado siempre sobre la misma línea de base. Así todas
 * las tarjetas muestran la pieza al mismo tamaño aparente. La copia queda
 * junto al original y se anota en el adjunto (`_ese_foto_catalogo`) con la
 * fecha del original: si el render se reemplaza, se regenera sola.
 *
 * Usa Imagick si está cargado y GD si no; sin ninguno de los dos devuelve
 * la URL original y la tarjeta se ve como antes.
 *
 * @package EseLatam
 */
declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

/**
 * URL de la versión uniforme de una foto de producto (o la original si no
 * se puede generar).
 *
 * @param string $url  URL de tamaño completo de un adjunto de la biblioteca.
 * @param int    $lado Lado del lienzo cuadrado de salida, en px.
 */
function ese_latam_foto_catalogo(string $url, int $lado = 1200): string {
    static $cache = [];

    if ('' === $url) {
        return '';
    }
    if (isset($cache[$url])) {
        return $cache[$url];
    }

    $cache[$url] = $url;

    $id = attachment_url_to_postid($url);
    if ($id <= 0) {
        return $url;
    }

    $origen = get_attached_file($id);
    if (! is_string($origen) || '' === $origen || ! is_readable($origen)) {
        return $url;
    }

    $subida = wp_get_upload_dir();
    $mtime  = (int) filemtime($origen);
    $meta   = get_post_meta($id, '_ese_foto_catalogo', true);

    if (
        is_array($meta)
        && (int) ($meta['mtime'] ?? 0) === $mtime
        && (int) ($meta['lado'] ?? 0) === $lado
        && is_string($meta['file'] ?? null)
        && is_file($subida['basedir'] . '/' . $meta['file'])
    ) {
        return $cache[$url] = $subida['baseurl'] . '/' . $meta['file'];
    }

    $destino = (string) preg_replace('/\.[a-z0-9]+$/i', '', $origen) . '-catalogo.webp';

    if (! ese_latam_foto_catalogo_generar($origen, $destino, $lado)) {
        return $url;
    }

    $rel = ltrim(str_replace(wp_normalize_path($subida['basedir']), '', wp_normalize_path($destino)), '/');
    update_post_meta($id, '_ese_foto_catalogo', ['file' => $rel, 'mtime' => $mtime, 'lado' => $lado]);

    return $cache[$url] = $subida['baseurl'] . '/' . $rel;
}

/**
 * Genera la copia recortada y reencuadrada. Devuelve false si no hay motor
 * de imágenes o el archivo no se pudo procesar.
 *
 * Encuadre: la pieza ocupa como máximo el 86 % del alto y el 90 % del ancho
 * del lienzo, centrada horizontalmente y con la base al 95 % del alto. La
 * misma línea de base para todas hace que "paren" sobre la sombra de la
 * tarjeta a la misma altura.
 */
function ese_latam_foto_catalogo_generar(string $origen, string $destino, int $lado): bool {
    $max_w = (int) round($lado * 0.90);
    $max_h = (int) round($lado * 0.86);
    $base  = (int) round($lado * 0.95);

    if (class_exists('Imagick')) {
        try {
            $im = new Imagick($origen);
            $im->setIteratorIndex(0);
            $alpha = $im->getImageAlphaChannel();
            $fondo = $alpha ? 'transparent' : 'white';

            $im->setImageBackgroundColor(new ImagickPixel($fondo));
            $im->trimImage(0.03 * Imagick::getQuantum());
            $im->setImagePage(0, 0, 0, 0);

            $w = $im->getImageWidth();
            $h = $im->getImageHeight();
            if ($w < 2 || $h < 2) {
                return false;
            }

            $escala = min($max_w / $w, $max_h / $h);
            $nw     = max(1, (int) round($w * $escala));
            $nh     = max(1, (int) round($h * $escala));
            $im->resizeImage($nw, $nh, Imagick::FILTER_LANCZOS, 1);

            $x = (int) round(($lado - $nw) / 2);
            $y = $base - $nh;
            $im->setImageBackgroundColor(new ImagickPixel($fondo));
            $im->extentImage($lado, $lado, -$x, -$y);

            $im->setImageFormat('webp');
            $im->setImageCompressionQuality(86);
            $ok = $im->writeImage($destino);
            $im->clear();

            return (bool) $ok;
        } catch (Throwable $e) {
            return false;
        }
    }

    if (! function_exists('imagecreatetruecolor') || ! function_exists('imagewebp')) {
        return false;
    }

    $src = ese_latam_foto_catalogo_gd_abrir($origen);
    if (false === $src) {
        return false;
    }

    $w = imagesx($src);
    $h = imagesy($src);

    // ¿Tiene transparencia? Se mira la esquina: en un render con alpha el
    // fondo es transparente; si es opaco, se recorta contra blanco.
    $esquina = imagecolorat($src, 0, 0);
    $alpha   = (($esquina >> 24) & 0x7F) > 0;

    // Caja del contenido. Se muestrea de a 2 px: el error de ±2 px no se ve
    // y el barrido de 1200×1200 baja de ~1,4 M a ~360 k lecturas.
    $paso = 2;
    $x0 = $w; $y0 = $h; $x1 = -1; $y1 = -1;
    for ($y = 0; $y < $h; $y += $paso) {
        for ($x = 0; $x < $w; $x += $paso) {
            $c = imagecolorat($src, $x, $y);
            if ($alpha) {
                $opaco = (($c >> 24) & 0x7F) < 96;
            } else {
                $opaco = (($c >> 16) & 0xFF) < 245 || (($c >> 8) & 0xFF) < 245 || ($c & 0xFF) < 245;
            }
            if ($opaco) {
                if ($x < $x0) { $x0 = $x; }
                if ($x > $x1) { $x1 = $x; }
                if ($y < $y0) { $y0 = $y; }
                if ($y > $y1) { $y1 = $y; }
            }
        }
    }

    if ($x1 < 0 || $y1 < 0) {
        imagedestroy($src);
        return false;
    }

    $bw = $x1 - $x0 + $paso;
    $bh = $y1 - $y0 + $paso;

    $escala = min($max_w / $bw, $max_h / $bh);
    $nw     = max(1, (int) round($bw * $escala));
    $nh     = max(1, (int) round($bh * $escala));
    $x      = (int) round(($lado - $nw) / 2);
    $y      = $base - $nh;

    $dst = imagecreatetruecolor($lado, $lado);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $fondo = $alpha ? imagecolorallocatealpha($dst, 0, 0, 0, 127) : imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $fondo);
    imagealphablending($dst, true);
    imagecopyresampled($dst, $src, $x, $y, $x0, $y0, $nw, $nh, $bw, $bh);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);

    $ok = imagewebp($dst, $destino, 86);
    imagedestroy($src);
    imagedestroy($dst);

    return (bool) $ok;
}

/**
 * Abre un archivo de imagen con GD según su tipo real.
 *
 * @return GdImage|false
 */
function ese_latam_foto_catalogo_gd_abrir(string $ruta) {
    $tipo = wp_check_filetype($ruta)['type'] ?? '';

    switch ($tipo) {
        case 'image/png':
            return function_exists('imagecreatefrompng') ? imagecreatefrompng($ruta) : false;
        case 'image/webp':
            return function_exists('imagecreatefromwebp') ? imagecreatefromwebp($ruta) : false;
        case 'image/jpeg':
            return function_exists('imagecreatefromjpeg') ? imagecreatefromjpeg($ruta) : false;
        default:
            return false;
    }
}
