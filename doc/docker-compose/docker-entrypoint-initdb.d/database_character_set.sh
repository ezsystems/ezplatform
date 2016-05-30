#!/usr/bin/env bash
echo "Altering database, setting character set to utf8"
echo "ALTER DATABASE ${MYSQL_DATABASE} CHARACTER SET utf8 COLLATE utf8_general_ci;" | "${mysql[@]}"
