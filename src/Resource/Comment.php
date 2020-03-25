<?php

/**
 * This class represents objects dispensed by the Comment model
 *
 * @package     Nails
 * @subpackage  module-comment
 * @category    Resource
 * @author      Nails Dev Team
 * @link        https://docs.nailsapp.co.uk/modules/other/comment
 */

namespace Nails\Comment\Resource;

use Nails\Comment\Constants;
use Nails\Comment\Model;
use Nails\Comment\Resource\Comment\Flag;
use Nails\Comment\Resource\Comment\Vote;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Helper\Model\Expand;
use Nails\Common\Resource\Entity;
use Nails\Common\Resource\ExpandableField;
use Nails\Factory;

/**
 * Class Comment
 *
 * @package App\Resource
 */
class Comment extends Entity
{
    /** @var string */
    public $type;

    /** @var int */
    public $item_id;

    /** @var int */
    public $parent_id;

    /** @var Comment */
    public $parent;

    /** @var string */
    public $body;

    /** @var ExpandableField */
    public $flags;

    /** @var ExpandableField */
    public $votes;

    // --------------------------------------------------------------------------

    /**
     * Returns the comment's parent
     *
     * @param array $aData A control array to pass to the Comment model
     *
     * @return $this|null
     * @throws FactoryException
     * @throws ModelException
     */
    public function parent(array $aData = []): ?self
    {
        if (empty($this->parent) && !empty($this->parent_id)) {
            /** @var Model\Comment $oModel */
            $oModel       = Factory::model('Comment', Constants::MODULE_SLUG);
            $this->parent = $oModel->getById($this->parent_id, $aData);
        }

        return $this->parent;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the comment's flags
     *
     * @param Expand\Group|null $oExpand An Expand group to pass to the CommentFlag model
     *
     * @return ExpandableField
     * @throws FactoryException
     * @throws ModelException
     */
    public function flags(Expand\Group $oExpand = null): ExpandableField
    {
        return $this->getExpandableField('flags');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the comment's votes
     *
     * @param Expand\Group|null $oExpand An Expand group to pass to the CommenVote model
     *
     * @return ExpandableField
     * @throws FactoryException
     * @throws ModelException
     */
    public function votes(Expand\Group $oExpand = null): ExpandableField
    {
        return $this->getExpandableField('votes');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an expanded field for the comment
     *
     * @param string $sTrigger The trigger to expand
     *
     * @return ExpandableField
     * @throws FactoryException
     * @throws ModelException
     */
    protected function getExpandableField(string $sTrigger): ExpandableField
    {
        if (empty($this->{$sTrigger})) {

            /** @var Model\Comment $oModel */
            $oModel = Factory::model('Comment', 'app');
            /** @var Listing $oItem */
            $oItem = $oModel->getById(
                $this->id,
                [
                    new Expand($sTrigger, $oExpand),
                ]
            );

            $this->{$sTrigger} = $oItem->{$sTrigger};
        }

        return $this->{$sTrigger};
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a public safe version of the object
     *
     * @return \stdClass
     */
    public function getPublic(): \stdClass
    {
        return (object) [
            'id'         => $this->id,
            'body'       => $this->body,
            'type'       => $this->type,
            'item_id'    => $this->item_id,
            'parent_id'  => $this->parent_id,
            'parent'     => $this->parent
                ? $this->parent->getPublic()
                : null,
            'flags'      => $this->flags
                ? array_map(
                    function (Flag $oFlag) {
                        return $oFlag->getPublic();
                    },
                    $this->flags->data
                )
                : null,
            'votes'      => $this->votes
                ? array_map(
                    function (Vote $oVote) {
                        return $oVote->getPublic();
                    },
                    $this->votes->data
                )
                : null,
            'created'    => $this->created,
            'created_by' => $this->created_by,
        ];
    }
}
