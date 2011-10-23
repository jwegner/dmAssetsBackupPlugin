<?php

abstract class dmAssetsBackupAdapter
{
  protected
  $filesystem,
  $connection;

  public function __construct(dmFilesystem $filesystem)
  {
    $this->filesystem = $filesystem;
  }

  abstract public function execute($fileDestination, $directorySource);
  
}