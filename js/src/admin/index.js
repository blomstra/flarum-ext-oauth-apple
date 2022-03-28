import app from 'flarum/admin/app';
import AppleOAuthPage from './components/AppleOAuthSettingsPage';

app.initializers.add('blomstra/oauth-apple', () => {
  app.extensionData.for('blomstra-oauth-apple').registerPage(AppleOAuthPage);
});
