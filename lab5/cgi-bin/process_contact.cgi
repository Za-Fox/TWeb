#!C:/Users/User/AppData/Local/Programs/Python/Python313/python.exe
# -*- coding: utf-8 -*-
"""
==============================================================
  Lucrarea de laborator Nr.4 – CGI
  Face Off Bar – process_contact.cgi
  
  Sarcini îndeplinite:
    1. Preia datele din formular (POST)
    2. Validează câmpurile obligatorii (server-side)
    3. Salvează în fișierul submissions.txt
    4. Formează un răspuns HTML corespunzător
       (redirecționare înapoi cu status GET)
==============================================================
"""

import cgi
import cgitb
import os
import datetime
import html
import sys
import re

# Activează raportarea detaliată a erorilor (util la dezvoltare)
cgitb.enable()

# ------------------------------------------------------------------ #
#  Constante                                                           #
# ------------------------------------------------------------------ #
SUBMISSIONS_FILE = os.path.join(os.path.dirname(__file__), '..', 'submissions.txt')
# Ajustează calea dacă structura de foldere e diferită:
# SUBMISSIONS_FILE = '/var/www/html/faceoff/submissions.txt'

MAX_NAME_LEN    = 100
MAX_EMAIL_LEN   = 150
MAX_SUBJECT_LEN = 200
MAX_MSG_LEN     = 5000

EMAIL_REGEX = re.compile(r'^[^@\s]+@[^@\s]+\.[^@\s]+$')

# ------------------------------------------------------------------ #
#  Funcții ajutătoare                                                  #
# ------------------------------------------------------------------ #

def sanitize(value: str, max_len: int = 500) -> str:
    """Elimină spații și trunchiază la lungimea maximă."""
    return value.strip()[:max_len] if value else ''


def validate(name: str, email: str, message: str) -> str | None:
    """
    Returnează codul erorii dacă datele sunt invalide, altfel None.
    """
    if not name:
        return 'empty_name'
    if not email:
        return 'empty_email'
    if not EMAIL_REGEX.match(email):
        return 'invalid_email'
    if not message:
        return 'empty_message'
    return None  # date valide


def save_submission(name: str, email: str, subject: str, message: str) -> bool:
    """
    Adaugă înregistrarea în fișierul de text.
    Returnează True dacă salvarea a reușit.
    """
    try:
        timestamp = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        separator = '=' * 60

        entry = (
            f"\n{separator}\n"
            f"  Dată / Oră : {timestamp}\n"
            f"  Nume       : {name}\n"
            f"  Email      : {email}\n"
            f"  Subiect    : {subject if subject else '(fără subiect)'}\n"
            f"  Mesaj      :\n{message}\n"
            f"{separator}\n"
        )

        # Creează fișierul dacă nu există și adaugă înregistrarea
        with open(SUBMISSIONS_FILE, 'a', encoding='utf-8') as f:
            if f.tell() == 0:
                # Primul rând — antet
                f.write("FACEOFF BAR — Mesaje primite prin formular\n")
                f.write("=" * 60 + "\n")
            f.write(entry)

        return True

    except OSError as exc:
        # Loghează eroarea în stderr (Apache/Nginx o scrie în error.log)
        print(f"[CGI ERROR] Nu pot scrie în {SUBMISSIONS_FILE}: {exc}", file=sys.stderr)
        return False


def redirect_to(page: str, status: str, errcode: str = '') -> None:
    """
    Trimite header HTTP Location pentru redirecționare.
    """
    url = f"../{page}?status={status}"
    if errcode:
        url += f"&errcode={errcode}"
    print(f"Location: {url}")
    print()           # linie goală — obligatorie după headers CGI
    sys.exit(0)


def send_html_response(name: str, email: str) -> None:
    """
    Răspuns HTML complet (alternativă la redirect — Lab 4 sarcina 2).
    Afișat dacă return_page lipsește sau se vrea răspuns inline.
    """
    safe_name  = html.escape(name)
    safe_email = html.escape(email)
    print("Content-Type: text/html; charset=utf-8")
    print()
    print(f"""<!DOCTYPE html>
<html lang="ro">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Mesaj trimis — Face Off Bar</title>
  <link rel="stylesheet" href="../shared.css"/>
  <style>
    .cgi-card {{
      max-width: 520px; margin: 80px auto; text-align: center;
      background: #161616; border: 1px solid rgba(212,160,23,.22);
      border-radius: 16px; padding: 50px 36px;
    }}
    .cgi-icon {{ font-size: 3rem; margin-bottom: 18px; }}
    .cgi-card h2 {{
      font-family: 'Cormorant Garamond', serif;
      color: #d4a017; font-size: 1.8rem; margin-bottom: 14px;
    }}
    .cgi-card p {{ color: #888; line-height: 1.7; }}
    .cgi-card .btn {{
      display: inline-block; margin-top: 28px;
      padding: 11px 28px; border: 1px solid #d4a017;
      border-radius: 40px; color: #d4a017; text-decoration: none;
      font-size: .8rem; letter-spacing: 2px; text-transform: uppercase;
      transition: background .25s, color .25s;
    }}
    .cgi-card .btn:hover {{ background: #d4a017; color: #000; }}
  </style>
</head>
<body>
  <header>
    <h1>Face Off Bar</h1>
    <p>Cocktailuri premium · Atmosferă unică · Chișinău</p>
  </header>
  <nav>
    <a href="../acasa.php">Acasă</a>
    <a href="../meniu.php">Meniu</a>
    <a href="../galerie.php">Galerie</a>
    <a href="../contact.php">Contact</a>
  </nav>
  <div class="container">
    <div class="cgi-card">
      <div class="cgi-icon">✅</div>
      <h2>Mesaj trimis cu succes!</h2>
      <p>
        Mulțumim, <strong style="color:#d4a017">{safe_name}</strong>!<br>
        Mesajul tău a fost salvat și înregistrat.<br>
        Îți vom răspunde la <em>{safe_email}</em> în cel mai scurt timp.
      </p>
      <a href="../contact.php" class="btn">← Înapoi la Contact</a>
    </div>
  </div>
</body>
</html>
""")


# ------------------------------------------------------------------ #
#  Punct de intrare CGI                                               #
# ------------------------------------------------------------------ #

def main():
    # 1. Citire date POST
    form = cgi.FieldStorage()

    name        = sanitize(form.getvalue('name',        ''), MAX_NAME_LEN)
    email       = sanitize(form.getvalue('email',       ''), MAX_EMAIL_LEN)
    subject     = sanitize(form.getvalue('subject',     ''), MAX_SUBJECT_LEN)
    message     = sanitize(form.getvalue('message',     ''), MAX_MSG_LEN)
    return_page = sanitize(form.getvalue('return_page', ''), 50)

    # 2. Validare server-side
    error_code = validate(name, email, message)

    if error_code:
        # Date invalide → redirecționare cu cod eroare
        if return_page:
            redirect_to(return_page, 'error', error_code)
        else:
            # Fallback: mesaj HTML inline
            print("Content-Type: text/html; charset=utf-8")
            print()
            print(f"<p style='color:red'>Eroare: {html.escape(error_code)}</p>")
        return

    # 3. Salvare în fișier (sarcina 3 Lab 4)
    saved = save_submission(name, email, subject, message)

    if not saved:
        if return_page:
            redirect_to(return_page, 'error', 'save_error')
        else:
            print("Content-Type: text/html; charset=utf-8")
            print()
            print("<p style='color:red'>Eroare la salvarea mesajului.</p>")
        return

    # 4. Răspuns (sarcina 2 Lab 4)
    if return_page:
        # Redirecționare înapoi la pagina PHP cu status=success
        redirect_to(return_page, 'success')
    else:
        # Răspuns HTML complet generat de CGI
        send_html_response(name, email)


if __name__ == '__main__':
    main()
