const bcrypt = require("bcryptjs");
const fs = require("fs");
const path = require("path");

const ADMIN_FILE = path.join(__dirname, "..", "data", "admin.json");

function readAdmin() {
  return JSON.parse(fs.readFileSync(ADMIN_FILE, "utf-8"));
}

function writeAdmin(data) {
  fs.writeFileSync(ADMIN_FILE, JSON.stringify(data, null, 2), "utf-8");
}

function verifyLogin(username, password) {
  const admin = readAdmin();
  if (username !== admin.username) return false;
  return bcrypt.compareSync(password, admin.passwordHash);
}

function setPassword(username, newPassword) {
  const admin = readAdmin();
  admin.username = username;
  if (newPassword) admin.passwordHash = bcrypt.hashSync(newPassword, 10);
  writeAdmin(admin);
}

function requireLogin(req, res, next) {
  if (req.session && req.session.loggedIn) return next();
  return res.redirect("/admin/login");
}

module.exports = { verifyLogin, setPassword, requireLogin, readAdmin };
