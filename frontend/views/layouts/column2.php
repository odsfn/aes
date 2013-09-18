<?php $this->beginContent('//layouts/main'); ?>

        <div class="row-fluid">
            <div class="span10 offset1">

                <div class="row-fluid">

                    <div class="span3" id="column-left">
                        <?php 
                        if(!empty($this->clips['sidebar']))
                            echo $this->clips['sidebar'];
                        
                        ?>
                    </div><!-- #column-left -->

                    <div class="span9" id="column-right">
                        <?php echo $content; ?>
                    </div><!-- #column-right -->

                </div>
            </div>
        </div>

<?php $this->endContent(); ?>