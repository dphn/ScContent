<?php

namespace ScContent\Entity\Front;

use ScContent\Entity\AbstractContent,
    ScContent\View\Helper\FormatProviderInterface;

class Content extends AbstractContent implements FormatProviderInterface
{
    protected $type = 'category';

    protected $title = 'Nothing was found.';

    protected $description = 'Content named "home" was not published.';

    public function setType($type)
    {
        $this->type = $type;
    }
}
