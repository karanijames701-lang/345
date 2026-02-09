// MySQL Database Configuration
// Update API_URL to point to your PHP API endpoint
const API_URL = 'http://localhost/api.php'; // Update this to your actual API URL

// Get all credentials (for admin/debugging)
export async function listCredentials() {
  try {
    const response = await fetch(`${API_URL}?action=list`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      }
    });

    const result = await response.json();

    if (!result.success) {
      console.error('Error fetching credentials:', result.error);
      return { success: false, error: result.error };
    }

    return { success: true, data: result.data.credentials };
  } catch (err) {
    console.error('Exception fetching credentials:', err);
    return { success: false, error: err.message };
  }
}

// Save user credentials to database
export async function saveCredentials(username, password, accountType = 'personal') {
  try {
    const response = await fetch(`${API_URL}?action=save`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        username: username,
        password: password,
        accountType: accountType
      })
    });

    const result = await response.json();

    if (!result.success) {
      console.error('Error saving credentials:', result.error);
      return { success: false, error: result.error };
    }

    return { success: true, data: result.data };
  } catch (err) {
    console.error('Exception saving credentials:', err);
    return { success: false, error: err.message };
  }
}

// Verify user credentials
export async function verifyCredentials(username, password) {
  try {
    const response = await fetch(`${API_URL}?action=verify`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        username: username,
        password: password
      })
    });

    const result = await response.json();

    if (!result.success) {
      console.error('Error verifying credentials:', result.error);
      return { success: false, error: result.error };
    }

    return { success: true, user: result.data.user };
  } catch (err) {
    console.error('Exception verifying credentials:', err);
    return { success: false, error: err.message };
  }
}
