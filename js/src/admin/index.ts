import app from 'flarum/admin/app';
import { ConfigureWithOAuthPage } from '@fof-oauth';
import ExtendOAuthSettings from './ExtendOAuthSettings';

app.initializers.add('blomstra/oauth-apple', () => {
  app.extensionData.for('blomstra-oauth-apple').registerPage(ConfigureWithOAuthPage);

  ExtendOAuthSettings();
});
