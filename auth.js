import { saveCredentials, verifyCredentials } from './config.js';

// Handle Personal Login Form
document.addEventListener('DOMContentLoaded', function() {
  const personalForm = document.querySelector('#personalLoginStandAlone form');
  const businessForm = document.querySelector('#businessLoginStandAlone form');

  if (personalForm) {
    personalForm.addEventListener('submit', async function(e) {
      e.preventDefault();

      const username = document.getElementById('personal-username').value;
      const password = document.getElementById('personal-password').value;

      if (!username || !password) {
        alert('Please enter both username and password');
        return;
      }

      // Save credentials to database
      const result = await saveCredentials(username, password, 'personal');

      if (result.success) {
        console.log('Credentials saved successfully:', result.data);
        alert('Login credentials saved successfully!');
        // Clear form
        personalForm.reset();
      } else {
        console.error('Failed to save credentials:', result.error);
        alert('Error saving credentials: ' + result.error);
      }
    });
  }

  if (businessForm) {
    businessForm.addEventListener('submit', async function(e) {
      e.preventDefault();

      const companyId = document.getElementById('business-company-id').value;
      const userId = document.getElementById('business-user-id').value;
      const password = document.getElementById('business-password').value;

      if (!companyId || !userId || !password) {
        alert('Please enter all required fields');
        return;
      }

      // Combine company ID and user ID as username
      const username = `${companyId}:${userId}`;

      // Save credentials to database
      const result = await saveCredentials(username, password, 'business');

      if (result.success) {
        console.log('Business credentials saved successfully:', result.data);
        alert('Business login credentials saved successfully!');
        // Clear form
        businessForm.reset();
      } else {
        console.error('Failed to save business credentials:', result.error);
        alert('Error saving business credentials: ' + result.error);
      }
    });
  }
});

// Export functions for manual use if needed
export { saveCredentials, verifyCredentials };
