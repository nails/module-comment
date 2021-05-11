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

namespace Nails\Comment\Resource\Comment;

use Nails\Comment\Constants;
use Nails\Comment\Model;
use Nails\Comment\Resource;
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

    /** @var Resource\Comment|null */
    public $comment;

    /** @var string */
    public $vote_type;

    // --------------------------------------------------------------------------

    /**
     * Returns the comment
     *
     * @param array $aData A control array to pass to the Comment model
     *
     * @return Resource\Comment|null
     * @throws FactoryException
     * @throws ModelException
     */
    public function comment(array $aData = []): ?Resource\Comment
    {
        if (empty($this->comment) && !empty($this->comment_id)) {
            /** @var Model\Comment $oModel */
            $oModel = Factory::model('Comment', Constants::MODULE_SLUG);
            /** @var Resource\Comment|null $oItem */
            $oItem = $oModel->getById($this->comment_id, $aData);
            $this->comment = $oItem;
        }

        return $this->comment;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a public-safe version of the object
     *
     * @return \stdClass
     */
    public function getPublic(): \stdClass
    {
        return (object) [
            'id'         => $this->id,
            'comment_id' => $this->comment_id,
            'comment'    => $this->comment ? $this->comment->getPublic() : null,
            'vote_type'  => $this->vote_type,
        ];
    }
}
