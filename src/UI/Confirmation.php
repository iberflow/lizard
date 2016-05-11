<?php

namespace Iber\Lizard\UI;

/**
 * Class Confirmation
 *
 * @package  Iber\Lizard\UI
 */
class Confirmation
{
    /**
     * @param $addable
     * @param $removable
     * @return string
     */
    public function getTitle($addable, $removable)
    {
        $title = 'Are you sure you want to perform these updates?'.PHP_EOL;

        if (!empty($addable)) {
            $title .= PHP_EOL . 'Install:';

            foreach ($addable as $package) {
                $title .= PHP_EOL . ' ' . unicode_to_string('\u2714') . '  ' . $package;
            }
        }

        if (!empty($removable)) {
            $title .= PHP_EOL . 'Remove:';

            foreach ($removable as $package) {
                $title .= PHP_EOL . ' ' . unicode_to_string('\u2716') . '  ' . $package;
            }
        }

        return $title;
    }
}
