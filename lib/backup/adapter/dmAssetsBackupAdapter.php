<?php

abstract class dmAssetsBackupAdapter {

    protected
    $filesystem,
    $extension;

    public function __construct(dmFilesystem $filesystem, $extension) {
        $this->filesystem = $filesystem;
        $this->extension = $extension;
    }

    protected function getFileName() {
        return date('Y-m-d-H-i-s-u').'.'.$this->extension;
    }


    abstract public function execute($directoryDestination, $directorySource);
}