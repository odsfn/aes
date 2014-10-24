<?php

class ImageController extends CController
{
    const GALLERY_PERM_PER_ALL = 0;

    const GALLERY_PERM_PER_REGISTERED = 1;

    const GALLERY_PERM_PER_OWNER = 2;

    public function actionAlbum($op = 'view', $album_id = 0, $profile = 0)
    {
        $menu = array();
        $content = '';
        $user_id = (!Yii::app()->user->isGuest ? Yii::app()->user->id : 0);

        // Если нет профиля используем текущий
        $profile_id = $profile ? $profile : $user_id;

        // Текущий профиль
        if ($user_id == $profile_id)
            $profile = Profile::model()->findByAttributes(array('user_id' => $user_id));
        // Запрашиваемй профиль
        elseif ($profile_id)
            $profile = Profile::model()->findByAttributes(array('user_id' => $profile_id));
        else
            throw new CHttpException(403);

        switch ($op) {
            case 'update':
                $model = Album::model()->findByPk($album_id);
                if (!$model)
                    $this->redirect(array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $model->id, 'profile' => $profile_id));

                if ($attributes = Yii::app()->request->getPost('Album')) {
                    $model->setScenario('update');
                    $model->attributes = $attributes;
                    if ($model->save()) {
                        $photos = File::model()->updateAll(array('permission' => $model->permission), 'album_id = :album_id', array(':album_id' => $model->id));
                        $this->redirect(array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $model->id, 'profile' => $profile_id));
                    }
                }

                $menu = array(
                    array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'profile' => $profile_id)),
                    array('label' => 'Альбом: ' . $model->name, 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $model->id, 'profile' => $profile_id)),
                    array('label' => 'Добавить фото', 'url' => array($this->getModule()->albumRoute , 'op' => 'upload', 'album_id' => $model->id, 'profile' => $profile_id), 'visible' => $user_id == $profile_id),
                    array('label' => 'Редактировать', 'url' => '#', 'active' => true, 'visible' => $user_id == $profile_id),
                        //array('label'=>'Создать альбом', 'url'=>array($this->getModule()->albumRoute . '/op/create')),
                );

                $content = $this->renderPartial('/_album_create', array('model' => $model), true);
                break;
            case 'delete':
                if (!$user_id)
                    throw new CHttpException(403);

                $model = Album::model()->findByPk($album_id);
                if ($model->delete())
                    $this->redirect(array($this->getModule()->albumRoute . '/op/view'));
                break;
            case 'create':
                if (!$user_id)
                    throw new CHttpException(403);

                $model = new Album();
                if ($attributes = Yii::app()->request->getPost('Album')) {
                    $model->setScenario('create');
                    $model->attributes = $attributes;
                    if ($model->save()) {
                        $this->redirect(array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $model->id, 'profile' => $profile_id));
                    }
                }

                $menu = array(
                    array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'profile' => $profile_id)),
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

                        // Доступно только зарегестрированным
                        if ($model->permission == self::GALLERY_PERM_PER_REGISTERED && Yii::app()->user->isGuest)
                            throw new CHttpException(403);

                        // Доступно только мне
                        if ($model->permission == self::GALLERY_PERM_PER_OWNER && Yii::app()->user->id != $profile->user_id)
                            throw new CHttpException(403);

                        $photos = File::model()->getRecords('album_id = :album_id AND user_id = :user_id', array(
                            ':album_id' => $model->id,
                            ':user_id' => $model->user_id,
                                ), $photos_page, $Gallery['photos_per_page'], $Gallery['photos_sort']);

                        $nphotos = File::model()->count('album_id = :album_id AND user_id = :user_id', array(
                            ':user_id' => $model->user_id, ':album_id' => $model->id));
                    } else
                        throw new CHttpException(404);

                    if ($user_id == $profile_id && !$photos)
                        $this->redirect(array($this->getModule()->albumRoute . '/op/upload/', 'album_id' => $model->id, 'profile' => $profile_id));

                    $menu = array(
                        array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'profile' => $profile_id)),
                        array('label' => 'Альбом: ' . $model->name, 'url' => '#', 'active' => true),
                        array('label' => 'Добавить фото', 'url' => array($this->getModule()->albumRoute , 'op' => 'upload', 'album_id' => $model->id, 'profile' => $profile_id), 'visible' => $user_id == $profile_id),
                        array('label' => 'Редактировать', 'url' => array($this->getModule()->albumRoute , 'op' => 'update', 'album_id' => $model->id, 'profile' => $profile_id), 'visible' => $user_id == $profile_id),
                            //array('label'=>'Создать альбом', 'url'=>array($this->getModule()->albumRoute . '/op/create')),
                    );

                    // Ajax
                    if (Yii::app()->getRequest()->isAjaxRequest) {
                        $output = $this->renderPartial('/_album_ajax', array(
                            'model' => $model,
                            'photos' => $photos,
                            'photos_page' => $photos_page,
                            'photos_per_page' => $Gallery['photos_per_page'],
                            'nphotos' => $nphotos,
                            'profile' => $profile,
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
                            'profile' => $profile,
                                ), true);
                    }
                } else {

                    //
                    // Список альбомов пользователя
                    //
          $params = $condition = array();

                    // Профиль пользователя
                    $condition[] = 't.user_id = :profile_id';
                    $params[':profile_id'] = $profile_id;

                    // !Доступно только мне
                    $condition[] = 't.id NOT IN (SELECT id FROM `album` f WHERE f.user_id <> 0 AND f.user_id <> :user_id AND f.permission = :perm2)';
                    $params[':user_id'] = $user_id;
                    $params[':perm2'] = self::GALLERY_PERM_PER_OWNER;

                    if (!$user_id) {
                        // !Доступно только зарегестрированным
                        $condition[] = 't.id NOT IN (SELECT id FROM `album` f WHERE f.permission = :perm1)';
                        $params[':perm1'] = self::GALLERY_PERM_PER_REGISTERED;
                    }

                    // Все альбомы
                    $albums = Album::model()->findAll(array(
                        'condition' => implode(' AND ', $condition),
                        'params' => $params,
                        'limit' => ($albums_page ? $albums_page * $Gallery['albums_per_page'] : $Gallery['albums_per_page']),
                    ));

                    $nalbums = Album::model()->count(array(
                        'condition' => implode(' AND ', $condition),
                        'params' => $params
                    ));


                    //
                    // Список Фотографий пользователя
                    //
          $params = $condition = array();

                    // Профиль пользователя
                    $condition[] = 't.user_id = :profile_id';
                    $params[':profile_id'] = $profile_id;

                    // !Доступно только мне
                    $condition[] = 't.id NOT IN (SELECT id FROM `file` f WHERE f.user_id <> 0 AND f.user_id <> :user_id AND f.permission = :perm2)';
                    $params[':user_id'] = $user_id;
                    $params[':perm2'] = self::GALLERY_PERM_PER_OWNER;

                    if (!$user_id) {
                        // !Доступно только зарегестрированным
                        $condition[] = 't.id NOT IN (SELECT id FROM `file` f WHERE f.permission = :perm1)';
                        $params[':perm1'] = self::GALLERY_PERM_PER_REGISTERED;
                    }

                    // Все фотографии
                    $photos = File::model()->findAll(array(
                        'condition' => implode(' AND ', $condition),
                        'params' => $params,
                        'limit' => ($photos_page ? $photos_page * $Gallery['photos_per_page'] : $Gallery['photos_per_page']),
                        'order' => $Gallery['photos_sort'],
                    ));

                    $nphotos = File::model()->count(array(
                        'condition' => implode(' AND ', $condition),
                        'params' => $params
                    ));

                    if ($user_id == $profile_id && !($photos || $albums))
                        $this->redirect(array($this->getModule()->albumRoute , 'op' => 'upload', 'profile' => $profile_id));

                    $menu = array(
                        array('label' => 'Все фотографии', 'url' => '#', 'active' => true),
                        array('label' => 'Добавить фото', 'url' => array($this->getModule()->albumRoute , 'op' => 'upload', 'profile' => $profile_id), 'visible' => $user_id == $profile_id),
                        array('label' => 'Создать альбом', 'url' => array($this->getModule()->albumRoute , 'op' => 'create', 'profile' => $profile_id), 'visible' => $user_id == $profile_id),
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
                                    'profile' => $profile,
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
                                    'profile' => $profile,
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
                            'profile' => $profile,
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
                        $photo_params['uploader'] = $this->createUrl($this->getModule()->albumRoute . '/op/upload', array('album_id' => $model->id, 'profile' => $profile_id)) . '?DBGSESSID=1';
                        $photo_params['redirect'] = $this->createUrl($this->getModule()->albumRoute . '/op/view', array('album_id' => $model->id, 'profile' => $profile_id));
                        $menu = array(
                            array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'profile' => $profile_id)),
                            array('label' => 'Альбом: ' . $model->name, 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $model->id, 'profile' => $profile_id)),
                            array('label' => 'Добавить фото', 'url' => '#', 'active' => true, 'visible' => $user_id == $profile_id),
                            array('label' => 'Редактировать', 'url' => array($this->getModule()->albumRoute , 'op' => 'update', 'album_id' => $model->id, 'profile' => $profile_id), 'visible' => $user_id == $profile_id),
                                //array('label'=>'Создать альбом', 'url'=>array($this->getModule()->albumRoute . '/op/create')),
                        );
                    } else
                        throw new CHttpException(500);
                } else {
                    $menu = array(
                        array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute . '/op/view/')),
                        array('label' => 'Добавить фото', 'url' => '#', 'active' => true, 'visible' => $user_id == $profile_id),
                        array('label' => 'Создать альбом', 'url' => array($this->getModule()->albumRoute , 'op' => 'create', 'profile' => $profile_id), 'visible' => $user_id == $profile_id),
                    );
                    $photo_params['uploader'] = $this->createUrl($this->getModule()->albumRoute, array('DBGSESSID' => '1', 'op' => 'upload'));
                    $photo_params['redirect'] = $this->createUrl($this->getModule()->albumRoute, array('profile' => $profile_id, 'op'=> 'view'));
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
                        'album_id' => $album_id,
                        'path' => $file_path,
                        'permission' => $permission,
                    );
                    if ($photo->save()) {
                        echo 1;
                        Yii::app()->end();
                    }
                }

                $photo_params['photo'] = $photo;

                $content = $this->renderPartial('/_photos_upload', $photo_params, true);
                break;
        }

        $this->renderPartial('/content', array('content' => $content, 'menu' => $menu, 'profile' => $profile));
    }

    public function actionPhoto($op = 'view', $photo_id = 0, $profile = 0, $page = 0, $album = 0)
    {
        $model = '';
        $menu = array();
        $user_id = (!Yii::app()->user->isGuest ? Yii::app()->user->id : 0);
        $profile_id = $profile;
        $Gallery = Yii::app()->params['Gallery'];

        // Если нет профиля используем текущий
        $profile_id = $profile ? $profile : $user_id;

        // Текущий профиль
        if ($user_id == $profile_id)
            $profile = Profile::model()->findByAttributes(array('user_id' => $user_id));
        // Запрашиваемй профиль
        elseif ($profile_id)
            $profile = Profile::model()->findByAttributes(array('user_id' => $profile_id));
        else
            throw new CHttpException(403);

        $model = File::model()->with('album')->findByPk($photo_id);

        if (!$model)
            throw new CHttpException(404);

        if ($model->album) {
            // Доступно только зарегестрированным
            if ($model->permission == self::GALLERY_PERM_PER_REGISTERED && Yii::app()->user->isGuest)
                throw new CHttpException(403);

            // Доступно только мне
            if ($model->permission == self::GALLERY_PERM_PER_OWNER && Yii::app()->user->id != $profile->user_id)
                throw new CHttpException(403);
        }

        switch ($op) {
            case 'delete':
                if (!$user_id)
                    throw new CHttpException(403);

                if ($model->delete())
                    $this->redirect(array($this->getModule()->albumRoute , 'op' => 'view', 'profile' => $profile_id));
                break;
            case 'album':
                if (!$user_id)
                    throw new CHttpException(403);

                if (isset($model->album)) {
                    $model->album->path = $model->path;
                    $model->album->save();
                }
                $this->redirect(array($this->getModule()->imageRoute, 'op' => 'view', 'photo_id' => $photo_id, 'profile' => $profile_id));
                break;
            case 'update':
                if (!$user_id)
                    throw new CHttpException(403);

                if (!$model)
                    $this->redirect(array($this->getModule()->imageRoute . '/op/view', 'photo_id' => $photo_id, 'profile' => $profile_id));

                $tags = Yii::app()->request->getPost('Tags');
                $album = Yii::app()->request->getPost('Album');
                $attributes = Yii::app()->request->getPost('File');

                $model->album_id = isset($album['id']) ? $album['id'] : 0;
                $model->tags = $tags;
                $model->attributes = $attributes;
                if ($model->album_id)
                    $model->permission = Album::model()->findByPk($model->album_id)->permission;
                if ($model->save())
                    $this->redirect(array($this->getModule()->imageRoute . '/op/view', 'photo_id' => $photo_id, 'profile' => $profile_id));
                break;
            case 'view':
                if (!$model)
                    throw new CHttpException(404);

                $params = $condition = array();
                // Профиль пользователя
                $condition[] = 't.user_id = :profile_id';
                $params[':profile_id'] = $profile_id;

                // !Доступно только мне
                $condition[] = 't.id NOT IN (SELECT id FROM `file` f WHERE f.user_id <> 0 AND f.user_id <> :user_id AND f.permission = :perm2)';
                $params[':user_id'] = $user_id;
                $params[':perm2'] = self::GALLERY_PERM_PER_OWNER;

                if (!$user_id) {
                    // !Доступно только зарегестрированным
                    $condition[] = 't.id NOT IN (SELECT id FROM `file` f WHERE f.permission = :perm1)';
                    $params[':perm1'] = self::GALLERY_PERM_PER_REGISTERED;
                }

                if (isset($model->album) && $album == $model->album->id) {
                    $condition[] = 'album_id = :album_id';
                    $params[':album_id'] = $model->album->id;
                }

                $criteria = new CdbCriteria(array(
                    'condition' => implode(' AND ', $condition),
                    'params' => $params,
                    'order' => $Gallery['photos_sort'],
                ));

                $pages = new CPagination(File::model()->count($criteria));
                $pages->route = $this->getModule()->imageRoute;
                $pages->params = array(
                    'op' => $op,
                    'photo_id' => $photo_id,
                    'profile' => $profile_id,
                    'album' => $album
                );
                $pages->pageSize = 1;
                $pages->applyLimit($criteria);
                
                if ($page)
                    $model = File::model()->find($criteria);

                if (isset($model->album))
                    $menu = array(
                        array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'profile' => $profile_id)),
                        array('label' => 'Альбом: ' . $model->album->name, 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'album_id' => $model->album->id, 'profile' => $profile_id)),
                        array('label' => 'Просмотр', 'url' => '#', 'active' => true),
                    );
                else
                    $menu = array(
                        array('label' => 'Все фотографии', 'url' => array($this->getModule()->albumRoute , 'op' => 'view', 'profile' => $profile_id)),
                        array('label' => 'Просмотр', 'url' => '#', 'active' => true),
                    );

                if ($model->user_id == $user_id)
                    $content = $this->renderPartial('/_photo_form', array('model' => $model, 'pages' => $pages), true);
                else
                    $content = $this->renderPartial('/_photo', array('model' => $model, 'pages' => $pages), true);

                break;
        }

        $this->renderPartial('/content', array('content' => $content, 'menu' => $menu, 'profile' => $profile));
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

    public function actionTest()
    {
        echo 'Test action completed from ImageController';
    }
    
    /**
     * Creates thumbnails for currently uploaded images
     * @param string $file_path Path to an image which has been currently uploaded
     */
    protected function createThumbnails($file_path)
    {
        $this->getModule()->getComponent('image')->createPath('100x100', $file_path);
        $this->getModule()->getComponent('image')->createPath('600x480', $file_path);
    }
}
