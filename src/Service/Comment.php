<?php

/**
 * This class provides an interface for creating and managing comments.
 *
 * @package     Nails
 * @subpackage  module-comment
 * @category    Service
 * @author      Nails Dev Team
 * @link        https://docs.nailsapp.co.uk/modules/other/comment
 */

namespace Nails\Comment\Service;

/**
 * Class Comment
 *
 * @package Nails\Comment\Service
 */
abstract class Comment
{
    /**
     * Returns the supported comment types
     *
     * @return string[]
     */
    abstract public function getTypes(): array;

    // --------------------------------------------------------------------------

    /**
     * Determines if the supplied comment type is valid
     *
     * @param string $sType The comment type
     *
     * @return bool
     */
    public function isValidType(string $sType): bool
    {
        if (empty($sType)) {
            return false;
        }

        return in_array($sType, $this->getTypes());
    }

    // --------------------------------------------------------------------------

    /**
     * Filters the comment body
     *
     * @param string $sBody The string to filter
     *
     * @return string
     */
    public function filterCommentBody(string $sBody): string
    {
        return trim(
            strip_tags($sBody, '<a>')
        );
    }
}
