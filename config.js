import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2/+esm';

// Supabase configuration
const SUPABASE_URL = 'https://xfzccopiomrhzqmrgwim.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InhmemNjb3Bpb21yaHpxbXJnd2ltIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzA2MzgyMDYsImV4cCI6MjA4NjIxNDIwNn0.nKIvYhAx2lTosJwqyZn_IAZTkg_6lGTpcvR9gkzB-MU';

// Initialize Supabase client
export const supabase = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

// Hash password using Web Crypto API
async function hashPassword(password) {
  const encoder = new TextEncoder();
  const data = encoder.encode(password);
  const hashBuffer = await crypto.subtle.digest('SHA-256', data);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
  return hashHex;
}

// Save user credentials to database
export async function saveCredentials(username, password, accountType = 'personal') {
  try {
    const passwordHash = await hashPassword(password);

    const { data, error } = await supabase
      .from('user_credentials')
      .insert([
        {
          username: username,
          password_hash: passwordHash,
          account_type: accountType,
          created_at: new Date().toISOString()
        }
      ])
      .select();

    if (error) {
      console.error('Error saving credentials:', error);
      return { success: false, error: error.message };
    }

    return { success: true, data: data };
  } catch (err) {
    console.error('Exception saving credentials:', err);
    return { success: false, error: err.message };
  }
}

// Verify user credentials
export async function verifyCredentials(username, password) {
  try {
    const passwordHash = await hashPassword(password);

    const { data, error } = await supabase
      .from('user_credentials')
      .select('*')
      .eq('username', username)
      .eq('password_hash', passwordHash)
      .maybeSingle();

    if (error) {
      console.error('Error verifying credentials:', error);
      return { success: false, error: error.message };
    }

    if (data) {
      // Update last login timestamp
      await supabase
        .from('user_credentials')
        .update({ last_login: new Date().toISOString() })
        .eq('id', data.id);

      return { success: true, user: data };
    }

    return { success: false, error: 'Invalid credentials' };
  } catch (err) {
    console.error('Exception verifying credentials:', err);
    return { success: false, error: err.message };
  }
}
