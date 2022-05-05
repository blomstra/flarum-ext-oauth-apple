<?php

namespace Blomstra\OAuthApple\Api\Controller;

use Flarum\Api\Controller\ShowForumController;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laminas\Diactoros\UploadedFile;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class KeyFileUploadController extends ShowForumController
{
    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * @var Filesystem
     */
    protected $uploadDir;

    /**
     * @var string
     */
    protected $fileExtension = 'txt';

    /**
     * @var string
     */
    protected $filePathSettingKey = 'fof-oauth.apple.key_file_path';

    /**
     * @var string
     */
    protected $filenamePrefix = 'apple-keyfile';
    
    /**
     * @param SettingsRepositoryInterface $settings
     * @param Factory $filesystemFactory
     */
    public function __construct(SettingsRepositoryInterface $settings, Factory $filesystemFactory)
    {
        $this->settings = $settings;
        $this->uploadDir = $filesystemFactory->disk('apple-keyfile');
    }

    /**
     * {@inheritdoc}
     */
    public function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertAdmin();

        /** @var UploadedFile $file */
        $file = Arr::get($request->getUploadedFiles(), $this->filenamePrefix);

        if (($path = $this->settings->get($this->filePathSettingKey)) && $this->uploadDir->exists($path)) {
            $this->uploadDir->delete($path);
        }

        $uploadName = $this->filenamePrefix.'-'.Str::lower(Str::random(8)).'.'.$this->fileExtension;

        $this->uploadDir->put($uploadName, $file->getStream()->getContents());

        $this->settings->set($this->filePathSettingKey, $uploadName);

        return parent::data($request, $document);
    }
}
