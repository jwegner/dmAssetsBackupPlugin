<?php

class dmAssetsBackup extends dmConfigurable {

    protected
    $filesystem,
    $logCallable,
    $adapter,
    $extension;
    protected static $extensions = null;

    public function __construct(dmFilesystem $filesystem, $adapter, array $options) {
        $this->filesystem = $filesystem;
        $this->adapter = $adapter;
        if (is_null(self::$extensions))
            self::$extensions = self::loadExtensions();
        $this->initialize($options);
    }

    protected function initialize(array $options) {
        $options['backup_dir'] = str_replace('SF_ROOT_DIR', sfConfig::get('sf_root_dir'), $options['backup_dir']);
        $options['source_dir'] = str_replace('SF_UPLOAD_DIR', sfConfig::get('sf_upload_dir'), $options['source_dir']);
        $this->configure($options);
    }

    public function execute() {
        $eventLog = dmContext::getInstance()->getServiceContainer()->getService('event_log');
        $adapter = $this->getAdapter();
        $this->log(sprintf('About to backup directory: %s into %s', $this->getOption('source_dir'), $this->getOption('backup_dir')));
        $this->createDir();
        try {
            $fileName = $adapter->execute($this->getOption('backup_dir'), $this->getOption('source_dir'));
        } catch (Exception $e) {
            $fileName = false;
        }
        if ($fileName) {
            $this->log(sprintf('New backup file: %s', $fileName));
            $this->log('Assets backup - done.');
            $eventLog->log(array(
                'server' => $_SERVER,
                'action' => 'assets',
                'type' => 'Assets',
                'subject' => 'Backup created'
            ));
        } else {
            $this->log('Error: backup of assets is not executed properly.');
            $eventLog->log(array(
                'server' => $_SERVER,
                'action' => 'error',
                'type' => 'Assets',
                'subject' => 'Backup error'
            ));
        }
        return $fileName;
    }

    protected function getAdapter() {
        $adapters = sfConfig::get('dm_dmAssetsBackupPlugin_adapters');
        if (!isset($adapters[$this->adapter])) {
            throw new dmException(sprintf('Adapter "%s" is not supported. Available adapters are: %s', $this->adapter, implode(', ', array_keys($adapters))));
        }
        try {
            return new $this->adapter($this->filesystem, $adapters[$this->adapter]['use']);
        } catch (Exception $e) {
            throw new dmException(sprintf('Fatal error: adapter "%s" could not be found.', $this->adapter));
        }
    }

    public function getBackupFiles() {
        $finder = sfFinder::type('file')->ignore_version_control()->maxdepth(0)->sort_by_name();
        foreach (self::$extensions as $extension => $mime)
            $finder->name('*.' . $extension);
        return $finder->in($this->getBackupDirectory());
    }

    public function getSourceDirectory() {
        return $this->getOption('source_dir');
    }

    public function getBackupDirectory() {
        return $this->getOption('backup_dir');
    }

    protected function createDir() {
        if (!$this->filesystem->mkdir($this->getOption('backup_dir'))) {
            throw new dmException(sprintf('Can NOT create backup directory %s', $this->getOption('backup_dir')));
        }
    }

    public function setLogCallable($callable) {
        $this->logCallable = $callable;
        return $this;
    }

    protected function log($msg) {
        if (is_callable($this->logCallable)) {
            call_user_func($this->logCallable, $msg);
        }
    }

    protected static function loadExtensions() {
        $adapters = sfConfig::get('dm_dmAssetsBackupPlugin_adapters');
        $extensions = array();
        foreach ($adapters as $adapter) {
            if (is_array($adapter['extensions'])) {
                $extensions = array_merge($extensions, array_combine(array_flip($adapter['extensions']), $adapter['mimes']));
            }
            else
                $extensions[$adapter['extensions']] = $adapter['mimes'];
        }
        return $extensions;
    }

    public static function getExtensions() {
        return self::$extensions;
    }

    public static function getMime($extension) {
        if (isset(self::$extensions[$extension]))
            return self::$extensions[$extension];
        else
            return 'Unknown';
    }
    
    public function delete($file) {
        if (trim((string)$file) == '') throw new dmException('File name not provided.');
        return $this->filesystem->unlink(dmOs::join($this->getOption('backup_dir'), $file));
    }
    
    public function download($file) {
        if (trim((string)$file) == '') throw new dmException('File name not provided.');
        return readfile(dmOs::join($this->getOption('backup_dir'), $file));
    }
    
    public function isBackUpExist($file) {
        if (trim((string)$file) == '') throw new dmException('File name not provided.');
        return file_exists(dmOs::join($this->getOption('backup_dir'), $file));
    }

}