<?php

use_helper('Date');
use_helper('File');
$totalBackupSize = 0;

echo _open('div.dm_assets_backup');
echo $form->open();
echo _open('table#dm_assets_backup_table', array('json' => array(
  'translation_url' => _link('dmPage/tableTranslation')->getHref()
)));
echo _open('thead')._open('tr');
echo _tag('th', _tag('input.check_all', array('type'=>'checkbox')));
echo _tag('th', __('Date created'));
echo _tag('th', __('Date accessed'));
echo _tag('th', __('Date modified'));
echo _tag('th', __('Group/Owner'));
echo _tag('th', __('Permissions'));
echo _tag('th', __('Mime'));
echo _tag('th', __('Size'));
echo _tag('th', __('Status'));
echo _tag('th', __('Actions'));

echo _close('tr')._close('thead');
echo _open('tbody');

foreach ($files as $file) {
    echo _open('tr');
        echo _tag('td', $form[$file['file']]->render());
        echo _tag('td', format_date($file['created'], 'g', $sf_user->getCulture()));
        echo _tag('td', format_date($file['accessed'], 'g', $sf_user->getCulture()));
        echo _tag('td', format_date($file['modified'], 'g', $sf_user->getCulture()));
        echo _tag('td', file_get_group($file['group'], 'name') .'/' . file_get_owner($file['owner'], 'name'));
        echo _tag('td', file_perms_to_human($file['permissions']));
        echo _tag('td', $file['mime']);
        echo _tag('td', file_format_size($file['size']));
        echo _tag('td', ($file['file'] && $file['size']>0) ? _tag('span.boolean.s16block.s16_tick') : _tag('span.boolean.s16block.s16_cross'));
        echo _open('td');
            echo _open('ul.sf_admin_td_actions');
                echo _tag('li.sf_admin_action_download', 
                        _tag('a.s16.s16_download.dm_download_link.sf_admin_action', 
                                array('title'=>__('Download this backup'), 'json'=>array(
                                    'link'=>  _link('dmAssetsBackupAdmin/download')->getHref(),
                                    'file'=> $file['file']                                       
                                )), __('Download')));
                echo _tag('li.sf_admin_action_delete', 
                        _tag('a.s16.s16_delete.dm_delete_link.sf_admin_action', 
                                array('title'=>__('Delete this backup'), 'json'=>array(   
                                    'link'=> _link('dmAssetsBackupAdmin/delete')->getHref(),
                                    'file'=> $file['file'],
                                    'message' => __('Are you shore that you want to delete this backup file?')
                                )), __('Delete')));
            echo _close('ul');
        echo _close('td');
        $totalBackupSize += $file['size'];
    echo _close('tr');
}

echo _close('tbody');
echo _close('table');

echo _open('div.dm_form_action_bar.dm_form_action_bar_bottom.clearfix');
    echo _open('ul.sf_admin_actions.clearfix');
        echo _tag('li', _tag('input', array('type'=>'submit', 'value'=>__('Backup now'), 'name'=>'_backup')));
        echo _tag('li', _tag('input', array('type'=>'submit', 'value'=>__('Backup and download'), 'name'=>'_backup_and_download')));
        echo _tag('li', _tag('input.batch_delete_button', array('type'=>'submit', 'value'=>__('Batch delete'), 'name'=>'_batch_delete', 'json'=>array(
            'message'=>__('Please select backup/s for batch delete.')
        ))));
    echo _close('ul');
    echo _tag('div.dm_help_wrap', array('style'=>'float:right; margin-top:2px;'), __('Total backup stored in: '). " ". file_format_size($totalBackupSize));
echo _close('div');

echo $form->renderHiddenFields();

if (isset ($backupFile)) {
    echo _open('div#flash.dm_download_link', array('title'=>__('Download this backup'), 'json'=>array(
                    'link'=>  _link('dmAssetsBackupAdmin/download')->getHref(),
                    'file'=> $backupFile
                    )));
        echo _open('ul.flashs infos');
            echo _open('li.flash.ui-corner-all.info');
                echo _tag('span.icon.fleft.mr5.s16block.s16_info');
                echo __('Click here to download just created backup.');
            echo _close('li');
        echo _close('ul');
    echo _close('div');
}

echo $form->close();
echo _close('div');