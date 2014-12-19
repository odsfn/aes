<?php

interface iGalleryItem {
    public static function getAvailablePhotosCriteria($withoutAlbums = false, $target_id, $user_id = null, $album = null, $tableName = null);
    public function show();
}
