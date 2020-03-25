<?php

/**
 * Thrown when the app does not extend the comment service
 *
 * @package     Nails
 * @subpackage  module-comment
 * @category    Exception
 * @author      Nails Dev Team
 * @link        https://docs.nailsapp.co.uk/modules/other/comment
 */

namespace Nails\Comment\Exception\Service;

use Nails\Comment\Exception\CommentException;

class AppServiceRequiredException extends CommentException
{
}
