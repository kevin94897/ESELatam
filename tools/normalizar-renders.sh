#!/usr/bin/env bash
#
# Normaliza los renders de producto a un lienzo común, listos para subir a
# la biblioteca de medios (los consume tools/seed-productos.php).
#
#   bash tools/normalizar-renders.sh [familia ...]
#   bash tools/normalizar-renders.sh            # todas
#
# POR QUÉ HACE FALTA
# ------------------
# Los renders llegan reencuadrados uno por uno: distinto tamaño de lienzo
# (1000x1000, 3840x2880, 3300x3300) y el contenedor a distinta escala dentro
# del cuadro. En la ficha de producto el <img> se dibuja a una ALTURA FIJA,
# así que tal cual vienen un 80L y un 360L se verían igual de grandes, y el
# 240L de 3 ruedas saldría más alto que el 370L (en el original ocupa más
# cuadro, al revés que en la realidad).
#
# Por eso no se puede heredar la escala del archivo: cada modelo se recorta
# a su contenido y se re-escala a su ALTURA REAL en milímetros. El ancho sale
# solo del aspecto de cada render, nunca se deforma. Todo queda apoyado sobre
# la misma línea de base para que el producto "pare" sobre el pedestal sin
# saltar al cambiar de variante.
#
# AGREGAR UNA FAMILIA NUEVA
# -------------------------
# Sumar una entrada a FAMILIAS con los cinco campos:
#   nombre|carpeta|patrón|altura por modelo|alias
# donde el patrón usa {L} = litros y {C} = código de color FC, la altura por
# modelo es "litros:altura_mm" separado por comas, y el alias (opcional)
# resuelve los archivos que no siguen ese patrón. El script avisa y falla si
# falta alguna combinación de la matriz.
set -euo pipefail

cd "$(dirname "$0")/.."

# Los renders originales ya NO viven en el theme: se movieron junto al resto
# de las imágenes que entrega el cliente, para que el repositorio no cargue
# ~90 MB de PNG. BASE se puede pisar por variable de entorno.
BASE="${BASE:-/c/Users/Usuario/Desktop/Kevin DATA/ESE Latam Productos Imagenes 2026/Contenedores con ruedas (renders originales)}"

CANVAS=1200      # lado del lienzo cuadrado de salida
ALTO_MAX=1100    # alto que ocupa el modelo más alto de cada familia
ANCHO_MAX=1140   # tope de ancho: los de 4 ruedas son más anchos que altos
                 # y, escalados solo por alto, el -extent los recortaría
BASELINE=30      # margen bajo la base del contenedor
CALIDAD=86       # calidad WebP

# nombre | carpeta | patrón de archivo | litros:altura_mm,... | alias
#
# El alias cubre los originales que no siguen el patrón de su carpeta
# (formato "litros:codigo=archivo.png", separados por ";"). En la familia de
# 4 ruedas el 400L es justamente eso: solo el negro trae el litraje en el
# nombre y los otros cinco colores vienen como fichas "FC xxx COLOR.png".
FAMILIAS=(
  "2 ruedas|$BASE/contenedores-2-ruedas|{L}L_FC{C}.png|80:930,120:940,180:1000,240:1075,360:1100|"
  "3 ruedas|$BASE/contenedores-3-ruedas|{L}L 3PL_FC{C}.png|240:1080,370:1100|"
  "4 ruedas|$BASE/contenedores-4-ruedas|{L}L_FC{C}.png|400:1070,500:1100,660:1210,770:1360,1100:1470|400:020=Contenedor 400 L FC020.png;400:030=FC 030 GRIS OSCURO.png;400:040=FC 040 VERDE.png;400:050=FC 050 AMARILLO.png;400:081=FC 081 AZUL.png;400:090=FC 090 MARRON.png;1100:020=1100 L FL_FC020.png;1100:030=1100 L FL_FC030.png;1100:040=1100 L FL_FC040.png;1100:050=1100 L FL_FC050.png;1100:081=1100 L FL_FC081.png;1100:090=1100 L FL_FC090.png"
)

COLORES=(020 030 040 050 081 090)

# El nombre del original no siempre respeta el patrón: hay copias "(1)" /
# "(2)" del explorador, algún código sin el cero inicial (120L_FC50) y los
# alias declarados arriba.
buscar_original() {
  local carpeta="$1" patron="$2" litros="$3" codigo="$4" alias="$5" base cand

  # 1) Alias explícito para esta combinación.
  if [ -n "$alias" ]; then
    local IFS=';'
    for par in $alias; do
      if [ "${par%%=*}" = "${litros}:${codigo}" ]; then
        local f="$carpeta/${par#*=}"
        [ -f "$f" ] && { printf '%s' "$f"; return 0; }
      fi
    done
  fi

  # 2) El patrón de la familia, con sus variantes de nombre habituales.
  base="${patron//\{L\}/$litros}"
  for cand in "${base//\{C\}/$codigo}" \
              "${base//\{C\}/${codigo#0}}"; do
    for suf in "" " (1)" " (2)"; do
      local f="$carpeta/${cand%.png}${suf}.png"
      [ -f "$f" ] && { printf '%s' "$f"; return 0; }
    done
  done
  return 1
}

total=0
faltantes=0

for entrada in "${FAMILIAS[@]}"; do
  IFS='|' read -r nombre carpeta patron alturas alias <<< "$entrada"

  # Si se pasaron argumentos, solo se procesan esas familias.
  if [ $# -gt 0 ]; then
    encontrada=0
    for arg in "$@"; do
      [[ "$nombre" == *"$arg"* ]] && encontrada=1
    done
    [ "$encontrada" -eq 1 ] || continue
  fi

  if [ ! -d "$carpeta" ]; then
    echo "-- $nombre: no existe $carpeta, se omite" >&2
    continue
  fi

  # La altura mayor de la familia define la escala del lienzo.
  max_mm=0
  IFS=',' read -ra pares <<< "$alturas"
  for par in "${pares[@]}"; do
    mm="${par#*:}"
    [ "$mm" -gt "$max_mm" ] && max_mm="$mm"
  done

  salida="$carpeta/web"
  mkdir -p "$salida"
  echo "== $nombre ($carpeta)"

  for par in "${pares[@]}"; do
    litros="${par%%:*}"
    mm="${par#*:}"
    # Regla de tres contra el modelo más alto de la familia, que es el que
    # ocupa ALTO_MAX: así la proporción entre capacidades es la real.
    alto=$(( mm * ALTO_MAX / max_mm ))

    for codigo in "${COLORES[@]}"; do
      if ! origen="$(buscar_original "$carpeta" "$patron" "$litros" "$codigo" "$alias")"; then
        echo "   FALTA ${litros}L FC${codigo}" >&2
        faltantes=$((faltantes + 1))
        continue
      fi

      # El sufijo del archivo de salida sale del patrón: así la familia de
      # 3 ruedas no pisa a la de 2 en la biblioteca de medios.
      sufijo=""
      [[ "$patron" == *"3PL"* ]] && sufijo="-3pl"
      [[ "$carpeta" == *"4-ruedas"* ]] && sufijo="-4r"
      destino="$salida/${litros}l${sufijo}-fc${codigo}.webp"

      magick "$origen" \
        -trim +repage \
        -resize "x${alto}" \
        -background none \
        -gravity south -splice "0x${BASELINE}" \
        -gravity center -extent "${CANVAS}x${CANVAS}" \
        -define webp:lossless=false -quality "$CALIDAD" \
        "$destino"

      total=$((total + 1))
    done
    printf '   %sL -> %s px de alto\n' "$litros" "$alto"
  done

  du -sh "$salida"
done

echo
echo "Generadas: $total   Faltantes: $faltantes"
[ "$faltantes" -eq 0 ] || exit 1
