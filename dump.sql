DROP TABLE IF EXISTS "articles" CASCADE;
DROP SEQUENCE IF EXISTS articles_id_seq;
CREATE TABLE "public"."articles" (
    id INTEGER PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    text TEXT NOT NULL,
    author VARCHAR(255) NOT NULL,
    created_at timestamp DEFAULT now(),
    title VARCHAR(255) NOT NULL
);
DROP TABLE IF EXISTS "comments" CASCADE;
DROP SEQUENCE IF EXISTS comments_id_seq;
CREATE TABLE "public"."comments" (
    id INTEGER PRIMARY KEY,
    author VARCHAR(255) NOT NULL,
    article_id INTEGER REFERENCES articles (id),
    parent_id integer REFERENCES comments (id) DEFAULT '0',
    body character varying(400),
    created_at timestamp DEFAULT now()
);