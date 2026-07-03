#!/bin/sh
set -e

echo "Waiting for PostgreSQL..."

until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME"; do
  sleep 1
done

echo "PostgreSQL is ready."

php artisan config:clear

echo "Creating database schema if needed..."

PGPASSWORD="$DB_PASSWORD" psql \
  -h "$DB_HOST" \
  -U "$DB_USERNAME" \
  -d "$DB_DATABASE" \
  -c "CREATE SCHEMA IF NOT EXISTS $DB_SCHEMA;"

SEEDED=$(PGPASSWORD="$DB_PASSWORD" psql \
  -h "$DB_HOST" \
  -U "$DB_USERNAME" \
  -d "$DB_DATABASE" \
  -tAc "SELECT to_regclass('$DB_SCHEMA.member');")

if [ -z "$SEEDED" ]; then
  echo "Seeding database..."
  php artisan db:seed --force
else
  echo "Database already seeded. Skipping seed."
fi

exec "$@"