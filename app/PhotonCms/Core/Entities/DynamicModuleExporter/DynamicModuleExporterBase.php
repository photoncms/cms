<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleExporter;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\DynamicModuleExporterTemplate\DynamicModuleExporterTemplateFactory;
use Photon\PhotonCms\Core\Helpers\BladeCompilerHelper;
use Photon\PhotonCms\Core\Helpers\RoutesHelper;

class DynamicModuleExporterBase
{
    /**
     * Type of the file to export to.
     *
     * @var string
     */
    protected $type = '';

    /**
     * IMPORTANT
     * All callable functions on file exporter.
     * These can be overridden by creating public methods of the same name in a descendant class.
     *
     * @var type
     */
    private $callable = [
        'export',
        'download',
        'store'
    ];

    /**
     * Base constructor.
     * Sets the type of the file to work with.
     * Use a factory for instantiation, do not instantiate directly!
     *
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Magic caller for the purpose of calling export methods.
     * Export methods are defined in $this->callable, and they can be
     * overridden by creating a public method of the same name in a descendant class.
     *
     * @param string $name
     * @param array $arguments [entries, filename, parameters]
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $entries = $arguments[0];
        $filename = ($arguments[1]) ? $arguments[1] : null;
        $parameters = ($arguments[2]) ? $arguments[2] : [];

        $filename = $this->prepareFileName($filename, $entries);

        if (in_array($name, $this->callable)) {
            if (is_array($entries) || $entries instanceof Collection) {
                $exporter = $this->makeMultipleExporter($entries, $filename, $parameters);
            } else {
                $exporter = $this->makeExporter($entries, $filename, $parameters);
            }
            switch ($exporter) {
                case ($exporter instanceof \Maatwebsite\Excel\Writers\LaravelExcelWriter) :
                    return $this->callLaravelExcelWriterAction($exporter, $name);
                    break;
                case ($exporter instanceof \Barryvdh\DomPDF\PDF) :
                    return $this->callPDFAction($exporter, $name, $filename);
                    break;
            }
        }
        else {
            throw new PhotonException('EXPORT_METHOD_DOESNT_EXIST', ['method' => $name]);
        }
    }

    /**
     * Handles LaravelExcelWriter exporter.
     *
     * @param \Maatwebsite\Excel\Writers\LaravelExcelWriter $exporter
     * @param string $name
     * @return mixed
     */
    private function callLaravelExcelWriterAction(\Maatwebsite\Excel\Writers\LaravelExcelWriter $exporter, $name)
    {
        $exporter->$name($this->type);

        if ($name === 'store') {
            return [
                'file_name' => RoutesHelper::getExportedFileDownloadUrl($exporter->filename.'.'.$exporter->ext),
                'expiration_time' => (new Carbon())->addSeconds(config('photon.exported_files_ttl'))->toIso8601String(),
                'ttl' => config('photon.exported_files_ttl')
            ];
        }
        elseif ($name === 'export') {
            return $exporter->$name($this->type);
        }
    }

    /**
     * Handles PDF exporter.
     *
     * @param \Barryvdh\DomPDF\PDF $exporter
     * @param string $name
     * @param string $filename
     * @return mixed
     */
    private function callPDFAction(\Barryvdh\DomPDF\PDF $exporter, $name, $filename)
    {
        $filename .= '.pdf';
        if ($name === 'store') {
            $fileNameAndPath = config('excel.export.store.path').'/'.$filename;
            $exporter->save($fileNameAndPath);
            return [
                'file_name' => RoutesHelper::getExportedFileDownloadUrl($filename),
                'expiration_time' => (new Carbon())->addSeconds(config('photon.exported_files_ttl'))->toIso8601String(),
                'ttl' => config('photon.exported_files_ttl')
            ];
        }
        elseif ($name === 'export') {
            return $exporter->stream($filename);
        }
        
        return $exporter->$name($filename);
    }

    /**
     * Contains a logic for determining the file name.
     *
     * @param string $filename
     * @param Collection|array $entries
     * @return string
     */
    protected function prepareFileName($filename, $entries)
    {
        // Default for single entry
        if (!is_array($entries) && !($entries instanceof Collection) && !$filename) {
            return $entries->anchor_text;
        }
        // Default for multiple entries
        elseif (!$filename) {
            if (is_array($entries) && !empty($entries)) {
                $className = get_class($entries[0]);
            }
            elseif ($entries instanceof Collection && !$entries->isEmpty()){
                $className = get_class($entries->first());
            }
            $className = explode('\\', $className);
            $className = array_pop($className);
            $filename = $className.'_'.time();
        }

        $filename = preg_replace("/[^a-zA-Z0-9.,+-]/", "", $filename);

        return $filename;
    }

    /**
     * Compiles the exporter template directory full path and name.
     *
     * @return string
     */
    protected function getExporterTemplateDirectory()
    {
        $class = get_class($this);
        $reflector = new \ReflectionClass($class);
        $classFileName = $reflector->getFileName();
        return dirname($classFileName).'/Templates';
    }

    /**
     * Compiles a blade template into an HTML string.
     *
     * @param string $templateName
     * @param array $data
     * @return string
     */
    protected function compileFromExporterTemplate($templateName, $data = [])
    {
        $templateContents = DynamicModuleExporterTemplateFactory::make($this->getExporterTemplateDirectory(), $templateName);

        return BladeCompilerHelper::bladeCompile($templateContents, $data);
    }
}