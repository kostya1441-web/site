const fs = require("fs");
const path = require("path");

const DATA_DIR = path.join(__dirname, "..", "data");
const CONTENT_FILE = path.join(DATA_DIR, "content.json");

let writeQueue = Promise.resolve();

function load() {
  const raw = fs.readFileSync(CONTENT_FILE, "utf-8");
  return JSON.parse(raw);
}

function saveSync(data) {
  const tmp = CONTENT_FILE + ".tmp";
  fs.writeFileSync(tmp, JSON.stringify(data, null, 2), "utf-8");
  fs.renameSync(tmp, CONTENT_FILE);
}

/**
 * Atomically read-modify-write the content store. `mutator` receives the
 * current data object, mutates it in place (or returns a replacement),
 * and the result is persisted. Calls are serialised through a queue so
 * concurrent admin requests cannot interleave and corrupt the file.
 */
function update(mutator) {
  writeQueue = writeQueue.then(() => {
    const data = load();
    const result = mutator(data) || data;
    saveSync(result);
    return result;
  });
  return writeQueue;
}

module.exports = { load, saveSync, update, CONTENT_FILE, DATA_DIR };
