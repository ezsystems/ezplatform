#!/usr/bin/env bash
echo "Altering database, setting character set to utf8mb4"
echo "ALTER DATABASE ${MYSQL_DATABASE} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" | "${mysql[@]}"
