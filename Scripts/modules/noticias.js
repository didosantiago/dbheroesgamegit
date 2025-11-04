DBH.noticias = (function() {
    var init = function() {
        texteditor();
    },
    texteditor = function(i) {
        if($('.text-editor').length){
            window.onload = function()  {
                CKEDITOR.replace('descricao', {
                    enterMode: CKEDITOR.ENTER_BR,
                    uiColor: '#CCCCCC',
                    language: 'pt-br',
                    filebrowserBrowseUrl: '../assets/ckeditor/kcfinder/browse.php?opener=ckeditor&type=files',
                    filebrowserImageBrowseUrl: '../assets/ckeditor/kcfinder/browse.php?opener=ckeditor&type=images',
                    filebrowserFlashBrowseUrl: '../assets/ckeditor/kcfinder/browse.php?opener=ckeditor&type=flash',
                    filebrowserUploadUrl: '../assets/ckeditor/kcfinder/upload.php?opener=ckeditor&type=files',
                    filebrowserImageUploadUrl: '../assets/ckeditor/kcfinder/upload.php?opener=ckeditor&type=images',
                    filebrowserFlashUploadUrl: '../assets/ckeditor/kcfinder/upload.php?opener=ckeditor&type=flash',
                    toolbar:
                    [
                      { name: 'basicstyles', items : [ 'Bold','Italic','Underline' ] },
                      { name: 'paragraph', items : [ 'NumberedList','BulletedList' ] },
                      { name: 'paragraph', items : [ 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
                      { name: 'styles', items : [ 'Font','FontSize' ] },
                      { name: 'colors', items : [ 'TextColor','BGColor' ] },
                      { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteFromWord','-','Undo','Redo' ] },                             
                      { name: 'tools', items : [ 'Maximize','-','About' ] },
                      { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
                      { name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] }
                    ],
                    height: "400px"
                });
            };
        }
    }
    
    return {
        init: init
    }
}());