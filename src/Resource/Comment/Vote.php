<?php

/**
 * This class represents objects dispensed by the CommentVote model
 *
 * @package     Nails
 * @subpackage  module-comment
 * @category    Resource
 * @author      Nails Dev Team
 * @link        https://docs.nailsapp.co.uk/modules/other/comment
 */

namespace App\Resource\Comment;

use Nails\Comment\Constants;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Resource\Entity;
use Nails\Factory;

/**
 * Class Vote
 *
 * @package App\Resource\Comment
 */
class Vote extends Entity
{
    /** @var int */
    public $comment_id;

    /** @var Resource\Comment */
    public $comment;

    /** @var string */
    public $vote_type;

    // --------------------------------------------------------------------------

    /**
     * Returns the comment
     *
     * @param array $aData A control array to pass to the Comment model
     *
     * @return $this|null
     * @throws FactoryException
     * @throws ModelException
     */
    public function comment(array $aData = []): ?self
    {
        if (empty($this->comment) && !empty($this->comment_id)) {
            /** @var Model\Comment $oModel */
            $oModel        = Factory::model('Comment', Constants::MODULE_SLUG);
            $this->comment = $oModel->getById($this->comment_id, $aData);
        }

        return $this->comment;
    }
}
