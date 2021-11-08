<?php

namespace Nails\Comment\Api\Controller;

use Nails\Api;
use Nails\Comment\Constants;
use Nails\Comment\Model;
use Nails\Comment\Resource;
use Nails\Comment\Service;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Exception\ValidationException;
use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Helper\Model\Expand;
use Nails\Common\Resource\Entity;
use Nails\Common\Service\Database;
use Nails\Common\Service\HttpCodes;
use Nails\Common\Service\Input;
use Nails\Common\Service\Uri;
use Nails\Factory;

/**
 * Class Comment
 *
 * @package Nails\Comment\Api\Controller
 */
class Comment extends Api\Controller\Base
{
    /**
     * Require the user be logged in to manage comments, cast votes or flag
     *
     * @var bool
     */
    const REQUIRE_AUTH = true;

    /**
     * The number of comments per page
     *
     * @var int
     */
    const CONFIG_PER_PAGE = 20;

    // --------------------------------------------------------------------------

    /**
     * Handles the lsiting of comments for a particular type and ID
     *
     * @return Api\Factory\ApiResponse
     * @throws Api\Exception\ApiException
     * @throws FactoryException
     * @throws ModelException
     */
    public function getRemap(): Api\Factory\ApiResponse
    {
        /** @var Input $oInput */
        $oInput = Factory::service('Input');

        [$sType, $iId] = $this->getTypeAndId();
        $iPage = (int) $oInput->get('page') ?: 1;
        $iPage = $iPage < 0 ? $iPage * -1 : $iPage;

        /** @var Api\Factory\ApiResponse $oApiResponse */
        $oApiResponse = Factory::factory('ApiResponse', Api\Constants::MODULE_SLUG);
        $oApiResponse
            ->setData($this->getComments($sType, $iId, $iPage))
            ->setMeta($this->getPagination($sType, $iId, $iPage));

        return $oApiResponse;
    }

    // --------------------------------------------------------------------------

    public function postRemap(): Api\Factory\ApiResponse
    {
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');

        [$sType, $iId] = $this->getTypeAndId();
        $iCommentId = $oUri->segment(6);

        if (!empty($iCommentId)) {

            /** @var Model\Comment $oModel */
            $oModel = Factory::model('Comment', Constants::MODULE_SLUG);
            /** @var Resource\Comment|null $oComment */
            $oComment = $oModel->getById($iCommentId);
            if (empty($oComment)) {
                throw new Api\Exception\ApiException(
                    HttpCodes::getByCode(HttpCodes::STATUS_NOT_FOUND),
                    HttpCodes::STATUS_NOT_FOUND
                );
            }

            $sAction = strtoupper($oUri->segment(7));
            switch ($sAction) {
                case 'FLAG':
                    return $this->flagComment($oComment);

                default:
                    return $this->voteComment($oComment, $sAction);
            }
        } else {
            return $this->createComment($sType, $iId);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the type and ID of the comment from the URL
     *
     * @return array
     * @throws Api\Exception\ApiException
     * @throws FactoryException
     */
    protected function getTypeAndId(): array
    {
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var Input $oInput */
        $oInput = Factory::service('Input');

        $sType = $oUri->segment(4);
        $iId   = (int) $oUri->segment(5) ?: null;

        $this->validateType($sType);

        return [$sType, $iId];
    }

    // --------------------------------------------------------------------------

    /**
     * Validates that the type provided is acceptable
     *
     * @param string $sType The comment type
     *
     * @throws Api\Exception\ApiException
     * @throws FactoryException
     */
    protected function validateType(string $sType): void
    {
        /** @var Service\Comment $oCommentService */
        $oCommentService = Factory::service('Comment', Constants::MODULE_SLUG);

        if (!$oCommentService->isValidType($sType)) {
            throw new Api\Exception\ApiException(
                sprintf(
                    '"%s" is not a valid comment type',
                    $sType
                ),
                HttpCodes::STATUS_BAD_REQUEST
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches comments for a particular type and ID
     *
     * @param string $sType The comment type
     * @param int    $iId   The item ID
     *
     * @return array
     * @throws FactoryException
     * @throws ModelException
     */
    protected function getComments(string $sType, int $iId, int $iPage): array
    {
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Model\Comment $oModel */
        $oModel = Factory::model('Comment', Constants::MODULE_SLUG);
        /** @var Resource\Comment[] $aItems */
        $aItems = $oModel->getAll([
            new Expand('flags'),
            new Expand('votes'),
            new Expand('created_by'),
            'where' => [
                ['type', $sType],
                ['item_id', $iId],
            ],
            'limit' => [$iPage, static::CONFIG_PER_PAGE],
            'sort'  => [
                $oModel->getColumn('created'),
                $oInput->get('sort') ?: 'asc',
            ],
        ]);

        return array_map(
            function (Resource\Comment $oComment) {
                return $oComment->getPublic();
            },
            $aItems
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns pagination object for  comments of a particular type and ID
     *
     * @param string $sType The comment type
     * @param int    $iId   The item ID
     *
     * @return array
     * @throws FactoryException
     * @throws ModelException
     */
    protected function getPagination(string $sType, int $iId, int $iPage): array
    {
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Model\Comment $oModel */
        $oModel = Factory::model('Comment', Constants::MODULE_SLUG);

        $iTotal = $oModel->countAll([
            'where' => [
                ['type', $sType],
                ['item_id', $iId],
            ],
        ]);

        return [
            'pagination' => [
                'page'     => $iPage,
                'per_page' => static::CONFIG_PER_PAGE,
                'total'    => $iTotal,
                'previous' => $this->buildUrl($iTotal, $iPage, -1),
                'next'     => $this->buildUrl($iTotal, $iPage, 1),
            ],
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Builds pagination URL
     *
     * @param int $iTotal      The total number of items
     * @param int $iPage       The current page number
     * @param int $iPageOffset The offset to the page number
     *
     * @return null|string
     * @throws FactoryException
     */
    protected function buildUrl($iTotal, $iPage, $iPageOffset)
    {
        /** @var Input $oInput */
        $oInput = Factory::service('Input');

        $aParams = array_merge(
            $oInput->get(),
            [
                'page' => $iPage + $iPageOffset,
            ]
        );

        if ($aParams['page'] <= 0) {
            return null;
        } elseif ($aParams['page'] === 1) {
            unset($aParams['page']);
        }

        $iTotalPages = ceil($iTotal / static::CONFIG_PER_PAGE);
        if (!empty($aParams['page']) && $aParams['page'] > $iTotalPages) {
            return null;
        }

        $sUrl = siteUrl() . uri_string();

        if (!empty($aParams)) {
            $sUrl .= '?' . http_build_query($aParams);
        }

        return $sUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Posts a new comment
     *
     * @param string $sType The comment type
     * @param int    $iId   The item ID
     *
     * @return Api\Factory\ApiResponse
     * @throws Api\Exception\ApiException
     * @throws FactoryException
     * @throws ModelException
     * @throws ValidationException
     */
    protected function createComment(string $sType, int $iId): Api\Factory\ApiResponse
    {
        /** @var Service\Comment $oCommentService */
        $oCommentService = Factory::service('Comment', Constants::MODULE_SLUG);
        /** @var Model\Comment $oModel */
        $oModel = Factory::model('Comment', Constants::MODULE_SLUG);

        $aData = $this->getRequestData();
        $sBody = $oCommentService->filterCommentBody(ArrayHelper::get('body', $aData));

        if (empty($sBody)) {
            throw new ValidationException(
                '"body" is a required field'
            );
        }

        $iExisting = $oModel->countAll([
            'where' => [
                ['type', $sType],
                ['item_id', $iId],
                ['body', $sBody],
                [$oModel->getColumnCreatedBy(), activeUser('id')],
            ],
        ]);

        if ($iExisting) {
            throw new ValidationException(
                'Duplicate comment detected.'
            );
        }

        /** @var Resource\Comment|null $oComment */
        $oComment = $oModel->create(
            [
                'type'    => $sType,
                'item_id' => $iId,
                'body'    => $sBody,
            ],
            true
        );

        if (empty($oComment)) {
            throw new Api\Exception\ApiException(
                trim('Failed to create comment. ' . $oModel->lastError())
            );
        }

        /** @var Api\Factory\ApiResponse $oApiResponse */
        $oApiResponse = Factory::factory('ApiResponse', Api\Constants::MODULE_SLUG);
        $oApiResponse
            ->setCode(HttpCodes::STATUS_CREATED)
            ->setData($oComment->getPublic());

        return $oApiResponse;
    }

    // --------------------------------------------------------------------------

    /**
     * Casts a vote against a comment
     *
     * @param Resource\Comment $oComment The comment being voted
     * @param string           $sVote    The vote
     *
     * @return Api\Factory\ApiResponse
     * @throws FactoryException
     * @throws ValidationException
     */
    protected function voteComment(Resource\Comment $oComment, string $sVote): Api\Factory\ApiResponse
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        /** @var Model\Comment\Vote $oModel */
        $oModel = Factory::model('CommentVote', Constants::MODULE_SLUG);

        if (!array_key_exists($sVote, $oModel->getTypes())) {
            throw new ValidationException(
                'Invalid vote option.',
                HttpCodes::STATUS_BAD_REQUEST
            );
        }

        try {

            $oDb->transaction()->start();

            $aExisting = $oModel->getAll([
                'where' => [
                    ['comment_id', $oComment->id],
                    [$oModel->getColumnCreatedBy(), activeUser('id')],
                ],
            ]);

            if (!empty($aExisting)) {
                $oModel->deleteMany(ArrayHelper::extract($aExisting, 'id'));
            }

            $iVoteId = $oModel->create([
                'comment_id' => $oComment->id,
                'vote_type'  => $sVote,
            ]);

            if (empty($iVoteId)) {
                throw new Api\Exception\ApiException(
                    trim('Failed to cast vote. ' . $oModel->lastError())
                );
            }

            $oDb->transaction()->commit();

        } catch (\Exception $e) {
            $oDb->transaction()->rollback();
            throw $e;
        }

        /** @var Api\Factory\ApiResponse $oApiResponse */
        $oApiResponse = Factory::factory('ApiResponse', Api\Constants::MODULE_SLUG);
        $oApiResponse->setCode(HttpCodes::STATUS_CREATED);
        return $oApiResponse;
    }

    // --------------------------------------------------------------------------

    /**
     * Flags a comment
     *
     * @param Resource\Comment $oComment The comment being flagged
     *
     * @return Api\Factory\ApiResponse
     * @throws Api\Exception\ApiException
     * @throws FactoryException
     * @throws ModelException
     * @throws ValidationException
     */
    protected function flagComment(Resource\Comment $oComment): Api\Factory\ApiResponse
    {
        /** @var Service\Comment $oCommentService */
        $oCommentService = Factory::service('Comment', Constants::MODULE_SLUG);
        /** @var Model\Comment\Flag $oModel */
        $oModel = Factory::model('CommentFlag', Constants::MODULE_SLUG);

        $aData   = $this->getRequestData();
        $sReason = $oCommentService->filterCommentBody(ArrayHelper::get('reason', $aData));

        if (empty($sReason)) {
            throw new ValidationException(
                '"reason" is a required field'
            );
        }

        $iExisting = $oModel->countAll([
            'where' => [
                ['comment_id', $oComment->id],
                [$oModel->getColumnCreatedBy(), activeUser('id')],
            ],
        ]);

        if ($iExisting) {
            throw new ValidationException(
                'You have already flagged this comment.'
            );
        }

        $iFlagId = $oModel->create([
            'comment_id' => $oComment->id,
            'reason'     => $sReason,
        ]);

        if (empty($iFlagId)) {
            throw new Api\Exception\ApiException(
                trim('Failed to flag comment. ' . $oModel->lastError())
            );
        }

        /** @var Api\Factory\ApiResponse $oApiResponse */
        $oApiResponse = Factory::factory('ApiResponse', Api\Constants::MODULE_SLUG);
        $oApiResponse->setCode(HttpCodes::STATUS_CREATED);
        return $oApiResponse;
    }
}
