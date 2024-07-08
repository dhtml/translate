import app from 'flarum/forum/app';
import addLanguageMenu from "./includes/addLanguageMenu";

app.initializers.add('dhtml/translate', () => {
  async function makeAsyncRequest() {
      fetch('/api/translate-queue')
        .then(response => {
          // Handle response (if needed)
          console.log('Async queue initiated');
        })
        .catch(error => {
          console.error('Error initiating async queue request:', error);
        });
  }

  addLanguageMenu();

  // Call the async function after initialization
  makeAsyncRequest();
});
