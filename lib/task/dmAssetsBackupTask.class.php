<?php

class dmAssetsBackupTask extends dmContextTask
{

  /**
   * @see sfTask
   */
  protected function configure()
  {
    parent::configure();

    $this->addOptions(array(
    ));

    $this->namespace = 'dm';
    $this->name = 'assets-backup';
    $this->briefDescription = 'Creates a backup of assets';

    $this->detailedDescription = $this->briefDescription;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->get('assets_backup')
    ->setLogCallable(array($this, 'customLog'))
    ->execute();
  }

  public function customLog($msg)
  {
    return $this->logSection('diem-assets-backup', $msg);
  }
}