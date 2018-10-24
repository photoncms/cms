<?php

namespace Photon\PhotonCms\Core\Commands;

use ComposerBump;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Photon\PhotonCms\Core\Helpers\LicenseKeyHelper;
use Illuminate\Filesystem\Filesystem;
use Talevskiigor\ComposerBump\Helpers\FileHelper;
use ZipArchive;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photon:update {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates Photon to newest version';

    protected $composerFileHelper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        FileHelper $composerFileHelper
    ) {
        $this->composerFileHelper = $composerFileHelper;
        parent::__construct();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!\App::environment('local', 'staging', 'development', 'testing')) {
            $this->info('...Photon update forbidden');
            return false;
        }

        $this->info("...Checking license");
        // check if license key exist
        $key = LicenseKeyHelper::checkLicenseKey();
        // ping home
        $validKey = LicenseKeyHelper::pingHome($key);        
        // store license key if it does not exist
        if(!$key) 
            LicenseKeyHelper::storeLiceseKey($validKey['body']['license_key']);

        $currentVersion = ComposerBump::getVersion();
        $this->info("...Installed version: " . $currentVersion);
        $this->info("...Latest version: " . $validKey['body']['newest_version']);
        
        if($validKey['body']['newest_version'] == $currentVersion) {
            $this->info("Photon instalation already up to date");
            return false;
        }

        // forbid update between major and minor version        
        $force = (bool) $this->option('force');
        $currentVersion = explode(".", $currentVersion); 
        $latestVersion = explode(".", $validKey['body']['newest_version']);
        if(($currentVersion[0] != $latestVersion[0] || $currentVersion[1] != $latestVersion[1]) && !$force) {
            $this->info("Major version difference, unable to update");
            return false;            
        }

        $directory = getcwd().'/tmp';

        // clear tmp folder
        $this->info("...Clearing old temporary files");
        $this->clearTmpFolder($directory);

        $this->info("...Downloading and extracting update");
        $this->download($zipFile = $this->makeFilename($directory))
             ->extract($zipFile, $directory)
             ->cleanUp($zipFile);

        $this->info("...Applying update");
        $this->copyCoreBeFiles();
        $this->copyCoreFeFiles();

        // clear tmp folder
        $this->info("...Clearing temporary files");
        $this->clearTmpFolder($directory);

        $this->info("...Updating composer");
        $this->composerFileHelper->setVersion($validKey['body']['newest_version'])->save();

        $this->info("Photon CMS Updated");
    }

    private function clearTmpFolder($directory)
    {        
        if (is_dir($directory)) {
            \File::deleteDirectory($directory);
        }
    }

    protected function copyCoreFeFiles()
    {
        $source = getcwd().'/tmp/cms-master/resources/assets/photonCms/core'; 
        $destination = getcwd().'/resources/assets/photonCms/core'; 

        \File::deleteDirectory($destination);
        \File::copyDirectory($source, $destination);
    }

    protected function copyCoreBeFiles()
    {
        $source = getcwd().'/tmp/cms-master/app/PhotonCms/Core'; 
        $destination = getcwd().'/app/PhotonCms/Core'; 

        \File::deleteDirectory($destination);
        \File::copyDirectory($source, $destination);
    }

    /**
     * Clean-up the Zip file.
     *
     * @param  string  $zipFile
     * @return $this
     */
    protected function cleanUp($zipFile)
    {
        \File::delete($zipFile);

        return $this;
    }

    /**
     * Generate a random temporary filename.
     *
     * @return string
     */
    protected function makeFilename($directory)
    {
        mkdir($directory);
        return $directory.'/laravel_'.md5(time().uniqid()).'.zip';
    }

    /**
     * Download the temporary Zip to the given file.
     *
     * @param  string  $zipFile
     * @param  string  $version
     * @return $this
     */
    protected function download($zipFile)
    {
        $ga = (new Client)->request('POST', 'https://www.google-analytics.com/collect', [
            'form_params' => [
                'v' => '1',
                't' => 'event',
                'tid' => 'UA-1936460-37',
                'cid' => '65ee52da-f6fa-4f0d-a1e5-d12dd4945679',
                'ec' => 'Downloads',
                'ea' => 'Update via artisan command',
            ]
        ]);
        
        $response = (new Client)->get('https://github.com/photoncms/cms/archive/master.zip');

        file_put_contents($zipFile, $response->getBody());

        return $this;
    }

    /**
     * Extract the Zip file into the given directory.
     *
     * @param  string  $zipFile
     * @param  string  $directory
     * @param  string  $version
     * @return $this
     */
    protected function extract($zipFile, $directory)
    {
        $archive = new ZipArchive;

        $archive->open($zipFile);

        $archive->extractTo($directory);

        $archive->close();

        return $this;
    }
}
