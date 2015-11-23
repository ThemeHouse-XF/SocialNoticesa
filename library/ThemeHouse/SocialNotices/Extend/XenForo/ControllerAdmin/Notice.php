<?php

class ThemeHouse_SocialNotices_Extend_XenForo_ControllerAdmin_Notice extends XFCP_ThemeHouse_SocialNotices_Extend_XenForo_ControllerAdmin_Notice
{

    /**
     *
     * @see XenForo_ControllerAdmin_Notice::_getNoticeAddEditResponse()
     */
    protected function _getNoticeAddEditResponse(array $notice)
    {
        /* @var $response XenForo_ControllerResponse_View */
        $response = parent::_getNoticeAddEditResponse($notice);

        /* @var $socialForumModel ThemeHouse_SocialGroups_Model_SocialForum */
        $socialForumModel = ThemeHouse_SocialGroups_SocialForum::getSocialForumModel();
        $response->params['pageCriteriaData']['socialForums'] = $socialForumModel->getSocialForums(array());

        return $response;
    } /* END _getNoticeAddEditResponse */
}