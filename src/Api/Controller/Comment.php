<?php

namespace Nails\Comment\Api\Controller;

use Nails\Api;
use Nails\Comment\Constants;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Resource\Entity;
use Nails\Common\Service\HttpCodes;
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
    const REQUIRE_AUTH = false;

    // --------------------------------------------------------------------------

    public function getRemap()
    {
        /** @var \Nails\Common\Service\Uri $oUri */
        $oUri = \Nails\Factory::service('Uri');

        $sType = $oUri->segment(4);
        $iId   = (int) $oUri->segment(5) ?: null;

        $this->validateType($sType);

        /** @var Api\Factory\ApiResponse $oApiResponse */
        $oApiResponse = Factory::factory('ApiResponse', Api\Constants::MODULE_SLUG);
        $oApiResponse->setData($this->getComments($sType, $iId));
        return $oApiResponse;
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
        /** @var \Nails\Comment\Service\Comment $oCommentService */
        $oCommentService = Factory::service('Comment', Constants::MODULE_SLUG);

        if (empty($sType)) {
            throw new Api\Exception\ApiException(
                'Comment type is required',
                HttpCodes::STATUS_BAD_REQUEST
            );
        } elseif (!in_array($sType, $oCommentService->getTypes())) {
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
     * Fetches comments for a particular type and id
     *
     * @param string $sType The comment type
     * @param int    $iId   The item ID
     *
     * @return array
     * @throws FactoryException
     * @throws ModelException
     */
    protected function getComments(string $sType, int $iId): array
    {
        /** @var \Nails\Comment\Model\Comment $oModel */
        $oModel = Factory::model('Comment', Constants::MODULE_SLUG);

        return $oModel->getAll([
            'where' => [
                ['type', $sType],
                ['item_id', $iId],
            ],
        ]);
    }
}
