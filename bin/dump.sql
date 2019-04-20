DROP TABLE IF EXISTS "articles" CASCADE;
CREATE TABLE "articles" (
    id          SERIAL PRIMARY KEY,
    author      VARCHAR(255) NOT NULL,
    title       VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    body        TEXT NOT NULL,
    created_at  timestamp DEFAULT now(),
    updated_at  timestamp DEFAULT now(),
    deleted_at  timestamp
);
DROP TABLE IF EXISTS "comments" CASCADE;
CREATE TABLE "comments" (
    id          SERIAL PRIMARY KEY,
    author      VARCHAR(255) NOT NULL,
    article_id  INTEGER REFERENCES articles (id) ON DELETE RESTRICT,
    parent_id   INTEGER DEFAULT '0',
    body        VARCHAR(400) NOT NULL,
    created_at  timestamp DEFAULT now(),
    updated_at  timestamp DEFAULT now(),
    deleted_at  timestamp
);