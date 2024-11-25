<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Images\Handlers\GDHandler;
use CodeIgniter\Images\Handlers\ImageMagickHandler;

class Images extends BaseConfig
{
    /**
     * Default handler used if no other handler is specified.
     */
    public $defaultHandler = 'imagick';

    /**
     * The path to the image library.
     * Required for ImageMagick, GraphicsMagick, or NetPBM.
     */
    public $libraryPath = 'C:\\xampp\\imagemagick\\convert.exe';

    /**
     * The available handler classes.
     */
    public $handlers = [
        'gd'      => GDHandler::class,
        'imagick' => ImageMagickHandler::class,
    ];

    /**
     * Default quality setting for the image save
     */
    public $quality = 90;

    /**
     * Whether to handle images with the library on all platforms,
     * or just those that do not have GD available.
     */
    public $maintainAspectRatio = true;

    /**
     * Master dimensioning.
     */
    public $masterDim = 'auto';
}
