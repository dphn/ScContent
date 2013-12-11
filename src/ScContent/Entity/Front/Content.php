<?php

namespace ScContent\Entity\Front;

use ScContent\Entity\AbstractContent,
    ScContent\View\Helper\FormatProviderInterface;

class Content extends AbstractContent implements FormatProviderInterface
{
    public function setType($type)
    {
        $this->type = $type;
    }
}
