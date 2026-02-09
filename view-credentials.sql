-- View all saved user credentials
SELECT
  id,
  username,
  password_hash,
  account_type,
  created_at,
  last_login
FROM user_credentials
ORDER BY created_at DESC;

-- View only personal account credentials
SELECT
  username,
  password_hash,
  created_at
FROM user_credentials
WHERE account_type = 'personal'
ORDER BY created_at DESC;

-- View only business account credentials
SELECT
  username,
  password_hash,
  created_at
FROM user_credentials
WHERE account_type = 'business'
ORDER BY created_at DESC;

-- Count total credentials saved
SELECT
  account_type,
  COUNT(*) as total_count
FROM user_credentials
GROUP BY account_type;

-- View most recent 10 login attempts
SELECT
  username,
  account_type,
  created_at
FROM user_credentials
ORDER BY created_at DESC
LIMIT 10;

-- Search for specific username
SELECT
  username,
  password_hash,
  account_type,
  created_at,
  last_login
FROM user_credentials
WHERE username LIKE '%search_term%';
