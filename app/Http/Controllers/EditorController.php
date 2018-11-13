<?php
namespace App\Http\Controllers;

use Auth;
use Gate;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Xpressengine\Editor\EditorHandler;
use Xpressengine\Http\Request;
use Xpressengine\Permission\Instance;
use Xpressengine\Permission\PermissionSupport;
use Xpressengine\Presenter\RendererInterface;
use Xpressengine\Support\Exceptions\AccessDeniedHttpException;
use Xpressengine\Support\Exceptions\InvalidArgumentException;
use XeConfig;
use XeEditor;
use XeMenu;
use XeMedia;
use XePresenter;
use XeUser;
use XeStorage;
use XeTag;

class EditorController extends Controller
{
    use PermissionSupport;
    
    public function setting(Request $request, $instanceId)
    {
        $editorId = $request->get('editorId');
        if (empty($editorId)) {
            $editorId = null;
        }

        XeEditor::setInstance($instanceId, $editorId);

        if (!$url = XeMenu::getInstanceSettingURIByItemId($instanceId)) {
            return redirect()->back();
        } else {
            return redirect($url);
        }
    }

    public function getDetailSetting($instanceId)
    {
        return XePresenter::make('editor.detail', [
            'instanceId' => $instanceId,
            'config' => XeEditor::getConfig($instanceId),
        ]);
    }

    public function postDetailSetting(Request $request, $instanceId)
    {
        $uploadMaxSize = $this->getMegaSize(ini_get('upload_max_filesize'));
        $postMaxSize = $this->getMegaSize(ini_get('post_max_size'));
        $phpFileMaxSize = min($uploadMaxSize, $postMaxSize);

        $defaultRule = [
            'fileMaxSize' => 'numeric|min:1|max:' . $phpFileMaxSize,
        ];

        $attachMaxSizeRule = [];
        if ($editAttachMaxSize = $request->get('attachMaxSize')) {
            if ($editAttachMaxSize != 0) {
                $editorConfig = XeEditor::getConfig($instanceId);
                $fileMaxSize = $request->get('fileMaxSize', $editorConfig->get('fileMaxSize'));

                $attachMaxSizeRule = ['attachMaxSize' => 'numeric|min:' . $fileMaxSize];
            } else {
                $attachMaxSizeRule = ['attachMaxSize' => 'numeric'];
            }
        }

        $rule = array_merge($defaultRule, $attachMaxSizeRule);

        $this->validate($request, $rule);

        XeEditor::setConfig($instanceId, [
            'height' => $request->get('height'),
            'fontSize' => $request->get('fontSize'),
            'fontFamily' => empty($request->get('fontFamily')) ? null : $request->get('fontFamily'),
            'stylesheet' => $request->get('stylesheet'),
            'uploadActive' => !is_null($request->get('uploadActive')) ? !!$request->get('uploadActive') : null,
            'fileMaxSize' => $request->get('fileMaxSize'),
            'attachMaxSize' => $request->get('attachMaxSize'),
            'extensions' => empty($request->get('extensions')) ? null : strtolower($request->get('extensions')),
        ]);

        return redirect()->back()->with('alert', ['type' => 'success', 'message' => xe_trans('xe::saved')]);
    }

    public function getPermSetting($instanceId)
    {
        return XePresenter::make('editor.perm', [
            'instanceId' => $instanceId,
            'permArgs' => $this->getPermArguments(
                XeEditor::getPermKey($instanceId),
                ['html', 'tool', 'upload', 'download']
            ),
        ]);
    }

    public function postPermSetting(Request $request, $instanceId)
    {
        $this->permissionRegister($request, XeEditor::getPermKey($instanceId), ['html', 'tool', 'upload', 'download']);

        return redirect()->back()->with('alert', ['type' => 'success', 'message' => xe_trans('xe::saved')]);
    }

    public function getToolSetting($instanceId)
    {
        $tools = XeEditor::getToolAll();

        $inherit = !XeEditor::getConfig($instanceId)->getPure('tools');

        $toolIds = XeEditor::getTools($instanceId);
        $activated = array_intersect_key($tools, array_flip($toolIds));
        $activated = array_merge(array_flip($toolIds), $activated);
        $deactivated = array_diff_key($tools, array_flip($toolIds));

        $items = [];
        foreach ($activated as $key => $item) {
            $items[$key] = ['class' => $item, 'activated' => true];
        }
        foreach ($deactivated as $key => $item) {
            $items[$key] = ['class' => $item, 'activated' => false];
        }

        return XePresenter::make('editor.tool', [
            'instanceId' => $instanceId,
            'items' => $items,
            'inherit' => $inherit,
        ]);
    }

    public function postToolSetting(Request $request, $instanceId)
    {
        if ($request->get('inherit')) {
            XeEditor::unsetTools($instanceId);
        } else {
            XeEditor::setTools($instanceId, $request->get('tools', []));
        }

        return redirect()->back()->with('alert', ['type' => 'success', 'message' => xe_trans('xe::saved')]);
    }

    public function redirectGlobalSetting()
    {
        return redirect()->route('settings.editor.global.detail');
    }

    public function getGlobalDetailSetting()
    {
        return XePresenter::make('editor.global.detail', [
            'config' => XeEditor::getGlobalConfig(),
        ]);
    }

    public function postGlobalDetailSetting(Request $request)
    {
        $uploadMaxSize = $this->getMegaSize(ini_get('upload_max_filesize'));
        $postMaxSize = $this->getMegaSize(ini_get('post_max_size'));
        $phpFileMaxSize = min($uploadMaxSize, $postMaxSize);

        $defaultRule = [
            'height' => 'required|numeric',
            'fontSize' => 'required',
            'fileMaxSize' => 'numeric|min:1|max:' . $phpFileMaxSize,
            ];

        $attachMaxSizeRule = [];
        if ($attachMaxSize = $request->get('attachMaxSize', 0)) {
            if ($attachMaxSize != 0) {
                $attachMaxSizeRule = ['attachMaxSize' => 'numeric|min:' . $request->get('fileMaxSize', 0)];
            } else {
                $attachMaxSizeRule = ['attachMaxSize' => 'numeric'];
            }
        }

        $rule = array_merge($defaultRule, $attachMaxSizeRule);

        $this->validate($request, $rule);

        XeEditor::setGlobalConfig([
            'height' => $request->get('height'),
            'fontSize' => $request->get('fontSize'),
            'fontFamily' => empty($request->get('fontFamily')) ? null : $request->get('fontFamily'),
            'stylesheet' => $request->get('stylesheet'),
            'uploadActive' => !!$request->get('uploadActive', false),
            'fileMaxSize' => $request->get('fileMaxSize', 0),
            'attachMaxSize' => $request->get('attachMaxSize', 0),
            'extensions' => empty($request->get('extensions')) ? null : strtolower($request->get('extensions')),
        ]);

        return redirect()->back()->with('alert', ['type' => 'success', 'message' => xe_trans('xe::saved')]);
    }

    public function getGlobalPermSetting()
    {
        return XePresenter::make('editor.global.perm', [
            'permArgs' => $this->getPermArguments(XeEditor::getPermKey(), ['html', 'tool', 'upload', 'download']),
        ]);
    }

    public function postGlobalPermSetting(Request $request)
    {
        $this->permissionRegister($request, XeEditor::getPermKey(), ['html', 'tool', 'upload', 'download']);

        return redirect()->back()->with('alert', ['type' => 'success', 'message' => xe_trans('xe::saved')]);
    }

    public function getGlobalToolSetting()
    {
        $tools = XeEditor::getToolAll();

        $toolIds = XeEditor::getGlobalTools();
        $activated = array_intersect_key($tools, array_flip($toolIds));
        $activated = array_merge(array_flip($toolIds), $activated);
        $deactivated = array_diff_key($tools, array_flip($toolIds));

        $items = [];
        foreach ($activated as $key => $item) {
            $items[$key] = ['class' => $item, 'activated' => true];
        }
        foreach ($deactivated as $key => $item) {
            $items[$key] = ['class' => $item, 'activated' => false];
        }

        return XePresenter::make('editor.global.tool', [
            'items' => $items,
        ]);
    }

    public function postGlobalToolSetting(Request $request)
    {
        XeEditor::setGlobalTools($request->get('tools', []));

        return redirect()->back()->with('alert', ['type' => 'success', 'message' => xe_trans('xe::saved')]);
    }

    /**
     * file upload
     *
     * @param Request       $request      request
     * @param string        $instanceId   instance id
     * @return RendererInterface
     */
    public function fileUpload(Request $request, $instanceId)
    {
        $uploadedFile = null;
        if ($request->file('file') !== null) {
            $uploadedFile = $request->file('file');
        } elseif ($request->file('image') !== null) {
            $uploadedFile = $request->file('image');
        }

        if ($uploadedFile === null) {
            throw new InvalidArgumentException;
        }

        $editor = XeEditor::get($instanceId);
        $config = $editor->getConfig();

        if (!$config->get('uploadActive') || Gate::denies('upload', new Instance(XeEditor::getPermKey($instanceId)))) {
            throw new AccessDeniedHttpException;
        }

        if ($config->get('fileMaxSize') * 1024 * 1024 < $uploadedFile->getSize() && !$editor->isPrivileged()) {
            throw new HttpException(
                Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
                xe_trans('xe::msgMaxFileSize', [
                    'fileMaxSize' => $config->get('fileMaxSize'),
                    'uploadFileName' => $uploadedFile->getClientOriginalName()
                ])
            );
        }
        $extensions = array_map(function ($v) {
            return trim($v);
        }, explode(',', $config->get('extensions', '')));
        if (array_search('*', $extensions) === false
            && !in_array(strtolower($uploadedFile->getClientOriginalExtension()), $extensions)) {
            throw new HttpException(
                Response::HTTP_NOT_ACCEPTABLE,
                xe_trans('xe::msgAvailableUploadingFiles', [
                    'extensions' => $config->get('extensions'),
                    'uploadFileName' => $uploadedFile->getClientOriginalName()
                ])
            );
        }

        $file = XeStorage::upload($uploadedFile, EditorHandler::FILE_UPLOAD_PATH);

        $media = null;
        $thumbnails = null;
        if (XeMedia::is($file) === true) {
            $media = XeMedia::make($file);
            $thumbnails = XeMedia::createThumbnails($media, EditorHandler::THUMBNAIL_TYPE);
        }

        return XePresenter::makeApi([
            'file' => $file,
            'media' => $media,
            'thumbnails' => $thumbnails,
        ]);
    }

    /**
     * get file source
     *
     * @param string        $instanceId instance id
     * @param string        $id         document id
     * @return void
     * @throws InvalidArgumentException
     */
    public function fileSource($instanceId, $id)
    {
        if (empty($id)) {
            throw new InvalidArgumentException;
        }

        $file = XeStorage::find($id);

        if (XeMedia::is($file) === true) {
            $dimension = 'L';
            if (\Agent::isMobile() === true) {
                $dimension = 'M';
            }
            $media = XeMedia::images()->getThumbnail(
                XeMedia::make($file),
                EditorHandler::THUMBNAIL_TYPE,
                $dimension
            );

            header('Content-type: ' . $media->mime);
            echo $media->getContent();
        }
    }

    /**
     * file download
     *
     * @param string        $instanceId instance id
     * @param string        $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function fileDownload($instanceId, $id)
    {
        if (empty($id) || !$file = XeStorage::find($id)) {
            throw new InvalidArgumentException;
        }

        if (Gate::denies('download', new Instance(XeEditor::getPermKey($instanceId)))) {
            throw new AccessDeniedHttpException;
        }

        return XeStorage::download($file);
    }

    /**
     * file destory
     *
     * @param string  $instanceId
     * @param string  $id
     * @return RendererInterface
     */
    public function fileDestroy($instanceId, $id)
    {
        if (empty($id) || !$file = XeStorage::find($id)) {
            throw new InvalidArgumentException;
        }

        if ($file->userId !== Auth::id() && Auth::user()->getRating() != 'super') {
            throw new AccessDeniedHttpException;
        }

        try {
            $result = XeStorage::delete($file);
        } catch (\Exception $e) {
            $result = false;
        }

        return XePresenter::makeApi([
            'deleted' => $result,
        ]);
    }

    /**
     * 해시태그 suggestion 리스트
     *
     * @param Request $request
     * @return mixed
     */
    public function hashTag(Request $request)
    {
        $tags = XeTag::similar($request->get('string'));

        $suggestions = [];
        foreach ($tags as $tag) {
            $suggestions[] = [
                'id' => $tag->id,
                'word' => $tag->word,
            ];
        }

        return XePresenter::makeApi($suggestions);
    }

    /**
     * 멘션 suggestion 리스트
     *
     * @param Request $request
     * @return mixed
     */
    public function mention(Request $request)
    {
        $suggestions = [];

        $string = $request->get('string');
        $users = XeUser::where('display_name', 'like', $string . '%')->where('id', '<>', Auth::user()->getId())->get();
        foreach ($users as $user) {
            $suggestions[] = [
                'id' => $user->getId(),
                'displayName' => $user->getDisplayName(),
                'profileImage' => $user->profileImage,
            ];
        }

        return XePresenter::makeApi($suggestions);
    }

    /**
    **
    * Get php.ini setting file size to MegaByte Size
    *
    * @param  string $originalSize php.ini setting value
    * @return float|int|mixed
    */
    protected function getMegaSize($originalSize)
    {
        $originalSize = strtoupper($originalSize);
        $unit = substr($originalSize, -1);
        $size = str_replace($unit, '', $originalSize);

        switch ($unit) {
            case 'K':
                $size = $size / 1024;
                break;

            case 'G':
                $size = $size * 1024;
                break;

            case 'T':
                $size = $size * 1024 * 1024;
                break;
        }

        return $size;
    }
}
