<?php

class dmAssetsBackupAdminActions extends dmAdminBaseActions {

    protected $backupService;

    public function __construct($context, $moduleName, $actionName) {
        parent::__construct($context, $moduleName, $actionName);
        $this->backupService = $this->getService('assets_backup');
    }

    public function executeIndex(sfWebRequest $request) {
        if ($request->isMethod('post')) {
            if ($request->hasParameter('_backup') || $request->hasParameter('_backup_and_download')) $this->forward ('dmAssetsBackupAdmin', 'backup');
            if ($request->hasParameter('_batch_delete')) $this->forward ('dmAssetsBackupAdmin', 'batchDelete');
        }
        if ($this->getUser()->hasAttribute('dmAssetsBackup/lastBackupFile')) {
            $this->backupFile = $this->getUser()->getAttribute('dmAssetsBackup/lastBackupFile');
            $this->getUser()->getAttributeHolder()->remove('dmAssetsBackup/lastBackupFile');
        }
        
        $this->prepareFilesForForm(new dmForm());
    }

    public function executeBackup(sfWebRequest $request) {        
        if (!$request->isMethod('post') || !($request->hasParameter('_backup') || $request->hasParameter('_backup_and_download'))) $this->forward ('dmAssetsBackupAdmin', 'index'); 
        $this->form = new dmForm();        
        if ($this->form->bindAndValid($request)) {            
            $backupFile = $this->backupService->execute();
            if ($backupFile) {                
                $this->getUser()->setFlash('notice', 'Backup created successfully!');
                if ($request->hasParameter('_backup_and_download')) {                    
                    $this->getUser()->setAttribute('dmAssetsBackup/lastBackupFile', $backupFile);
                }
            } else {
                $this->getUser()->setFlash('error', 'Something went wrong, backup is not created properly.', false);
            }
        }
        $request->getParameterHolder()->remove('_backup');
        $request->getParameterHolder()->remove('_backup_and_download');
        $this->forward ('dmAssetsBackupAdmin', 'index'); 
    }

    public function executeBatchDelete(sfWebRequest $request) {
        if (!$request->isMethod('post') || !$request->hasParameter('_batch_delete')) $this->forward ('dmAssetsBackupAdmin', 'index'); 
        $this->prepareFilesForForm(new dmForm());
        $request->getParameterHolder()->remove('_batch_delete');
        if ($this->form->bindAndValid($request)) {
            $status = array(
                'error'=>false,
                'total_deleted'=>0,
                'scheduled_for_delete'=>0
            );            
            foreach ($this->files as $file) {
                if (isset($this->form[$file['file']]) && $this->form[$file['file']]->getValue()) {
                    $status['scheduled_for_delete']++;
                    if ($this->backupService->delete($file['file'])) $status['total_deleted']++;
                    else $status['error'] = true;
                }
            }
            if ($status['error']) $this->getUser()->setFlash(
                    'error', 
                    sprintf('Something went wrong, only %s file(s) are deleted form %s scheduled.', $status['total_deleted'], $status['scheduled_for_delete']), 
                    false);
            else $this->getUser()->setFlash('notice', sprintf('Total %s backup file(s) are successfuly deleted!', $status['total_deleted']));
        }
        $this->forward ('dmAssetsBackupAdmin', 'index'); 
    }

    public function executeDelete(sfWebRequest $request) {
        if (!$request->isMethod('post') || !$request->hasParameter('_delete') || !$request->hasParameter('_file_name')) $this->forward ('dmAssetsBackupAdmin', 'index');
        $this->form = new dmForm();        
        $request->getParameterHolder()->remove('_delete');
        if ($this->form->bindAndValid($request)) {
            if ($this->backupService->delete($request->getParameter('_file_name'))) {
                $this->getUser()->setFlash('notice', sprintf('Backup file %s is successfuly deleted!', $request->getParameter('_file_name')));
            } else {
                $this->getUser()->setFlash('error', sprintf('Something went wrong, backup file %s is not deleted!', $request->getParameter('_file_name')), false);
            }
        }
        $this->forward ('dmAssetsBackupAdmin', 'index');
    }

    public function executeDownload(sfWebRequest $request) {
        if ($this->backupService->isBackUpExist($fileName = $request->getParameter('_file_name'))) {
            $response = $this->getResponse();
            $this->setLayout(false);
            sfConfig::set('sf_web_debug', false);
            $response->clearHttpHeaders();
            $response->setHttpHeader('Content-Disposition', 'attachment; filename='.$fileName);
            $response->setHttpHeader('Pragma: public', true);
            $response->setContentType(dmAssetsBackup::getMime(substr(strrchr($fileName, '.'), 1)));
            $response->sendHttpHeaders();
            $response->setContent($this->backupService->download($fileName));
            return sfView::NONE;
        } else {
            $this->getUser()->setFlash('error', sprintf('Something went wrong, backup file does not exist!', $request->getParameter('_file_name')), false);
        }
    }

    protected function prepareFilesForForm($form) {
        $this->form = $form;
        $files = $this->backupService->getBackupFiles();
        $this->files = $this->parseFiles($files);
        $widgets = array();
        $validators = array();
        foreach ($this->files as $file) {
            $widgets[$file['file']] = new sfWidgetFormInputCheckbox();
            $validators[$file['file']] = new sfValidatorBoolean(array('required' => false));
        }
        $this->form->setWidgets($widgets);
        $this->form->setValidators($validators);
    }


    protected function parseFiles($files) {
        $result = array();
        foreach ($files as $file) $result[] = $this->parseFile($file);
        return $result;
    }

    protected function parseFile($file) {
        $stats = stat($file);
        $pathinfo = pathinfo($file);
        $result['full_path'] = $file;
        $result['file'] = $pathinfo['basename'];
        $result['extension'] = $pathinfo['extension'];
        $result['mime'] = dmAssetsBackup::getMime($pathinfo['extension']);
        $result['accessed'] = $stats['atime'];
        $result['modified'] = $stats['atime'];
        $result['created'] = $stats['atime'];
        $result['size'] = $stats['size'];
        $result['group'] = $stats['gid'];
        $result['owner'] = $stats['uid'];
        $result['permissions'] = $stats['mode'];
        clearstatcache();
        return $result;
    }    

}