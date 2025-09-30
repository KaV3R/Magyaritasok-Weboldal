# Magyaritasok-Weboldal

Ez egy **nyílt forráskódú weboldal projekt**, amelynek célja, hogy a közösség számára **ingyenes és helyben futtatható** eszközt biztosítson játékok magyarosításainak gyűjtésére, megosztására és kezelésére.  
A felhasználók regisztrálhatnak, bejelentkezhetnek, feltölthetik a fordításokat (magyarításokat), valamint fórumon kommunikálhatnak egymással. A projekt tartalmaz **admin kezelőfelületet** is a moderációhoz és tartalomkezeléshez.

> ⚡ **Fontos:** a projekt teljesen ingyenes, és úgy készült, hogy saját gépeken (vagy VPS-en) egyszerűen futtatható legyen.

---

## ✨ Funkciók
- 👤 Felhasználói regisztráció és bejelentkezés
- 📂 Fordítások (magyarítások) feltöltése és verziókövetése
- 💬 Fórum a közösségi kommunikációhoz
- 🔑 Admin felület moderáláshoz és tartalomkezeléshez
- 🔎 Keresés és kategorizálás (játék szerint, verzió, állapot)
- Discord webhook integráció

---

## 🚀 Telepítés Windows-on (XAMPP)

### 1. XAMPP letöltése és telepítése
- Töltsd le a [XAMPP hivatalos oldaláról](https://www.apachefriends.org/hu/index.html)
- Telepítsd a programot (pl. `C:\xampp` könyvtárba)

### 2. Repository másolása a htdocs-ba
- Nyisd meg a XAMPP telepítési könyvtárát (`C:\xampp`)
- Lépj be a `htdocs` mappába
- Másold ide a projekt forráskódját (pl. `C:\xampp\htdocs`)

### 3. Apache és MySQL elindítása
- Indítsd el a **XAMPP Control Panelt**
- Kattints az **Apache** és **MySQL** melletti **Start** gombokra

### 4. Adatbázis importálása
- Nyisd meg a böngészőben a `http://localhost/phpmyadmin` oldalt
- Hozz létre egy új adatbázist (pl. `magyarositasok` néven)
- Importáld a projekt `database.sql` fájlját az adatbázisba

### 5. Konfiguráció
- Állítsd be az adatbázis kapcsolatot:
  - host: `localhost`
  - user: `root`
  - password: *(alapértelmezés szerint üres XAMPP-ban)*
  - database: `magyarositasok`

### 6. Weboldal elérése
- Nyisd meg a böngészőt, és írd be: `http://localhost`  
- Ha mindent jól állítottál be, a weboldal elindul a saját gépeden.

---

## 🛠 Adminisztráció
- Fordítások jóváhagyása / elutasítása
- Felhasználói jogosultságok kezelése (moderátor, admin stb.)
- Fórum moderálása

---

## 🤝 Közreműködés
1. Forkold a repót  
2. Hozz létre egy feature-branch-et:
   ```bash
   git checkout -b feature/uj-funkcio
   ```
3. Fejleszd és teszteld a változtatásokat  
4. Küldj pull requestet részletes leírással

### Kódviselkedési elv (Code of Conduct)
Legyél tisztelettudó és segítőkész. A projekthez való hozzájárulás feltétele a kulturált kommunikáció és a netikett betartása.

---

## 📄 Licenc
**MIT Licenc** — egyszerű, permisszív nyílt forráskódú licenc.  
Ha más licencet szeretnél, módosítsd a `LICENSE` fájlt.

---

## 📬 Kapcsolat
- Discord: **ricsi3171**  
- GitHub issue szekció: kérdések, hibajelentések és ötletek megosztására

