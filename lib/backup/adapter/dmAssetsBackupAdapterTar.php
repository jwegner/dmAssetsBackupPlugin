<?php

class dmAssetsBackupAdapterTar extends dmAssetsBackupAdapter
{
  public function execute($directoryDestination, $directorySource)
  {
    //tar -cf $destination $source
    $fileName = $this->getFileName();
    $destinationFile = dmOs::join($directoryDestination, $fileName);
    if (chdir($directorySource)) $command = sprintf('tar -cf %s *', $destinationFile);
    else $command = sprintf('tar -cf %s %s', $destinationFile, $directorySource);
    $success = $this->filesystem->execute($command);
    return ($success) ? $fileName : false;
  }
}
