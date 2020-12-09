DROP TABLE IF EXISTS ibexa_setting;
CREATE TABLE ibexa_setting (
  id SERIAL NOT NULL,
  "group" varchar(128) NOT NULL,
  identifier varchar(128) NOT NULL,
  value json NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT ibexa_setting_group_identifier UNIQUE ("group", identifier)
);
