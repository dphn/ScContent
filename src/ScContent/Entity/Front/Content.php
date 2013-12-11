<?php

namespace ScContent\Entity\Front;

use ScContent\Entity\AbstractContent,
    ScContent\View\Helper\FormatProviderInterface;

class Content extends AbstractContent implements FormatProviderInterface
{
    protected $type = 'category';

    protected $title = 'Welcome!';

    protected $description = 'Create a home page of your site. To do this, add an article or category and assign it the name "home". Do not forget to change its status to "published".';

    public function setType($type)
    {
        $this->type = $type;
    }
}
