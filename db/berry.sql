DROP TABLE IF EXISTS "articles" CASCADE;
DROP SEQUENCE IF EXISTS articles_id_seq;
CREATE SEQUENCE articles_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."articles" (
    "id" integer DEFAULT nextval('articles_id_seq') NOT NULL,
    "description" character varying(255) NOT NULL,
    "text" text NOT NULL,
    "author" VARCHAR(255) NOT NULL,
    "created_at" timestamp DEFAULT now(),
    "title" character varying(255),
    CONSTRAINT "articles_pkey" PRIMARY KEY ("id")
) WITH (oids = false);


DROP TABLE IF EXISTS "authors" CASCADE;
DROP SEQUENCE IF EXISTS authors_id_seq;
-- CREATE SEQUENCE authors_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

-- CREATE TABLE "public"."authors" (
--     "id" integer DEFAULT nextval('authors_id_seq') NOT NULL,
--     "name" character varying NOT NULL,
--     "ssid" character varying(255) NOT NULL,
--     CONSTRAINT "authors_pkey" PRIMARY KEY ("id")
-- ) WITH (oids = false);


DROP TABLE IF EXISTS "comments" CASCADE;
DROP SEQUENCE IF EXISTS comments_id_seq;
CREATE SEQUENCE comments_id_seq INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;

CREATE TABLE "public"."comments" (
    "id" integer DEFAULT nextval('comments_id_seq') NOT NULL,
    "author" VARCHAR(255) NOT NULL,
    "article_id" integer NOT NULL,
    "parent_id" integer DEFAULT '0',
    "body" character varying(400),
    "created_at" timestamp DEFAULT now(),
    CONSTRAINT "comments_pkey" PRIMARY KEY ("id")
) WITH (oids = false);