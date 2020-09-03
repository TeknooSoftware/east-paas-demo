jQuery(document).ready(function() {
    $('textarea.form-control.rich-text[data-type!="raw"]').each(function() {
        var that = this;
        $(this).trumbowyg({
            btnsDef: {
                websiteImage: {
                    fn: function () {
                        $.get($(that).data('media-list'), function (body) {
                            // Open a modal box
                            var $body = $(body);
                            var imgSrc = 1;
                            var imgAlt = null;

                            $body.find('img').click(function () {
                                imgSrc = $(this).attr('src');
                                imgAlt = $(this).attr('alt');

                                $(this).parent().parent().find('li').removeClass('selected');
                                $(this).parent().addClass('selected');
                            });

                            var $modal = $(that).trumbowyg('openModal', {
                                title: 'Select a media',
                                content: $body
                            });

                            $modal.on('tbwconfirm', function(e){
                                $(that).trumbowyg('closeModal');

                                if (null != imgSrc) {
                                    $(that).trumbowyg('execCmd', {
                                        cmd: 'insertHtml',
                                        param: '<img src="' + imgSrc + '" alt="' + imgAlt + '"/>',
                                        forceCss: false
                                    });
                                }
                            });

                            $modal.on('tbwcancel', function(e){
                                $(that).trumbowyg('closeModal');
                            });
                        });
                    },
                    tag: 'Image',
                    title: 'Insert an image',
                    text: 'Img',
                    isSupported: function () {
                        return true;
                    },
                    key: 'I',
                    param: '',
                    forceCSS: false,
                    class: '',
                    ico: 'insertImage',
                    hasIcon: true,
                    semantic: false
                }
            },
            btns: [
                ['viewHTML'],
                ['undo', 'redo'], // Only supported in Blink browsers
                ['formatting'],
                ['strong', 'em', 'del'],
                ['superscript', 'subscript'],
                ['link'],
                ['insertImage', 'websiteImage'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['removeformat'],
                ['fullscreen']
            ]
        });
    });
});