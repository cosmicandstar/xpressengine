<?php
/**
 * This file is abstract Media class
 *
 * PHP version 7
 *
 * @category    Media
 * @package     Xpressengine\Media
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link        https://xpressengine.io
 */

namespace Xpressengine\Media\Models;

use Xpressengine\Media\Models\Meta\Meta;
use Xpressengine\Storage\File;
use Xpressengine\Media\MimeTypeFilter;

/**
 * Abstract class Media
 *
 * @category    Media
 * @package     Xpressengine\Media
 * @author      XE Developers <developers@xpressengine.com>
 * @copyright   2015 Copyright (C) NAVER Corp. <http://www.navercorp.com>
 * @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html LGPL-2.1
 * @link        https://xpressengine.io
 */
abstract class Media extends File
{
    use MimeTypeFilter;

    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';

    /**
     * Available mime type
     *
     * @var array
     */
    protected static $mimes = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url', 'meta'];

    /**
     * Make media model
     *
     * @param File $file file model
     * @return static
     */
    public static function make(File $file)
    {
        $model = new static();

        foreach ($file->getAttributes() as $key => $val) {
            $model->{$key} = $val;
        }

        $model->exists = $file->exists;

        return $model;
    }

    /**
     * Returns available mime type
     *
     * @return array
     */
    public static function getMimes()
    {
        return static::$mimes;
    }

    /**
     * Meta data relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function meta()
    {
        $class = $this->getMetaModel();

        $instance = new $class;

        return $this->hasOne($class, $instance->getForeignKey());
    }

    /**
     * Returns meta data model for current model
     *
     * @return string
     */
    abstract public function getMetaModel();

    /**
     * Rendered media
     *
     * @param array $option rendering option
     * @return string
     */
    abstract public function render(array $option = []);

    /**
     * Returns media type
     *
     * @return string
     */
    abstract public function getType();


    /**
     * Get the mutated url attribute.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->url();
    }

    /**
     * Get the meta data.
     *
     * @return Meta|null
     */
    public function getMetaAttribute($value)
    {
        return $value;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->url();
    }
}
