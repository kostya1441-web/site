const express = require("express");
const session = require("express-session");
const path = require("path");
const crypto = require("crypto");

const publicRoutes = require("./routes/public");
const adminRoutes = require("./routes/admin");

const app = express();
const PORT = process.env.PORT || 3000;
const SESSION_SECRET = process.env.SESSION_SECRET || crypto.randomBytes(32).toString("hex");

app.disable("x-powered-by");
app.use(
  session({
    secret: SESSION_SECRET,
    resave: false,
    saveUninitialized: false,
    cookie: { maxAge: 1000 * 60 * 60 * 12, sameSite: "lax" },
  })
);

app.use("/assets", express.static(path.join(__dirname, "public", "assets")));
app.use("/uploads", express.static(path.join(__dirname, "public", "uploads")));

app.use("/admin", adminRoutes);
app.use("/", publicRoutes);

app.use((req, res) => res.status(404).send("Страница не найдена (404)"));

app.listen(PORT, () => {
  console.log(`Hazleton Pumps CMS running on http://localhost:${PORT}`);
  console.log(`Admin panel: http://localhost:${PORT}/admin/login`);
});
