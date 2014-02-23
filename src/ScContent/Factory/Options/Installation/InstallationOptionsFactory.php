<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Options\Installation;

use ScContent\Options\Installation\Installation,
    ScContent\Options\Installation\Member,
    ScContent\Options\Installation\Step,
    //
    ScContent\Exception\InvalidArgumentException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class InstallationOptionsFactory
{
    /**
     * @param  string $moduleName
     * @param  array $options
     * @throws \ScContent\Exception\InvalidArgumentException
     * @return \ScContent\Options\Installation\Installation
     */
    public static function make($moduleName, $options)
    {
        $installation = new Installation($moduleName, $options);
        if (! isset($options['steps'])
            || ! is_array($options['steps'])
            || empty($options['steps'])
        ) {
            throw new InvalidArgumentException(sprintf(
                "Missing 'steps' options for module '%s'.",
                $moduleName
            ));
        }
        foreach ($options['steps'] as $stepName =>  $stepOptions) {
            if (! isset($stepOptions['chain'])
                || ! is_array($stepOptions['chain'])
                || empty($stepOptions['chain'])
            ) {
                throw new InvalidArgumentException(sprintf(
                    "Missing chain for step '%s' of module '%s'.",
                    $stepName,
                    $moduleName
                ));
            }

            $step = new Step($stepName, $stepOptions);
            foreach ($stepOptions['chain'] as $memberName => $memberOptions) {
                $member = new Member($memberName, $memberOptions);
                $step->addMember($member);
            }

            $installation->addStep($step);
        }
        return $installation;
    }
}
