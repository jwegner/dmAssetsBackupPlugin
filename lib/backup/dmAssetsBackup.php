<?php

class dmAssetsBackup extends dmConfigurable
{
  protected
  $filesystem,
  $logCallable,
  $adapters;

  public function __construct(dmFilesystem $filesystem, array $adapters, array $options)
  {
    $this->filesystem = $filesystem;
    $this->adapters   = $adapters;

    $this->initialize($options);
  }
  
  protected function initialize(array $options)
  {
    $options['dir'] = str_replace('SF_ROOT_DIR', sfConfig::get('sf_root_dir'), $options['dir']);
    $options['source'] = sfConfig::get('sf_upload_dir');
    $this->configure($options);
  }

  public function getBackupDirectory() {
      return $this->getOption('dir');
  }

  public function getBackupFiles() {
      $finder = new dmFilesystem();
      $files = $finder->findFilesInDir($this->getBackupDirectory());
      return $files;
  }


  public function setLogCallable($callable)
  {
    $this->logCallable = $callable;

    return $this;
  }

  public function execute()
  {
    $adapter = $this->getAdapter();
    $filename = $this->getFile();
    $file   = dmOs::join($this->getOption('dir'), $filename);
    
    $this->log(sprintf('About to backup directory: %s', $this->getOption('source')));

    $this->createDir();

    $adapter->execute($file, $this->getOption('source'));

    $this->log('Done.');
    
    return $filename;
  }

  protected function createDir()
  {
    if(!$this->filesystem->mkdir($this->getOption('dir')))
    {
      throw new dmException(sprintf('Can NOT create dir %s', $this->getOption('dir')));
    }
  }

  protected function getFile()
  {
    $fileName = strtr($this->getOption('file_format'), array(
      '%year%'    => date('Y'),
      '%month%'   => date('m'),
      '%day%'     => date('d'),
      '%time%'    => date('H-i-s')
    ));

    return $fileName . '.' . sfConfig::get('dm_dmAssetsBackupPlugin_extension');
  }

  protected function getAdapter()
  {
    $adapterName = sfConfig::get('dm_dmAssetsBackupPlugin_adapter');
    if(!isset($this->adapters[$adapterName]))
    {
      throw new dmException(sprintf('%s is not supported. Available adapters are %s', $adapterName, implode(', ', array_keys($this->adapters))));
    }

    return new $this->adapters[$adapterName]($this->filesystem);
  }

  protected function log($msg)
  {
    if(is_callable($this->logCallable))
    {
      call_user_func($this->logCallable, $msg);
    }
  }
}