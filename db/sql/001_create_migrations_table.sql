CREATE TABLE IF NOT EXISTS migrations (
    version INT UNSIGNED NOT NULL PRIMARY KEY,
    dateran TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
