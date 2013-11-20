<?php
$this->layout = '//layouts/main';
?>

<h1>Hello from sandbox!</h1>

<p>This is sample of posts widget. It shows post from the page of user 1. You should provide
fixture data first manually or by running 

<pre>aes/frontend/tests/phpunit$ phpunit ./functional/UserPageTest.php</pre>

You can find the fixtures that you should import to the database in the tests file.
</p>

<?php    
    $profile = Profile::model()->findByPk(1);

    $this->widget('PostsMarionetteWidget', array(
        'jsConstructorOptions' => array(
            'targetId' => $profile->target_id,
            'targetType' => 'Profile',
            'userPageId' => $profile->user_id
        ),
        'show' => array('el' => '#posts-container1')
    ));
?>

<div class="row-fluid">
    <div class="span6" id="posts-container1"></div>
</div>