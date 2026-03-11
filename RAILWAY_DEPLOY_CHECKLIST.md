# Railway deploy checklist – do these in order

Do **every** step. Missing one causes the same errors again.

---

## 1. Railway → your backend service → **Settings**

- **Custom Start Command:** leave **empty** (delete any `php artisan serve...`).
- **Custom Build Command:** leave **empty**.
- **Networking:** generated domain must use port **80**.

---

## 2. Railway → **Settings** → Build

- **Clear build cache** (or “Redeploy without cache”) so the latest Dockerfile is used.
- Confirm **Root Directory** is empty (repo root), not `Backend`.

---

## 3. GitHub

- Merge **ashan** → **main** (so Railway builds the nginx + PHP-FPM Dockerfile).
- Or: if Railway is connected to **ashan**, make sure **ashan** has the latest code (no merge needed).

---

## 4. Railway → **Deploy**

- Click **Deploy** / **Redeploy** after the merge (or it may deploy automatically).
- Wait for **Build** to finish, then check **Deploy logs** (not Build logs).
- You should see: `Nothing to migrate.` then nginx running (no Apache/MPM errors).

---

## 5. If it still crashes

- In **Deploy logs**, copy the **exact** error line and share it.
- Confirm again: **Custom Start Command** is empty and **port is 80**.
