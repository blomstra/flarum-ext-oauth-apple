import app from 'flarum/admin/app';
import Button from 'flarum/common/components/Button';

export default class UploadKeyFileButton extends Button {
  loading = false;

  view(vnode) {
    this.attrs.loading = this.loading;
    this.attrs.className = (this.attrs.className || '') + ' Button';

    const keyFilePath = app.data.settings['fof-oauth.apple.key_file_path'];

    if (keyFilePath) {
      this.attrs.onclick = this.remove.bind(this);

      return (
        <div>
          <p>
            <code>{keyFilePath}</code>
          </p>
          <p>{super.view({ ...vnode, children: app.translator.trans('fof-oauth.admin.settings.providers.apple.keyfile_upload.remove_button') })}</p>
        </div>
      );
    } else {
      this.attrs.onclick = this.upload.bind(this);
    }

    return super.view({ ...vnode, children: app.translator.trans('fof-oauth.admin.settings.providers.apple.keyfile_upload.upload_button') });
  }

  /**
   * Prompt the user to upload the key file.
   */
  upload() {
    if (this.loading) return;

    const $input = $('<input type="file">');

    $input
      .appendTo('body')
      .hide()
      .click()
      .on('change', (e) => {
        const body = new FormData();
        body.append(this.attrs.name, $(e.target)[0].files[0]);

        this.loading = true;
        m.redraw();

        app
          .request({
            method: 'POST',
            url: this.resourceUrl(),
            serialize: (raw) => raw,
            body,
          })
          .then(this.success.bind(this), this.failure.bind(this));
      });
  }

  /**
   * Remove the key file.
   */
  remove() {
    this.loading = true;
    m.redraw();

    app
      .request({
        method: 'DELETE',
        url: this.resourceUrl(),
      })
      .then(this.success.bind(this), this.failure.bind(this));
  }

  resourceUrl() {
    return app.forum.attribute('apiUrl') + '/oauth/apple/keyfile';
  }

  /**
   * After a successful upload/removal, reload the page.
   *
   * @param {object} response
   * @protected
   */
  success(response) {
    window.location.reload();
  }

  /**
   * If upload/removal fails, stop loading.
   *
   * @param {object} response
   * @protected
   */
  failure(response) {
    this.loading = false;
    m.redraw();
  }
}
