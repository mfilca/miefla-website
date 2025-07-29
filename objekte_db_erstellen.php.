<?php
// Datenbank öffnen/erstellen
$db = new SQLite3('objekte.db');

// Tabelle: Mietobjekte
$db->exec("
CREATE TABLE IF NOT EXISTS objekte (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  titel TEXT NOT NULL,
  kategorie TEXT NOT NULL,
  unterkategorie TEXT NOT NULL,
  beschreibung TEXT NOT NULL,
  preis REAL NOT NULL,
  kaution REAL NOT NULL,
  von TEXT NOT NULL,
  bis TEXT NOT NULL,
  region TEXT NOT NULL,
  reserviert INTEGER DEFAULT 0
)
");

// Tabelle: Bilder zum Mietobjekt
$db->exec("
CREATE TABLE IF NOT EXISTS bilder (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  objekt_id INTEGER NOT NULL,
  pfad TEXT NOT NULL,
  FOREIGN KEY(objekt_id) REFERENCES objekte(id)
)
");

// Tabelle: Zusatzoptionen (ankreuzbare Extras)
$db->exec("
CREATE TABLE IF NOT EXISTS zusatzoptionen (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  objekt_id INTEGER NOT NULL,
  beschreibung TEXT NOT NULL,
  preis REAL NOT NULL,
  kaution REAL NOT NULL,
  FOREIGN KEY(objekt_id) REFERENCES objekte(id)
)
");

// Tabelle: Benutzer (für Login/Registrierung)
$db->exec("
CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  passwort TEXT NOT NULL
)
");

// Tabelle: Anfragen von Mietern
$db->exec("
CREATE TABLE IF NOT EXISTS anfragen (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  objekt_id INTEGER NOT NULL,
  name TEXT NOT NULL,
  nachricht TEXT,
  zeitstempel TEXT,
  status TEXT DEFAULT 'offen',
  FOREIGN KEY(objekt_id) REFERENCES objekte(id)
)
");

echo "<p style='color:lime;'>✅ Datenbank 'objekte.db' wurde vollständig erstellt.</p>";
?>
