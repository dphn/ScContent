<?php

namespace ScContent\Entity\Front;

use ScContent\Entity\AbstractContent;

class Content extends AbstractContent
{
    public function setType($type)
    {
        $this->type = $type;
    }
}
