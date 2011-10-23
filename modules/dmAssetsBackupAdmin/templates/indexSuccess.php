<?php

function formatfilesize( $data ) {
    if( $data < 1024 ) return $data . ' '. __('bytes');
    else if( $data < 1024000 ) return round( ( $data / 1024 ), 1 ) . ' '. __('KB');
    else return round( ( $data / 1024000 ), 1 ) . ' ' . _('MB');
}

$totalBackupSize = 0;

echo $form->open();
echo _open('table#dm_page_meta_table', array('json' => array(
  'translation_url' => _link('dmPage/tableTranslation')->getHref()
)));
echo _open('thead')._open('tr');
echo _tag('th', _tag('input.check_all', array('type'=>'checkbox')));
echo _tag('th', __('Date created'));
echo _tag('th', __('Size'));
echo _tag('th', __('Status'));
echo _tag('th', __('Actions'));

echo _close('tr')._close('thead');
echo _open('tbody');

foreach ($files as $file) {
    echo _open('tr');
        echo _tag('td', $form[dmString::slugify($file['path'])]->render());
        echo _tag('td', date('r',$file['date']->getTimestamp()));
        echo _tag('td', formatfilesize($file['filesize']));
        echo _tag('td', ($file['filesize']) ? _tag('span.boolean.s16block.s16_tick') : _tag('span.boolean.s16block.s16_cross'));
        echo _open('td');
            echo _open('ul.sf_admin_td_actions');
                echo _tag('li.sf_admin_action_download', 
                        _tag('a.s16.s16_download.dm_download_link.sf_admin_action', 
                                array('title'=>__('Download this backup'), 'json'=>array(
                                    'link'=>  _link('dmAssetsBackupAdmin/download')->getHref(),
                                    'file'=> $file['path']                                       
                                )), __('Download')));
                echo _tag('li.sf_admin_action_delete', 
                        _tag('a.s16.s16_delete.dm_delete_link.sf_admin_action', 
                                array('title'=>__('Delete this backup'), 'json'=>array(                                    
                                    'message' => __('Are you shore that you want to delete this backup file?')
                                )), __('Delete')));
            echo _close('ul');
        echo _close('td');
        $totalBackupSize += $file['filesize'];
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
    echo _tag('div.dm_help_wrap', array('style'=>'float:right; margin-top:2px;'), __('Total backup stored in: '). " ". formatfilesize($totalBackupSize));
echo _close('div');

echo $form->renderHiddenFields();

if (isset ($downloadFileName)) {
    echo _open('div#flash.automatic_download', array('title'=>__('Close'), 'json'=>array(
                'link'=>  _link('dmAssetsBackupAdmin/download')->getHref(),
                    'link'=>  _link('dmAssetsBackupAdmin/download')->getHref(),
                    'file'=> $downloadFileName
                    )));
        echo _open('ul.flashs infos');
            echo _open('li.flash.ui-corner-all.info');
                echo _tag('span.icon.fleft.mr5.s16block.s16_info');
                echo __('Your download should start shortly, if not, please click here to start download.');
            echo _close('li');
        echo _close('ul');
    echo _close('div');
}

echo $form->close();

