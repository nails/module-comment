<?php

/**
 * This class manages content in the "nails_comment_vote" table.
 *
 * @package     Nails
 * @subpackage  module-comment
 * @category    Model
 * @author      Nails Dev Team
 * @link        https://docs.nailsapp.co.uk/modules/other/comment
 */

namespace Nails\Comment\Model\Comment;

use Nails\Comment\Constants;
use Nails\Common\Exception\ModelException;
use Nails\Common\Model\Base;

/**
 * Class Vote
 *
 * @package Nails\Comment\Model\Comment
 */
class Vote extends Base
{
    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = NAILS_DB_PREFIX . 'comment_vote';

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'CommentVote';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = Constants::MODULE_SLUG;

    // --------------------------------------------------------------------------

    /**
     * Vote constructor.
     *
     * @throws ModelException
     */
    public function __construct()
    {
        parent::__construct();
        $this
            ->hasOne('comment', 'Comment', Constants::MODULE_SLUG);
    }
}
