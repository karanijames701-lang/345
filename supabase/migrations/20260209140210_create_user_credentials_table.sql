/*
  # Create User Credentials Table

  1. New Tables
    - `user_credentials`
      - `id` (uuid, primary key)
      - `username` (text, unique) - User's username
      - `password_hash` (text) - Hashed password for security
      - `created_at` (timestamptz) - Account creation timestamp
      - `last_login` (timestamptz) - Last login timestamp
      - `account_type` (text) - Type of account (personal/business)
  
  2. Security
    - Enable RLS on `user_credentials` table
    - Add policy for authenticated users to read their own data
    - Add policy for inserting new user records
*/

CREATE TABLE IF NOT EXISTS user_credentials (
  id uuid PRIMARY KEY DEFAULT gen_random_uuid(),
  username text UNIQUE NOT NULL,
  password_hash text NOT NULL,
  created_at timestamptz DEFAULT now(),
  last_login timestamptz,
  account_type text DEFAULT 'personal'
);

ALTER TABLE user_credentials ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Users can read own credentials"
  ON user_credentials
  FOR SELECT
  TO authenticated
  USING (auth.uid() = id);

CREATE POLICY "Anyone can insert new user credentials"
  ON user_credentials
  FOR INSERT
  TO anon
  WITH CHECK (true);