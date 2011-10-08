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
//      new sfCommandOption('module', null, sfCommandOption::PARAMETER_REQUIRED, 'The module name'),
//      new sfCommandOption('nb', null, sfCommandOption::PARAMETER_OPTIONAL, 'nb records to create', 20),
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