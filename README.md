# Magyaritasok-Weboldal
🎮 Játék Magyarítások — Open Source

Ez egy nyílt forráskódú weboldal projekt, amelynek célja, hogy a közösség számára ingyenes és helyben futtatható eszközt biztosítson játékok magyarosításainak gyűjtésére, megosztására és kezelésére.A felhasználók regisztrálhatnak, bejelentkezhetnek, feltölthetik a fordításokat (magyarításokat), valamint fórumon kommunikálhatnak egymással. A projekt tartalmaz admin kezelőfelületet is a moderációhoz és tartalomkezeléshez.

⚡ Fontos: a projekt teljesen ingyenes, és úgy készült, hogy saját gépeken (vagy VPS-en) egyszerűen futtatható legyen.

✨ Funkciók

👤 Felhasználói regisztráció és bejelentkezés

📂 Fordítások (magyarítások) feltöltése és verziókövetése

💬 Fórum a közösségi kommunikációhoz

🔑 Admin felület moderáláshoz és tartalomkezeléshez

🔎 Keresés és kategorizálás (játék szerint, verzió, állapot)

📢 Discord elérhetőség támogatáshoz és kérdésekhez

🚀 Telepítés Windows-on (XAMPP)

A legegyszerűbb módja a projekt kipróbálásának Windows rendszeren a XAMPP használata.

1. XAMPP letöltése és telepítése

Töltsd le a XAMPP hivatalos oldaláról

Telepítsd a programot (pl. C:\xampp könyvtárba)

2. Repository másolása a htdocs-ba

Nyisd meg a XAMPP telepítési könyvtárát (C:\xampp)

Lépj be a htdocs mappába

Másold ide a projekt forráskódját (pl. C:\xampp\htdocs\jatekmagyaritasok)

3. Apache és MySQL elindítása

Indítsd el a XAMPP Control Panelt

Kattints az Apache és MySQL melletti Start gombokra

4. Adatbázis importálása

Nyisd meg a böngészőben a http://localhost/phpmyadmin oldalt

Hozz létre egy új adatbázist (pl. jatekmagyaritasok néven)

Importáld a projekt database.sql fájlját az adatbázisba

5. Konfiguráció

Nyisd meg a projekt mappájában az .env vagy config.php fájlt

Állítsd be az adatbázis kapcsolatot:

host: localhost

user: root

password: (alapértelmezés szerint üres XAMPP-ban)

database: jatekmagyaritasok

6. Weboldal elérése

Nyisd meg a böngészőt, és írd be:

http://localhost/jatekmagyaritasok

Ha mindent jól állítottál be, a weboldal elindul a saját gépeden.

🛠 Adminisztráció

Az admin felület segítségével kezelhető:

Fordítások jóváhagyása / elutasítása

Felhasználói jogosultságok kezelése (moderátor, admin stb.)

Fórum moderálása

Discord webhook értesítések (új feltöltés, jelentés)

🤝 Közreműködés

Szívesen fogadjuk a pull requesteket és hibajelentéseket!

Forkold a repót

Hozz létre egy feature-branch-et:

git checkout -b feature/uj-funkcio

Fejleszd és teszteld a változtatásokat

Küldj pull requestet részletes leírással

Kódviselkedési elv (Code of Conduct)

Legyél tisztelettudó és segítőkész. A projekthez való hozzájárulás feltétele a kulturált kommunikáció és a netikett betartása.

📄 Licenc

MIT Licenc — egyszerű, permisszív nyílt forráskódú licenc.Ha más licencet szeretnél, módosítsd a LICENSE fájlt.

📬 Kapcsolat

Discord: ricsi3171

GitHub issue szekció: kérdések, hibajelentések és ötletek megosztására

ℹ️ Megjegyzés: A README tartalmaz Windows (XAMPP) és általános telepítési példákat. Ha szeretnéd, pontos stackhez (pl. Laravel, Node.js, Django) tudom szabni a dokumentációt.

