<?php

/**
 * This class manages content in the "nails_comment" table.
 *
 * @package     Nails
 * @subpackage  module-comment
 * @category    Model
 * @author      Nails Dev Team
 * @link        https://docs.nailsapp.co.uk/modules/other/comment
 */

namespace Nails\Comment\Model;

use Nails\Comment\Constants;
use Nails\Common\Exception\ModelException;
use Nails\Common\Model\Base;

/**
 * Class Comment
 *
 * @package Nails\Comment\Model
 */
class Comment extends Base
{
    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = NAILS_DB_PREFIX . 'comment';

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'Comment';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = Constants::MODULE_SLUG;

    /**
     * The default column to sort on
     *
     * @var string|null
     */
    const DEFAULT_SORT_COLUMN = 'created';

    // --------------------------------------------------------------------------

    /**
     * Comment constructor.
     *
     * @throws ModelException
     */
    public function __construct()
    {
        parent::__construct();
        $this
            ->hasMany('flags', 'CommentFlag', 'comment_id', Constants::MODULE_SLUG)
            ->hasMany('votes', 'CommentVote', 'comment_id', Constants::MODULE_SLUG);
    }
}
