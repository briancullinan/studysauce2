<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
Let your sponsor know about your study achievement.
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<div class="field-type-image field-name-field-photo-evidence field-widget-image-plupload ">
    <div class="form-item form-type-plupload-file">
        <div class="plupload" id="goals-plupload" style="position: relative;">
            <div class="plup-list-wrapper">
                <ul class="plup-list clearfix ui-sortable"></ul>
            </div>
            <div class="plup-filelist" id="goal-plupload-filelist">
                <table>
                    <tbody>
                    <tr class="plup-drag-info">
                        <td>
                            <div class="drag-main">Click here to select files</div>
                            <div class="drag-more">
                                <div>You can upload up to <strong>1</strong> files.</div>
                                <div>Allowed files types: <strong>png gif jpg jpeg</strong>.</div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="plup-bar clearfix">
                <input type="hidden" id="goal-upload-path" value="/node/plup/goals?plupload_token=d8aWkV2wfhmd7WFoAJR_n5evxX10ynvzWWote0kkeC8">
                <a href="#goal-select" class="plup-select" id="goals-plupload-select" style="z-index: 10;">Add</a>
                <a hre="#goal-upload" class="plup-upload" id="goals-plupload-upload">Upload</a>
                <div class="plup-progress"></div>
            </div>
            <div id="p192f3bt951h2v1t1jajk11gb1oo00_html5_container" class="plupload html5" style="position: absolute; width: 0px; height: 0px; overflow: hidden; z-index: 9; opacity: 0; top: 0px; left: 0px; background: transparent;"><input id="p192f3bt951h2v1t1jajk11gb1oo00_html5" style="font-size: 999px; position: absolute; width: 100%; height: 100%;" type="file" accept="image/png,image/gif,image/jpeg,image/*" multiple="multiple"></div></div>
    </div>
</div>
<div class="field-type-text-long field-name-field-message field-widget-text-textarea ">
    <div class="form-item form-type-textarea">
        <label>Message </label>

        <div class="form-textarea-wrapper resizable textarea-processed resizable-textarea"><textarea class="text-full form-textarea jquery_placeholder-processed" placeholder="Message" name="field_goals[und][0][field_message][und][0][value]" cols="60" rows="5"></textarea>
        </div>
    </div>
</div>
<?php $view['slots']->stop();

 $view['slots']->start('modal-footer') ?>
<button type="button" class="btn btn-primary">Save</button>
<?php $view['slots']->stop() ?>

