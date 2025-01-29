<?php

namespace TimbleOne\BackendBundle;

use Symfony\Component\HttpFoundation\File\File;

interface MediaObject
{
    public function getFile(): ?File;
    public function setFile(?File $file): static;
}