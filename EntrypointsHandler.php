<?php

use Exception;

/**
 * Class EntrypointsHandler
 *
 * @package Local\Util
 */
class EntrypointsHandler
{
    /**
     * @var string
     */
    private $base;

    /**
     * @var string
     */
    private $entrypointsFile;

    /**
     * @var array
     */
    private $entryPoints;

    /**
     * @var array
     */
    private $chunks;

    /**
     * @var array
     */
    private $cssFiles;

    /**
     * @var array
     */
    private $jsFiles;


    /**
     * EntrypointsHandler constructor.
     *
     * @param string $base            Расположение директории ассетов относительно
     *                                DOCUMENT_ROOT.
     * @param string $entrypointsFile Имя entrypoints-файла.
     *
     * @throws Exception Стандартное исключение.
     */
    public function __construct(string $base = 'local/dist/', string $entrypointsFile = 'entrypoints.json')
    {
        $this->base = $base;
        $this->entrypointsFile = $entrypointsFile;

        $this->loadEntrypoints();
    }


    /**
     * Функция для загрузки файлов js/css из всех чанков.
     *
     * @return void
     */
    public function getFiles(): void
    {
        foreach ($this->chunks as $chunk) {
            $files = $this->entryPoints['app'][$chunk] ?? [];

            if (!empty($files)) {
                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $filePath) {
                    strpos($filePath, '.js') !== false
                        ? $this->jsFiles[] = $filePath
                        : $this->cssFiles[] = $filePath;
                }
            }
        }
    }

    /**
     * Функция для выгрузки сss файлов.
     * @return void
     */
    public function loadCss(): void
    {
        array_walk($this->cssFiles, function ($filePath) {
            echo '<link rel="stylesheet" href="' . $filePath . '">';
        });
    }

    /**
     * Функция для выгрузки js файлов.
     * @return void
     */
    public function loadJs(): void
    {
        array_walk($this->jsFiles, function ($filePath) {
            echo '<script src="'. $filePath .'"></script>';
        });
    }

    /**
     * Генерирует массив ассетов на основе файла манифеста.
     *
     * @return void
     * @throws Exception Стандартное исключение.
     */
    private function loadEntrypoints(): void
    {
        $entryPoints = json_decode(file_get_contents(
            $_SERVER['DOCUMENT_ROOT'] . '/' . $this->base . $this->entrypointsFile
        ), true);

        if (!(bool)$entryPoints) {
            throw new Exception('Entrypoints file not found!');
        }
        $this->chunks = array_keys($entryPoints['app'] ?? $entryPoints['main']);
        $this->entryPoints = $entryPoints;
    }
}
