<?php

class ImageController extends CController
{
    const GALLERY_PERM_PER_ALL = 0;

    const GALLERY_PERM_PER_REGISTERED = 1;

    const GALLERY_PERM_PER_OWNER = 2;

    public function init()
    {
        parent::init();
        
        $request = Yii::app()->request;
        
        if ($request->getParam('op') == 'upload'
            && isset($_POST['SESSION_ID'])) {
            $session=Yii::app()->getSession();
            $actualSession = $_POST['SESSION_ID'];
            
            if ($session->sessionID != $actualSession) {
                $session->close();
                $session->sessionID = $_POST['SESSION_ID'];
                $session->open();
            }
        }
    }

    public function actionAlbum($op = 'view', $album_id = 0, $target_id = 0)
    {
        $menu = array();
        $content = '';
        $user_id = (!Yii::app()->user->isGuest ? Yii::app()->user->id : 0);

        switch ($op) {
            case 'update':
                $model = Album::model()->findByPk($album_id);
                
                if (!$model)
                    $this->redirect(array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $model->id, 'target_id' => $target_id));

                // add access check handler method defined as attribute of AlbumModule
                if (!$user_id || !$this->getModule()->canEditAlbum($model))
                    throw new CHttpException(403);
                
                if ($attributes = Yii::app()->request->getPost('Album')) {
                    $model->setScenario('update');
                    $model->attributes = $attributes;
                    if ($model->save()) {
                        $photos = File::model()->updateAll(array('permission' => $model->permission), 'album_id = :album_id', array(':album_id' => $model->id));
                        $this->redirect(array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $model->id, 'target_id' => $target_id));
                    }
                }

                $menu = array(
                    array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'target_id' => $target_id)),
                    array('label' => 'Альбом: ' . $model->name, 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $model->id, 'target_id' => $target_id)),
                    array('label' => 'Добавить фото', 
                        'url' => array(
                            $this->getModule()->albumRoute , 'op' => 'upload', 'album_id' => $model->id, 'target_id' => $target_id
                        ), 
                        'visible' => $this->getModule()->canAddPhotoToAlbum($model)
                    ),
                    array('label' => 'Редактировать', 'url' => '#', 'active' => true),
                );

                $content = $this->renderPartial('/_album_create', array('model' => $model), true);
                break;
            case 'delete':
                $model = Album::model()->findByPk($album_id);
                
                if (!$user_id || !$this->getModule()->canDeleteAlbum($model))
                    throw new CHttpException(403);
                
                if ($model->delete())
                    $this->redirect(array($this->getModule()->albumRoute . '/op/view'));
                break;
            case 'create':
                if (!$user_id || !$this->getModule()->canCreateAlbum($target_id, $user_id))
                    throw new CHttpException(403);

                $model = new Album();
                if ($attributes = Yii::app()->request->getPost('Album')) {
                    $model->setScenario('create');
                    $model->attributes = $attributes;
                    $model->target_id = $target_id;
                    if ($model->save()) {
                        $this->redirect(array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $model->id, 'target_id' => $target_id));
                    }
                }

                $menu = array(
                    array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'target_id' => $target_id)),
                    array('label' => 'Новый альбом', 'url' => '#', 'active' => true),
                );

                $content = $this->renderPartial('/_album_create', array('model' => $model), true);
                break;
            case 'view':

                $albums = $photos = array();
                $nalbums = $nphotos = 0;
                $photos_page = Yii::app()->getRequest()->getParam('photos_page', 1);
                $albums_page = Yii::app()->getRequest()->getParam('albums_page', 1);

                $Gallery = Yii::app()->params['Gallery'];

                if ($album_id) {

                    $model = Album::model()->findByPk($album_id);

                    // Вывод фото из альбома
                    if ($model) {

                        if (!$this->getModule()->canViewAlbum($model)) 
                            throw new CHttpException(403);

                        $photos = File::model()->getRecords(
                            'album_id = :album_id', 
                            array(
                                ':album_id' => $model->id
                            ), 
                            $photos_page, 
                            $Gallery['photos_per_page'], 
                            $Gallery['photos_sort']
                        );

                        $nphotos = File::model()->count('album_id = :album_id', array(':album_id' => $model->id));
                    } else
                        throw new CHttpException(404);

                    if (!$photos && $this->getModule()->isOwner($user_id, $target_id))
                        $this->redirect(array($this->getModule()->albumRoute . '/op/upload/', 'album_id' => $model->id, 'target_id' => $target_id));

                    $menu = array(
                        array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'target_id' => $target_id)),
                        array('label' => 'Альбом: ' . $model->name, 'url' => '#', 'active' => true),
                        array('label' => 'Добавить фото', 'url' => array($this->getModule()->albumRoute , 'op' => 'upload', 'album_id' => $model->id, 'target_id' => $target_id), 'visible' => $this->getModule()->isOwner($user_id, $target_id)),
                        array('label' => 'Редактировать', 'url' => array($this->getModule()->albumRoute , 'op' => 'update', 'album_id' => $model->id, 'target_id' => $target_id), 'visible' => $this->getModule()->isOwner($user_id, $target_id)),
                    );

                    // Ajax
                    if (Yii::app()->getRequest()->isAjaxRequest) {
                        $output = $this->renderPartial('/_album_ajax', array(
                            'model' => $model,
                            'photos' => $photos,
                            'photos_page' => $photos_page,
                            'photos_per_page' => $Gallery['photos_per_page'],
                            'nphotos' => $nphotos,
                            'target_id' => $target_id,
                                ), true);

                        Yii::app()->clientScript->renderBodyEnd($output);
                        echo $output;
                        Yii::app()->end();
                    } else {
                        $content = $this->renderPartial('/_album', array(
                            'model' => $model,
                            'nphotos' => $nphotos,
                            'photos' => $photos,
                            'photos_page' => $photos_page,
                            'photos_per_page' => $Gallery['photos_per_page'],
                            'target_id' => $target_id,
                                ), true);
                    }
                } else {

                    //
                    // Список альбомов цели
                    //
                    $albumsCriteria = Album::getAvailableAlbumsCriteria($target_id, $user_id);
                    $albumsCountCriteria = clone $albumsCriteria;
                    
                    $albumsCriteria->limit = ($albums_page ? $albums_page * $Gallery['albums_per_page'] : $Gallery['albums_per_page']);
                    $albumsCriteria->order = $Gallery['albums_sort'];
                    
                    $albums = Album::model()->findAll($albumsCriteria);
                    $nalbums = Album::model()->count($albumsCountCriteria);
                    
                    //
                    // Список Фотографий
                    //
                    
                    $withoutAlbum = false;
                    if ($_GET['without_album'])
                        $withoutAlbum = true;
                    
                    $photosCriteria = File::getAvailablePhotosCriteria($withoutAlbum, $target_id, $user_id);
                    $photosCountCriteria = clone $photosCriteria;
                    
                    $photosCriteria->limit = ($photos_page ? $photos_page * $Gallery['photos_per_page'] : $Gallery['photos_per_page']);
                    $photosCriteria->order = $Gallery['photos_sort'];
                    
                    // Все фотографии
                    $photos = File::model()->findAll($photosCriteria);
                    $nphotos = File::model()->count($photosCountCriteria);

                    if (!($photos || $albums) && $this->getModule()->isOwner($user_id, $target_id))
                        $this->redirect(array($this->getModule()->albumRoute , 'op' => 'upload', 'target_id' => $target_id));

                    $menu = array(
                        array('label' => 'Все фотографии', 'url' => '#', 'active' => true),
                        array('label' => 'Добавить фото', 'url' => array($this->getModule()->albumRoute , 'op' => 'upload', 'target_id' => $target_id), 'visible' => $this->getModule()->isOwner($user_id, $target_id)),
                        array('label' => 'Создать альбом', 'url' => array($this->getModule()->albumRoute , 'op' => 'create', 'target_id' => $target_id), 'visible' => $this->getModule()->isOwner($user_id, $target_id)),
                    );

                    // Ajax
                    if (Yii::app()->getRequest()->isAjaxRequest) {
                        switch (Yii::app()->request->getQuery('view')) {
                            case 'albums':
                                $output = $this->renderPartial('/_default_albums_ajax', array(
                                    'albums' => $albums,
                                    'nalbums' => $nalbums,
                                    'albums_page' => $albums_page,
                                    'albums_per_page' => $Gallery['albums_per_page'],
                                    'target_id' => $target_id,
                                        ), true);

                                Yii::app()->clientScript->renderBodyEnd($output);
                                echo $output;
                                break;
                            case 'photos':
                                $output = $this->renderPartial('/_default_photos_ajax', array(
                                    'nphotos' => $nphotos,
                                    'photos' => $photos,
                                    'photos_page' => $photos_page,
                                    'photos_per_page' => $Gallery['photos_per_page'],
                                    'target_id' => $target_id,
                                    'without_album'=>$withoutAlbum
                                        ), true);

                                Yii::app()->clientScript->renderBodyEnd($output);
                                echo $output;
                                break;
                        }
                        Yii::app()->end();
                    } else
                        $content = $this->renderPartial('/_default', array(
                            // Album
                            'albums' => $albums,
                            'nalbums' => $nalbums,
                            'albums_page' => $albums_page,
                            'albums_per_page' => $Gallery['albums_per_page'],
                            // Photo
                            'nphotos' => $nphotos,
                            'photos' => $photos,
                            'photos_page' => $photos_page,
                            'photos_per_page' => $Gallery['photos_per_page'],
                            'target_id' => $target_id,
                            'without_album'=>$withoutAlbum
                                ), true);
                }

                break;
            case 'upload':
                if (!$user_id)
                    throw new CHttpException(403);

                $album_params = $photo_params = array();
                if ($album_id) {
                    $model = Album::model()->findByPk($album_id);
                    if ($model) {
                        
                        if(!$this->getModule()->canAddPhotoToAlbum($model, $user_id))
                            throw new CHttpException(403);

                        $photo_params['uploader'] = $this->createUrl($this->getModule()->albumRoute . '/op/upload', array('album_id' => $model->id, 'target_id' => $target_id)) . '?DBGSESSID=1';
                        $photo_params['redirect'] = $this->createUrl($this->getModule()->albumRoute . '/op/view', array('album_id' => $model->id, 'target_id' => $target_id));
                        $menu = array(
                            array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'target_id' => $target_id)),
                            array('label' => 'Альбом: ' . $model->name, 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $model->id, 'target_id' => $target_id)),
                            array('label' => 'Добавить фото', 'url' => '#', 'active' => true, 'visible' => $this->getModule()->isOwner($user_id, $target_id)),
                            array('label' => 'Редактировать', 'url' => array($this->getModule()->albumRoute , 'op' => 'update', 'album_id' => $model->id, 'target_id' => $target_id), 'visible' => $this->getModule()->isOwner($user_id, $target_id)),
                                //array('label'=>'Создать альбом', 'url'=>array($this->getModule()->albumRoute . '/op/create')),
                        );
                    } else
                        throw new CHttpException(500);
                } else {
                    if(!$this->getModule()->isOwner($user_id, $target_id))
                        throw new CHttpException(403);
                    
                    $menu = array(
                        array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute . '/op/view/')),
                        array('label' => 'Добавить фото', 'url' => '#', 'active' => true, 'visible' => $this->getModule()->isOwner($user_id, $target_id)),
                        array('label' => 'Создать альбом', 'url' => array($this->getModule()->albumRoute , 'op' => 'create', 'target_id' => $target_id), $this->getModule()->isOwner($user_id, $target_id)),
                    );
                    $photo_params['uploader'] = $this->createUrl($this->getModule()->albumRoute, array('DBGSESSID' => '1', 'op' => 'upload'));
                    $photo_params['redirect'] = $this->createUrl($this->getModule()->albumRoute, array('target_id' => $target_id, 'op'=> 'view'));
                }

                $photo = new File();
                if ($file_name = Yii::app()->request->getPost('Filename')) {
                    $photo->setScenario('upload');
                    $file_temp_path = CUploadedFile::getInstance($photo, 'filename')->tempName;
                    $file_path = $this->getModule()->getComponent('image')->createImage($file_temp_path, $file_name);
                    
                    $this->createThumbnails($file_path);
                    
                    if (!$file_path)
                        throw new CHttpException(500);
                    if (isset($model))
                        $permission = Album::model()->findByPk($model->id)->permission;
                    else
                        $permission = 0;

                    $file_path = str_replace(Yii::getPathOfAlias('webroot') . DIRECTORY_SEPARATOR, '', $file_path);

                    $photo->attributes = array(
                        'filename' => basename($file_path),
                        'target_id' => $target_id,
                        'album_id' => empty($album_id) ? null : $album_id,
                        'path' => $file_path,
                        'permission' => $permission,
                    );
                    if ($photo->save()) {                        
                        echo CJSON::encode(array(
                            'success' => true,
                            'html' => $this->renderPartial(
                                '/_photo_update_compact', 
                                array('model'=>$photo),
                                true
                            )
                        ));
                        Yii::app()->end();
                    }
                }

                $photo_params['photo'] = $photo;

                $content = $this->renderPartial('/_photos_upload', $photo_params, true);
                break;
        }

        $this->renderPartial('/content', array('content' => $content, 'menu' => $menu, 'target_id' => $target_id));
    }

    public function actionPhoto($op = 'view', $photo_id = 0, $target_id = 0, $page = 1, $album = 0)
    {
        $model = '';
        $menu = array();
        $user_id = (!Yii::app()->user->isGuest ? Yii::app()->user->id : 0);
        $target_id = $target_id;
        $Gallery = Yii::app()->params['Gallery'];

        $model = File::model()->with('album')->findByPk($photo_id);

        if($op != 'view') {
            if (!$model)
                throw new CHttpException(404);

            if (!$this->getModule()->canViewAlbum($model->album)) {
                throw new CHttpException(403);
            }
        }
        
        switch ($op) {
            case 'delete':
                if (!$user_id || !$this->getModule()->isOwner($user_id, $target_id))
                    throw new CHttpException(403);

                $afterDeleteHandler = function($event) {
                    $originalPath = $event->sender->path;
                    $filesPathes = Yii::app()->getModule('album')->getAbsolutePathes($originalPath);
                    
                    foreach($filesPathes as $path) {
                        if(file_exists($path)) unlink($path);
                    }
                };
                $model->attachEventHandler('onAfterDelete', $afterDeleteHandler);
                
                if ($model->delete())
                    $this->redirect(array($this->getModule()->albumRoute , 'op' => 'view', 'target_id' => $target_id));
                break;
            case 'album':
                if (!$user_id)
                    throw new CHttpException(403);

                if (isset($model->album)) {
                    $model->album->cover_id = $model->id;
                    $model->album->save();
                }
                
                if (Yii::app()->getRequest()->isAjaxRequest) {
                    $this->renderPartial('/_photo_albumCoverMark');
                    Yii::app()->end();
                } else {
                    $this->redirect(array($this->getModule()->imageRoute, 'op' => 'view', 'photo_id' => $photo_id, 'target_id' => $target_id));
                }
                
                break;
            case 'view':
                
                $withoutAlbum = Yii::app()->request->getParam('without_album', false);
                
                $criteria = File::getAvailablePhotosCriteria($withoutAlbum, $target_id, $user_id, $album);
                $criteria->order = $Gallery['photos_sort'];

                // Specified photo, so we have to search it in the navigation set
                if ($photo_id && !empty($_GET['exact'])) {
                    $positionCriteria = clone $criteria;
                    $positionCriteria->select = '*, COUNT(id) as page';
                    
                    $compareOp = '<';
                    if (preg_match('/DESC/i' ,$positionCriteria->order))
                        $compareOp = '>';
                    
                    $positionCriteria->addCondition('id '.$compareOp.'= ' . (int)$photo_id);
                    
                    $tableSchema = File::model()->getTableSchema();
                    $command = File::model()->getCommandBuilder()->createFindCommand($tableSchema, $positionCriteria);
                    
                    $result = $command->queryRow();
                    $page = $result['page'];
                    
                    $_GET['page'] = $page;
                }
                
                $pages = new CPagination(File::model()->count($criteria));
                $pages->route = $this->getModule()->imageRoute;
                $pagerParams = array(
                    'op' => $op,
                    'target_id' => $target_id,
                    'album' => $album
                );
                
                if ($withoutAlbum)
                    $pagerParams['without_album'] = $withoutAlbum;
                
                $pages->params = $pagerParams;
                $pages->pageSize = 1;
                $pages->applyLimit($criteria);
                
                $model = File::model()->find($criteria);

                if (!empty($album) && isset($model->album))
                    $menu = array(
                        array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'target_id' => $target_id)),
                        array('label' => 'Альбом: ' . $model->album->name, 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $album, 'target_id' => $target_id)),
                        array('label' => 'Просмотр', 'url' => '#', 'active' => true),
                    );
                else {
                    $menu = array(
                        array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'target_id' => $target_id)),
                    );
                    
                    if ($withoutAlbum)
                        $menu[] = array('label' => 'Без альбома', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'target_id' => $target_id, 'without_album' => true));
                    
                    $menu[] = array('label' => 'Просмотр', 'url' => '#', 'active' => true);
                }
                
                $canEdit = false;
                if ($model->user_id == $user_id)
                    $canEdit = true;
                    
                $content = $this->renderPartial(
                    '/_photo', 
                    array(
                        'model' => $model, 'pages' => $pages, 'canEdit' => $canEdit,
                        'albumContext' => $album, 'without_album' => $withoutAlbum
                    ), 
                    true
                );

                break;
        }

        $this->renderPartial('/content', array('content' => $content, 'menu' => $menu, 'target_id' => $target_id));
    }

    public function actionAjaxUpdatePhoto($photo_id, $albumContext = false)
    {
        $model = File::model()->findByPk($photo_id);
        $user_id = Yii::app()->user->id;
        
        if (!$model)
            throw new CHttpException(404);
        
        $target_id = $model->target_id;
        
        if (!$user_id || !$this->getModule()->isOwner($user_id, $target_id))
            throw new CHttpException(403);

        $attributes = Yii::app()->request->getPost('File');

        if (isset($attributes['album_id']) 
            && $attributes['album_id'] == 'NULL' || $attributes['album_id'] == '0' || $attributes['album_id'] == ''
        ){
            $attributes['album_id'] = null;
        }
        
        $model->attributes = $attributes;
        
        if($model->isNewRecord)
            $updated = false;
        else
            $updated = true;
        
        if ($model->save()) {
            
            $canEdit = false;
            if ($model->user_id == $user_id)
                $canEdit = true;
            
            $formView = '/_photo_details_panel';
            
            if (Yii::app()->request->getParam('form_type') == 'compact')
                $formView = '/_photo_update_compact';
            
            if ($updated)
                $updated = Yii::app()->locale->dateFormatter->formatDateTime(time(), null, 'short');
                    
            $response = array(
                'success' => true,
                'html' => $this->renderPartial($formView, array(
                    'model' => $model,
                    'canEdit' => $canEdit,
                    'albumContext' => $albumContext,
                    'updated' => $updated
                ), true)
            );
        } else {
            
            $formView = '/_photo_update';
            
            if (Yii::app()->request->getParam('form_type') == 'compact')
                $formView = '/_photo_update_compact';
            
            $response = array(
                'success' => false,
                'html' => $this->renderPartial($formView, array('model' => $model), true)
            );
        }
        
        echo CJSON::encode($response);
        Yii::app()->end();
    }
    
    public function actionPhotoDelete($photo_id)
    {
        $model = File::model()->findByPk($photo_id);

        if (!$model)
            throw new CHttpException(404);
        
        $target_id = $model->target_id;
        $user_id = Yii::app()->user->id;
        
        if (!$user_id || !$this->getModule()->isOwner($user_id, $target_id))
            throw new CHttpException(403);

        $afterDeleteHandler = function($event) {
            $originalPath = $event->sender->path;
            $filesPathes = Yii::app()->getModule('album')->getAbsolutePathes($originalPath);

            foreach($filesPathes as $path) {
                if(file_exists($path)) unlink($path);
            }
        };
        $model->attachEventHandler('onAfterDelete', $afterDeleteHandler);

        if (!$model->delete()) 
            throw new CException('Photo #'.$photo_id.' deletion failed');

        if (Yii::app()->request->isAjaxRequest) {
            
            echo CJSON::encode(array(
                'success'=>true,
                'html'=>Yii::t('album', 'Фото было удалено')
            ));
            
            return;
        }
        
        $this->redirect(array($this->getModule()->albumRoute , 'op' => 'view', 'target_id' => $target_id));
    }

    public function actionRotateImage($photo_id, $direction)
    {
        $model = File::model()->findByPk($photo_id);

        if (!$model)
            throw new CHttpException(404);
        
        $target_id = $model->target_id;
        $user_id = Yii::app()->user->id;
        
        if (!$user_id || !$this->getModule()->isOwner($user_id, $target_id))
            throw new CHttpException(403);
        
        $this->getModule()->getComponent('image')->rotate($file_path = $model->getAbsolutePath(), $direction);
        $this->createThumbnails($file_path, true);
        
        echo CJSON::encode(array('success'=>true));
    }
    
    public function actionTagsJson($tag = '')
    {
        if (Yii::app()->getUser()->getIsGuest())
            $this->render('/site/index');
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 2097 05:00:00 GMT');
        header('Content-type: application/json');

        $this->layout = false;
        if (isset($_GET['tag'])) {

            $criteria = new CDbCriteria(array(
                'limit' => 10
            ));

            $criteria->addSearchCondition('name', $_GET['tag']);

            $tags = Tags::model()->findAll($criteria);

            $this->renderPartial('/_tag_json', array('tags' => $tags));
        }
    }
    
    /**
     * Creates thumbnails for currently uploaded images
     * @param string $file_path Path to an image which has been currently uploaded
     */
    protected function createThumbnails($file_path, $overwrite = false)
    {
        $this->getModule()->getComponent('image')->createPath('160x100', $file_path, false, $overwrite);
        $this->getModule()->getComponent('image')->createPath('1150x710', $file_path, false, $overwrite);
    }
    
    protected function createAlbumThumbnail($file_path)
    {
        $this->getModule()->getComponent('image')->createPath('360x220', $file_path);
    }
}
