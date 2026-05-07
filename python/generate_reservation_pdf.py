# genera el voucher de reserva en PDF
# uso: python generate_reservation_pdf.py --data <json> --out <ruta>

import argparse
import json
import sys
from datetime import datetime

from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.lib.units import cm
from reportlab.pdfgen import canvas
from reportlab.platypus import Paragraph

# paleta del hotel (debe casar con tokens.css)
INK = colors.HexColor('#1a1a1a')
INK_SOFT = colors.HexColor('#4a4a4a')
INK_MUTE = colors.HexColor('#9a9a9a')
PAPER = colors.HexColor('#faf7f2')
BRASS = colors.HexColor('#b08d57')
LINE = colors.HexColor('#e8e0d2')


def money(v):
    try:
        n = float(v)
    except (TypeError, ValueError):
        return '-'
    return '$' + format(int(round(n)), ',d').replace(',', '.')


def fmt_date(s):
    if not s:
        return '-'
    try:
        return datetime.fromisoformat(s).strftime('%d %b %Y')
    except Exception:
        return s


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--data', help='JSON inline')
    ap.add_argument('--data-file', help='ruta a archivo JSON')
    ap.add_argument('--out', required=True, help='ruta de salida del PDF')
    args = ap.parse_args()

    if args.data_file:
        with open(args.data_file, 'r', encoding='utf-8') as f:
            data = json.load(f)
    elif args.data:
        data = json.loads(args.data)
    else:
        print('falta --data o --data-file', file=sys.stderr)
        sys.exit(2)

    c = canvas.Canvas(args.out, pagesize=A4)
    w, h = A4

    # margenes
    mx = 2.4 * cm
    my = 2.4 * cm

    # ---------- header ----------
    c.setFillColor(INK)
    c.setFont('Helvetica-Bold', 9)
    c.drawString(mx, h - my, 'HOTEL LUNA AZUL · CARTAGENA')

    c.setFont('Helvetica', 9)
    c.setFillColor(INK_MUTE)
    c.drawRightString(w - mx, h - my, 'Voucher de reserva · Documento interno')

    # logo monogram
    c.setStrokeColor(BRASS)
    c.setLineWidth(0.8)
    c.circle(mx + 14, h - my - 30, 14, stroke=1, fill=0)
    c.setFillColor(INK)
    c.setFont('Helvetica-Bold', 10)
    c.drawCentredString(mx + 14, h - my - 33, 'LA')

    # linea
    c.setStrokeColor(LINE)
    c.setLineWidth(0.6)
    c.line(mx, h - my - 60, w - mx, h - my - 60)

    # ---------- bloque codigo ----------
    y = h - my - 110
    c.setFillColor(INK_MUTE)
    c.setFont('Helvetica', 8)
    c.drawString(mx, y, 'CÓDIGO DE RESERVA')

    c.setFillColor(INK)
    c.setFont('Helvetica-Bold', 32)
    c.drawString(mx, y - 38, str(data.get('code', '-')))

    # estado a la derecha
    status = (data.get('status') or '').replace('_', ' ').upper()
    c.setFillColor(INK_MUTE)
    c.setFont('Helvetica', 8)
    c.drawRightString(w - mx, y, 'ESTADO')
    c.setFillColor(INK)
    c.setFont('Helvetica-Bold', 14)
    c.drawRightString(w - mx, y - 22, status)

    # linea hairline
    c.setStrokeColor(LINE)
    c.line(mx, y - 60, w - mx, y - 60)

    # ---------- huesped + habitacion ----------
    y -= 88
    col_w = (w - 2 * mx - 30) / 2

    def label_value(x, yy, label, value, font_size=11):
        c.setFillColor(INK_MUTE)
        c.setFont('Helvetica', 7.5)
        c.drawString(x, yy, label.upper())
        c.setFillColor(INK)
        c.setFont('Helvetica', font_size)
        c.drawString(x, yy - 14, str(value or '-'))

    # huesped (col izq)
    c.setFillColor(INK)
    c.setFont('Helvetica-Bold', 10)
    c.drawString(mx, y, 'HUÉSPED')

    label_value(mx, y - 22, 'Nombre',
                f"{data.get('first_name','')} {data.get('last_name','')}".strip())
    label_value(mx, y - 60, 'Documento',
                f"{data.get('document_type','')} {data.get('document_number','')}".strip())
    label_value(mx, y - 98, 'Teléfono', data.get('phone', '-'))
    label_value(mx, y - 136, 'Correo', data.get('guest_email') or '-')

    # habitacion (col der)
    x2 = mx + col_w + 30
    c.setFillColor(INK)
    c.setFont('Helvetica-Bold', 10)
    c.drawString(x2, y, 'HABITACIÓN')

    label_value(x2, y - 22, 'Código', data.get('room_code', '-'))
    label_value(x2, y - 60, 'Tipo',
                f"{data.get('room_type','')} · {data.get('capacity','-')} pax")
    label_value(x2, y - 98, 'Piso', data.get('floor', '-'))
    label_value(x2, y - 136, 'Tarifa / noche', money(data.get('price_per_night')))

    # divider
    y -= 180
    c.setStrokeColor(LINE)
    c.line(mx, y, w - mx, y)

    # ---------- estancia ----------
    y -= 28
    c.setFillColor(INK)
    c.setFont('Helvetica-Bold', 10)
    c.drawString(mx, y, 'ESTANCIA')

    label_value(mx, y - 22, 'Entrada', fmt_date(data.get('check_in')))
    label_value(x2, y - 22, 'Salida', fmt_date(data.get('check_out')))
    label_value(mx, y - 60, 'Noches', data.get('nights', '-'))
    label_value(x2, y - 60, 'Adultos / niños',
                f"{data.get('adults', 0)} · {data.get('children', 0)}")

    notes = data.get('notes') or '—'
    label_value(mx, y - 98, 'Notas', notes[:120])

    # divider
    y -= 130
    c.setStrokeColor(LINE)
    c.line(mx, y, w - mx, y)

    # ---------- total ----------
    y -= 30
    c.setFillColor(INK_MUTE)
    c.setFont('Helvetica', 8)
    c.drawString(mx, y, 'TOTAL DE LA RESERVA')

    c.setFillColor(INK)
    c.setFont('Helvetica-Bold', 28)
    c.drawString(mx, y - 36, money(data.get('total_amount')))

    # ---------- footer ----------
    c.setFillColor(INK_MUTE)
    c.setFont('Helvetica', 7.5)
    now = datetime.now().strftime('%d/%m/%Y %H:%M')
    c.drawString(mx, my, f'Documento generado automáticamente el {now}')
    c.drawRightString(w - mx, my,
                      f"Creado por {data.get('created_by_name','-')} · Hotel Luna Azul")

    c.showPage()
    c.save()


if __name__ == '__main__':
    main()
