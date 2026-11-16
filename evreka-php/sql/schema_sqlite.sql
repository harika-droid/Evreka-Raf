PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS users (
  id TEXT PRIMARY KEY, email TEXT UNIQUE, kadi TEXT, ad TEXT, role TEXT DEFAULT 'user',
  status TEXT, il TEXT, ilce TEXT, created INTEGER, data TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS books (
  id TEXT PRIMARY KEY, owner_id TEXT, ad TEXT, yazar TEXT, kat TEXT, tur TEXT,
  il TEXT, status TEXT, created INTEGER, data TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS requests (
  id TEXT PRIMARY KEY, book_id TEXT, from_id TEXT, to_id TEXT, status TEXT, created INTEGER, data TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS messages (
  id TEXT PRIMARY KEY, req_id TEXT, from_id TEXT, t INTEGER, data TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS reviews (
  id TEXT PRIMARY KEY, status TEXT, puan INTEGER, created INTEGER, data TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS contacts (id TEXT PRIMARY KEY, created INTEGER, data TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS notifs (
  id TEXT PRIMARY KEY, uid TEXT, read INTEGER, t INTEGER, data TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS wishes (id TEXT PRIMARY KEY, uid TEXT, ad TEXT, created INTEGER, data TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS ratings (
  id TEXT PRIMARY KEY, req_id TEXT, from_id TEXT, to_id TEXT, stars INTEGER, t INTEGER, data TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS reports (id TEXT PRIMARY KEY, from_id TEXT, status TEXT, t INTEGER, data TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS clubs (
  id TEXT PRIMARY KEY, ad TEXT, il TEXT, kat TEXT, owner_id TEXT, created INTEGER, data TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS announcements (id TEXT PRIMARY KEY, hedef TEXT, t INTEGER, data TEXT NOT NULL);
CREATE TABLE IF NOT EXISTS meta (k TEXT PRIMARY KEY, v TEXT);

CREATE INDEX IF NOT EXISTS ix_books_owner ON books(owner_id);
CREATE INDEX IF NOT EXISTS ix_req_book ON requests(book_id);
CREATE INDEX IF NOT EXISTS ix_msg_req ON messages(req_id);
CREATE INDEX IF NOT EXISTS ix_notif_uid ON notifs(uid);
CREATE INDEX IF NOT EXISTS ix_rating_to ON ratings(to_id);
