-- Dump of table groups
------------------------------------------------------------

DROP TABLE IF EXISTS groups;

CREATE TABLE groups (
  id serial NOT NULL PRIMARY KEY,
  name varchar NOT NULL,
  permissions text,
  created_at timestamp NOT NULL DEFAULT '1969-12-31 00:00:00',
  updated_at timestamp NOT NULL DEFAULT '1969-12-31 00:00:00'
);

ALTER TABLE groups ADD CONSTRAINT groups_name_unique UNIQUE (name);

-- Dump of table throttle
--------------------------------------------------------------

DROP TABLE IF EXISTS throttle;

CREATE TABLE throttle (
  id serial NOT NULL PRIMARY KEY,
  user_id integer unsigned NOT NULL,
  attempts integer NOT NULL,
  suspended smallint NOT NULL,
  banned smallint NOT NULL,
  last_attempt_at timestamp NOT NULL DEFAULT '1969-12-31 00:00:00',
  suspended_at timestamp NOT NULL DEFAULT '1969-12-31 00:00:00',
);



-- Dump of table users
--------------------------------------------------------------

DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id serial NOT NULL PRIMARY KEY,
  email varchar NOT NULL,
  password varchar NOT NULL,
  permissions text,
  activated smallint NOT NULL DEFAULT '0',
  activation_code varchar DEFAULT NULL,
  persist_code varchar DEFAULT NULL,
  reset_password_code varchar DEFAULT NULL,
  first_name varchar DEFAULT NULL,
  last_name varchar DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT '1969-12-31 00:00:00',
  updated_at timestamp NOT NULL DEFAULT '1969-12-31 00:00:00'
);

ALTER TABLE users ADD CONSTRAINT users_email_unique UNIQUE (email);


-- Dump of table users_groups
--------------------------------------------------------------

DROP TABLE IF EXISTS users_groups;

CREATE TABLE users_groups (
  id serial NOT NULL PRIMARY KEY,
  user_id integer unsigned NOT NULL,
  group_id integer unsigned NOT NULL,
);
