<?php

class dmAssetsBackupAdapterTar extends dmAssetsBackupAdapter
{
  public function execute($fileDestination, $directorySource)
  {
    //tar -cf $destination $source
    if (chdir($directorySource)) 
        $command = sprintf('tar -cf %s *', (preg_match("/".preg_quote('.tar') .'$/', $fileDestination)) ? $fileDestination : $fileDestination.'.tar');
    else       
        $command = sprintf('tar -cf %s %s',
            (preg_match("/".preg_quote('.tar') .'$/', $fileDestination)) ? $fileDestination : $fileDestination.'.tar',
            $directorySource
        );
    
    return $this->filesystem->execute($command);
  }
}
