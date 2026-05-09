#!/usr/bin/env bash
set -euo pipefail

BASE_URL=${BASE_URL:-"http://localhost:8000"}

csrf_response=$(curl -sS -c /tmp/aqualimpa.cookies "$BASE_URL/backend/csrf.php")
csrf_token=$(php -r '$d=json_decode(stream_get_contents(STDIN),true); echo $d["csrf_token"] ?? "";' <<< "$csrf_response")

if [[ -z "$csrf_token" ]]; then
  echo "CSRF token not returned"
  exit 1
fi

status_login=$(curl -s -o /dev/null -w "%{http_code}" -b /tmp/aqualimpa.cookies -X POST "$BASE_URL/backend/login.php" -d "email=test@example.com&senha=12345678&csrf_token=$csrf_token")

if [[ "$status_login" != "401" && "$status_login" != "422" && "$status_login" != "500" ]]; then
  echo "Unexpected login status: $status_login"
  exit 1
fi

echo "Integration checks passed"
