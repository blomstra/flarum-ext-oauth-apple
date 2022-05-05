import app from 'flarum/admin/app';
import AppleOAuthPage from './components/AppleOAuthSettingsPage';
import ExtendOAuthSettings from './ExtendOAuthSettings';

app.initializers.add('blomstra/oauth-apple', () => {
  app.extensionData.for('blomstra-oauth-apple').registerPage(AppleOAuthPage);

  ExtendOAuthSettings();
});
