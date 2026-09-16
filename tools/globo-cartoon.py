#!/usr/bin/env python3
"""
Genera la textura "cartoon" del globo de Distribuidores
(assets/imgs/distribuidores/earth-cartoon.webp) a partir de los mapas
realistas: el specular (agua = claro) separa mar de tierra; el daymap
aporta el relieve de color (bosque / pradera / desierto / hielo).

Paleta de marca: mar celeste (#0091d1 → #33a7da), tierra en verdes vivos
con un borde de costa más oscuro y una franja de agua clara pegada a la
costa, todo posterizado para el look caricaturesco.

Uso: python3 tools/globo-cartoon.py   (requiere Pillow + numpy)
"""
from pathlib import Path
import numpy as np
from PIL import Image, ImageFilter

BASE = Path(__file__).resolve().parent.parent / 'assets/imgs/distribuidores'
day = np.asarray(Image.open(BASE / 'earth-daymap.jpg').convert('RGB')).astype(np.float32) / 255
spec = np.asarray(Image.open(BASE / 'earth-specular.jpg').convert('L')).astype(np.float32) / 255
H, W, _ = day.shape

# --- máscaras ---------------------------------------------------------------
# Apertura morfológica sobre la máscara de tierra: el specular JPEG trae
# motas que, sin esto, salen como islitas cuadradas en medio del mar.
land_raw = Image.fromarray(((spec <= 0.5).astype(np.uint8)) * 255)
land_clean = land_raw.filter(ImageFilter.MinFilter(5)).filter(ImageFilter.MaxFilter(5))
water = ~np.asarray(land_clean).astype(bool)
lum = 0.299 * day[..., 0] + 0.587 * day[..., 1] + 0.114 * day[..., 2]
r, g, b = day[..., 0], day[..., 1], day[..., 2]
ice = (~water) & (lum > 0.72) & (np.abs(r - g) < 0.08) & (np.abs(g - b) < 0.08)
# Desierto: tierra clara y cálida (rojo por encima del verde y azul bajo)
desert = (~water) & (~ice) & (r > g + 0.04) & (lum > 0.38)
# Vegetación: el resto de la tierra; "verdor" para graduar la paleta
green_ratio = np.clip((g - (r + b) / 2) * 4 + 0.5, 0, 1)

def hexc(h):
    return np.array([int(h[i:i + 2], 16) for i in (1, 3, 5)], dtype=np.float32) / 255

def mix(a, b, t):
    t = t[..., None]
    return a * (1 - t) + b * t

def posterize(t, steps):
    return np.round(t * (steps - 1)) / (steps - 1)

out = np.zeros_like(day)

# --- mar: celeste de marca con profundidad posterizada ---------------------
deep = hexc('#0a6fb8')
shallow = hexc('#33a7da')
depth = posterize(np.clip((lum - 0.05) / 0.25, 0, 1), 3)  # el daymap es más claro en aguas someras
sea = mix(deep, shallow, depth)
out[water] = sea[water]

# --- tierra: verdes vivos -----------------------------------------------------
forest = hexc('#1f8a35')
meadow = hexc('#55bd3c')
lime = hexc('#9fd83a')
land_t = posterize(np.clip((lum - 0.12) / 0.45, 0, 1), 4)          # relieve de brillo
veg = mix(mix(forest, meadow, land_t), lime, posterize(1 - green_ratio, 3) * 0.35)
out[~water] = veg[~water]

# desierto: verde-amarillo cálido (sigue predominando el verde)
sand = hexc('#c6d24a')
sand_dark = hexc('#8fba33')
des = mix(sand_dark, sand, posterize(np.clip((lum - 0.35) / 0.4, 0, 1), 3))
out[desert] = des[desert]

# hielo / nieve
snow = hexc('#f3fbff')
out[ice] = snow

# --- costas: franja de agua clara y borde oscuro de tierra -------------------
land_img = Image.fromarray((~water).astype(np.uint8) * 255)
coast_glow = np.asarray(land_img.filter(ImageFilter.MaxFilter(9))).astype(bool) & water
coast_edge = (~water) & ~np.asarray(land_img.filter(ImageFilter.MinFilter(5))).astype(bool)
glow_col = hexc('#7fd0ef')
out[coast_glow] = mix(out, glow_col, np.full((H, W), 0.85, dtype=np.float32))[coast_glow]
out[coast_edge] = out[coast_edge] * 0.72

# suavizado leve para que la posterización no pixele en la esfera
img = Image.fromarray((np.clip(out, 0, 1) * 255).astype(np.uint8))
img = img.filter(ImageFilter.GaussianBlur(0.6))
img.save(BASE / 'earth-cartoon.webp', quality=82, method=6)
print('ok', img.size, (BASE / 'earth-cartoon.webp').stat().st_size, 'bytes')
