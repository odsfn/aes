<?php 
Yii::app()->clientScript->registerCss('itemEdit', '#form-container {display: none;}');

if ($this->getModule()->ajaxImageNavigation) : ?>
<script type="text/javascript">
$(function() {
    var galleryItemType = '<?= $this->getAction()->albumItemType; ?>';
    var GalleryItemMoving = function() {
        var space = $('#photo-space').val(),
            currentItemSpace = function() { 
                var val;
                
                if ($('#'+galleryItemType+'_album_id').length == 0)
                    val = 'removed';
                else
                    val = $('#'+galleryItemType+'_album_id').val() || 'without_album';
                
                return val; 
            },
            startNextUrl = $('div.pagination .next > a').attr('href'),
            isNextUrlChanged = false;
        
        //fixes next button after current photo was removed
        var fixNextLink = function() {
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
        
        this.check = function() {
            
            if (!startNextUrl)  //pagination was not found
                return;
            
            if (space == 'all') {
                if (currentItemSpace() == 'removed')
                    fixNextLink();
                
                return;
            }
            
            if (space == currentItemSpace()) {
                if (isNextUrlChanged) {
                    $('div.pagination .next > a').attr('href', startNextUrl);
                    isNextUrlChanged = false;
                } else
                    return;
            } else {
                if (isNextUrlChanged)
                    return;
                else {
                    fixNextLink();
                    isNextUrlChanged = true;
                }   
            }
        }
    };
    
    var currentGalleryItemMoving = new GalleryItemMoving();    
    
    function supports_history_api() {
        return !!(window.history && history.replaceState);
    }
    
    function updateHistory() {
        var exactHref = $('#current-photo-href').val();
        if (supports_history_api()) {
            window.history.replaceState({photo: exactHref}, $(document).find("title").text(), exactHref);
        }
    }
    
    var loadGalleryItem = function(href) {
        href += '?_dc=' + (new Date()).getTime();
        $('#image-view .pagination li').addClass('disabled');
        $('#image-view .pagination').append('<p>Loading...</p>');
        $('#image-view-container').load(href + ' #image-view', function(respText, textStatus, xhr) {
            updateHistory();
            currentGalleryItemMoving = new GalleryItemMoving();
        });
    };
    
    //make ajax navigation
    $('#image-view-container').on('click', '#image-view .pagination a', function(event){
        event.preventDefault();
        var el = $(this);
        if (el.parent().hasClass('disabled'))
            return;
        
        loadGalleryItem(el.attr('href'));
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
                        currentGalleryItemMoving.check();
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
                    currentGalleryItemMoving.check();
                } else
                    $('#form-container').html(response.html);
            },
            dataType: 'json'
        });
    });
    
    updateHistory();
});
</script>
<?php endif; ?>
<input type="hidden" id="photo-space" 
   value="<?php 
            if($albumContext) 
                echo $albumContext;
            elseif($without_album)
                echo 'without_album';
            else
                echo 'all';
        ?>" 
>
<div id="image-view-container">
    <div id="image-view" class="row-fluid">
        <ul class="thumbnails">
            <li class="span12">
                <div class="thumbnail without-border">
                <?php 
                echo $model->show(); 
                ?>
                </div>
            </li>
        </ul>
        <div class="row-fluid">
            <div class="span10">
                <?php $this->renderPartial($this->getAction()->viewGalleryItemDetailsPanel, array(
                    'model' => $model,
                    'canEdit' => $canEdit,
                    'albumContext' => $albumContext,
                    'target_id' => $target_id
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