<?php

interface iDownloadable 
{
    public function canBeDownloaded();

    public function getDownloadUrl();
}

