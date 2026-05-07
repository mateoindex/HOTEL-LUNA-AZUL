# reporte de ocupacion mensual con grafico de barras
# uso: python generate_occupancy_report.py --data <json> --out <ruta>

import argparse
import json
from datetime import datetime

from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.units import cm
from reportlab.pdfgen import canvas

INK = colors.HexColor('#1a1a1a')
INK_MUTE = colors.HexColor('#9a9a9a')
BRASS = colors.HexColor('#b08d57')
LINE = colors.HexColor('#e8e0d2')


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--data', help='JSON inline')
    ap.add_argument('--data-file', help='ruta a archivo JSON')
    ap.add_argument('--out', required=True)
    args = ap.parse_args()

    if args.data_file:
        with open(args.data_file, 'r', encoding='utf-8') as f:
            payload = json.load(f)
    else:
        payload = json.loads(args.data)
    days = payload.get('days', [])
    title = payload.get('title', 'Reporte de ocupación')
    subtitle = payload.get('subtitle', '')

    c = canvas.Canvas(args.out, pagesize=A4)
    w, h = A4
    mx = 2.4 * cm
    my = 2.4 * cm

    # header
    c.setFillColor(INK)
    c.setFont('Helvetica-Bold', 9)
    c.drawString(mx, h - my, 'HOTEL LUNA AZUL · CARTAGENA')
    c.setFillColor(INK_MUTE)
    c.setFont('Helvetica', 9)
    c.drawRightString(w - mx, h - my, 'Reporte de ocupación')

    c.setStrokeColor(LINE)
    c.line(mx, h - my - 14, w - mx, h - my - 14)

    # titulo
    c.setFillColor(INK_MUTE)
    c.setFont('Helvetica', 8)
    c.drawString(mx, h - my - 50, subtitle.upper())

    c.setFillColor(INK)
    c.setFont('Helvetica-Bold', 26)
    c.drawString(mx, h - my - 84, title)

    # area del grafico
    chart_top = h - my - 140
    chart_bottom = chart_top - 240
    chart_left = mx
    chart_right = w - mx

    c.setStrokeColor(LINE)
    c.line(chart_left, chart_top, chart_right, chart_top)

    if days:
        n = len(days)
        gap = 10
        bar_w = (chart_right - chart_left - gap * (n - 1)) / max(1, n)
        max_h = chart_top - chart_bottom - 40
        for i, d in enumerate(days):
            pct = max(2, min(100, int(d.get('pct', 0))))
            x = chart_left + i * (bar_w + gap)
            bh = max_h * (pct / 100.0)
            c.setFillColor(INK)
            c.rect(x, chart_bottom + 30, bar_w, bh, stroke=0, fill=1)
            # valor
            c.setFillColor(INK)
            c.setFont('Helvetica-Bold', 8)
            c.drawCentredString(x + bar_w / 2, chart_bottom + 30 + bh + 6, f"{pct}%")
            # label
            c.setFillColor(INK_MUTE)
            c.setFont('Helvetica', 7)
            try:
                lbl = datetime.fromisoformat(d['date']).strftime('%d %b').upper()
            except Exception:
                lbl = str(d.get('date', ''))
            c.drawCentredString(x + bar_w / 2, chart_bottom + 14, lbl)
    else:
        c.setFillColor(INK_MUTE)
        c.setFont('Helvetica', 12)
        c.drawCentredString((chart_left + chart_right) / 2,
                            (chart_top + chart_bottom) / 2,
                            'Sin datos en el rango seleccionado.')

    # tabla resumen
    table_top = chart_bottom - 30
    c.setFillColor(INK_MUTE)
    c.setFont('Helvetica', 7.5)
    c.drawString(mx, table_top, 'DETALLE')

    c.setStrokeColor(LINE)
    c.line(mx, table_top - 8, w - mx, table_top - 8)

    y = table_top - 30
    c.setFillColor(INK)
    c.setFont('Helvetica-Bold', 9)
    c.drawString(mx, y, 'Día')
    c.drawString(mx + 6 * cm, y, 'Habitaciones ocupadas')
    c.drawRightString(w - mx, y, 'Ocupación')

    y -= 8
    c.setStrokeColor(LINE)
    c.line(mx, y, w - mx, y)

    c.setFont('Helvetica', 9)
    c.setFillColor(INK)
    for d in days:
        y -= 18
        if y < my + 30:
            c.showPage()
            y = h - my
        try:
            lbl = datetime.fromisoformat(d['date']).strftime('%d %b %Y')
        except Exception:
            lbl = str(d.get('date', ''))
        c.drawString(mx, y, lbl)
        c.drawString(mx + 6 * cm, y, str(d.get('busy', 0)))
        c.drawRightString(w - mx, y, f"{d.get('pct', 0)}%")

    # footer
    c.setFillColor(INK_MUTE)
    c.setFont('Helvetica', 7.5)
    c.drawString(mx, my, f"Generado el {datetime.now().strftime('%d/%m/%Y %H:%M')}")
    c.drawRightString(w - mx, my, 'Hotel Luna Azul · Cartagena')

    c.showPage()
    c.save()


if __name__ == '__main__':
    main()
