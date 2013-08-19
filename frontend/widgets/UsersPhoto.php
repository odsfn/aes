<?php
Yii::import('common.widgets.imageWrapper.ImageWrapper');

/*
 * Виджет для вывода аватарки
 */
class UsersPhoto extends ImageWrapper
{
    /**
     * Модель пользователя
     * @var User
     */
    public $user;
    
    public $imgWidth;
    
    public $imgHeight;
    
    public $containerWidth;
    
    public $containerHeight;

    public function run() {
        
        Yii::app()->clientScript->registerCss('users-photo', 
            ".users-photo {
                background-color: #E9E9E9 !important;
                border: 1px solid #F5F5F5;
                border-radius: 2px;
                box-shadow: 1px 1px 10px 0px rgb(201, 201, 201);
            }

            .users-photo img {
                display: inline-block;
                border-radius: 2px;
            }"
        );
        
        if(!$this->user) {
            $this->user = Yii::app()->user->profile;
        }
        
        $this->imageSrc = $this->user->getPhoto($this->imgWidth, $this->imgHeight); 
        $this->imageAlt = $this->user->username;
        
        $this->width = $this->containerWidth;
        $this->height = $this->containerHeight;
        
        $this->htmlOptions = array('class' => 'users-photo users-photo-' . $this->user->user_id);
        
        parent::run();
    }
}