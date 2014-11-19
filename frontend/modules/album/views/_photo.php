<?php 
Yii::app()->clientScript->registerCss('photoEdit', '#form-container {display: none;}');

if ($this->getModule()->ajaxImageNavigation) : ?>
<script type="text/javascript">
$(function() {
    var loadPhoto = function(href) {
        href += '?_dc=' + (new Date()).getTime();
        $('#image-view .pagination li').addClass('disabled');
        $('#image-view .pagination').append('<p>Loading...</p>');
        $('#image-view-container').load(href + ' #image-view');
    };
    
    //make ajax navigation
    $('#image-view-container').on('click', '#image-view .pagination a', function(event){
        event.preventDefault();
        var el = $(this);
        if (el.parent().hasClass('disabled'))
            return;
        
        loadPhoto(el.attr('href'));
    });
    
    //register ajax handler setAsAlbumCover
    $('#image-view-container').on('click', '.set-as-album-cover', function(event) {
        event.preventDefault();
        var el = $(this);
        
        $.ajax({
            url: el.attr('href'),
            success: function(html) {
                $('#image-view-container .set-as-album-cover').replaceWith(html);
            }
        });
    });
    
    var hideForm = function() {
        $('#form-container').hide();
        $('#details,#edit-photo').show();
    }
    
    $('#image-view-container')
        .on('click', '#edit-photo', function(event) {
            $(this).hide();
            $('#details').hide();
            $('#form-container').show();
        })
        .on('click', '#form-container button[type="reset"]', function(event) {
            hideForm();
        })
        .on('click', '#details-container a.photo-delete', function(event) {
            event.preventDefault();
            var el = $(this);

            $.ajax({
                type: 'POST',
                url: el.attr('href'),
                success: function(response) {
                    if(response.success) {
                        $('#details-container').html(response.html);
                        //fix next link
                        var nextLink = $('div.pagination .next > a'),
                            curNextHref = nextLink.attr('href'),
                            newNextHref;
                        
                        var replacer = function(match, p1, offset, string) {
                            var page = parseInt(p1) - 1;
                            return '/' + page;
                        };
                        
                        newNextHref = curNextHref.replace(/\/(\d+)\/?$/, replacer);
                        nextLink.attr('href', newNextHref);
                    }
                },
                dataType: 'json'
            });
        });
    
    //form ajax submit
    $('#image-view-container').on('click', '#form-container button[type="submit"]', function(event) {
        event.preventDefault();
        var el = $(this),
            form = $(el.parents('form#photo-form')[0]),
            submitUrl = form.attr('action');
        
        $.ajax({
            type: "POST",
            url: submitUrl,
            data: form.serialize(),
            success: function(response) {
                if(response.success) {
                    $('#details-container').replaceWith(response.html);
                    
                    hideForm();
                } else
                    $('#form-container').html(response.html);
            },
            dataType: 'json'
        });
    });
});
</script>
<?php endif; ?>
<div id="image-view-container">
    <div id="image-view" class="row-fluid">
        <ul class="thumbnails">
            <li class="span12">
                <div class="thumbnail without-border">
                <?php 
                echo CHtml::tag('img', 
                    array('src'=> 
                        ($model->path ? $this->getModule()->getComponent('image')->createAbsoluteUrl('1150x710', $model->path) : $this->getModule()->getAssetsUrl('img/no_album.png'))
                    )
                ); 
                ?>
                </div>
            </li>
        </ul>
        <div class="row-fluid">
            <div class="span10">
                <?php $this->renderPartial('/_photo_details_panel', array(
                    'model' => $model,
                    'canEdit' => $canEdit,
                    'albumContext' => $albumContext
                )); ?>
            </div>

            <div class="span2 text-right">
                <div class="pagination">
                <?php $this->widget('bootstrap.widgets.TbPager', array(
                    'pages' => $pages,
                    'maxButtonCount' => 0,
                    'alignment' => 'right',
                    'header' => ''
                ))?>
                    <p><?php echo ($pages->currentPage + 1) . ' из '. $pages->itemCount; ?></p>
                </div>
            </div>

        </div>
    </div>
</div>