<?php

class ThemeHouse_SocialNotices_Extend_ThemeHouse_SocialGroups_ControllerPublic_SocialForum extends XFCP_ThemeHouse_SocialNotices_Extend_ThemeHouse_SocialGroups_ControllerPublic_SocialForum
{

    protected function _postDispatch($controllerResponse, $controllerName, $action)
    {
        /* @var $controllerResponse XenForo_ControllerResponse_View */
        if (get_class($controllerResponse) == 'XenForo_ControllerResponse_View') {
            $notices = array();
            if (isset($controllerResponse->containerParams['notices'])) {
                $notices = $controllerResponse->containerParams['notices'];
            }

            if (ThemeHouse_SocialGroups_SocialForum::hasInstance()) {
                $socialForum = ThemeHouse_SocialGroups_SocialForum::getInstance()->toArray();
                if (isset($socialForum['social_forum_id'])) {
                    $controllerResponse->containerParams['notices'] = $this->_getSocialNoticesContainerParams(
                        $controllerResponse, $notices);
                }
            }
        }

        parent::_postDispatch($controllerResponse, $controllerName, $action);
    } /* END _postDispatch */

    /**
     * Fetches all notices applicable to the visiting user
     *
     * @param array $params
     * @param array $containerData
     *
     * @return array
     */
    protected function _getSocialNoticesContainerParams(XenForo_ControllerResponse_View $controllerResponse,
        array $notices = array())
    {
        $socialForum = ThemeHouse_SocialGroups_SocialForum::getInstance();

        /* @var $noticeModel XenForo_Model_Notice */
        $noticeModel = XenForo_Model::create('XenForo_Model_Notice');

        if (XenForo_Application::get('options')->enableNotices) {
            $user = XenForo_Visitor::getInstance()->toArray();

            if (XenForo_Application::isRegistered('session')) {
                $dismissedNotices = XenForo_Application::getSession()->get('dismissedNotices');
            }

            if (!isset($dismissedNotices) || !is_array($dismissedNotices)) {
                $dismissedNotices = array();
            }

            // handle style overrides
            $user['style_id'] = XenForo_Application::get('options')->defaultStyleId;

            $noticeTokens = array(
                '{name}' => $user['username'] !== '' ? $user['username'] : new XenForo_Phrase('guest'),
                '{user_id}' => $user['user_id']
            );

            $allNotices = $noticeModel->getNoticesForSocialForum($socialForum['social_forum_id']);
            foreach ($allNotices as $noticeId => $notice) {
                if (!in_array($noticeId, $dismissedNotices) &&
                     XenForo_Helper_Criteria::userMatchesCriteria($notice['user_criteria'], true, $user) && XenForo_Helper_Criteria::pageMatchesCriteria(
                        $notice['page_criteria'], true, $controllerResponse->params,
                        $controllerResponse->containerParams)) {
                    $notices[$noticeId] = array(
                        'title' => $notice['title'],
                        'message' => str_replace(array_keys($noticeTokens), $noticeTokens, $notice['message']),
                        'wrap' => $notice['wrap'],
                        'dismissible' => ($notice['dismissible'] && XenForo_Visitor::getUserId())
                    );
                }
            }
        }

        return $notices;
    } /* END _getSocialNoticesContainerParams */

        /**
     *
     * @see ThemeHouse_SocialGroups_ControllerPublic_SocialForum::actionIndex()
     */
    public function actionIndex()
    {
        $response = parent::actionIndex();

        return $this->_getSocialNoticesResponse($response);
    } /* END actionIndex */

    /**
     *
     * @see ThemeHouse_SocialGroups_ControllerPublic_SocialForum::actionForum()
     */
    public function actionForum()
    {
        $response = parent::actionForum();

        return $this->_getSocialNoticesResponse($response);
    } /* END actionForum */

    /**
     *
     * @param XenForo_ControllerResponse_Abstract $response
     * @return XenForo_ControllerResponse_Abstract
     */
    protected function _getSocialNoticesResponse(XenForo_ControllerResponse_Abstract $response)
    {
        $visitor = XenForo_Visitor::getInstance();

        if ($this->_routeMatch->getResponseType() == 'rss') {
            return $response;
        }

        /* @var $response XenForo_ControllerResponse_View */
        if (get_class($response) == "XenForo_ControllerResponse_View" && ThemeHouse_SocialGroups_SocialForum::hasInstance()) {
            $socialForum = ThemeHouse_SocialGroups_SocialForum::getInstance()->toArray();

            $viewParams = array(
                'canManageSocialNotices' => $this->_getForumModel()->canManageSocialNotices($socialForum,
                    $errorPhraseKey)
            );
            if ($response->subView) {
                $response->subView->params = array_merge($response->subView->params, $viewParams);
            }
            $response->params = array_merge($response->params, $viewParams);
        }

        return $response;
    } /* END _getSocialNoticesResponse */ /* END actionIndex */

    public function actionNotices()
    {
        if (ThemeHouse_SocialGroups_SocialForum::hasInstance()) {
            $socialForum = ThemeHouse_SocialGroups_SocialForum::getInstance()->toArray();
        } else {
            return $this->responseError('th_requested_social_forum_not_found_socialgroups');
        }

        $this->_assertCanManageSocialNotices($socialForum);

        $notices = $this->_getNoticeModel()->getNoticesForSocialForum($socialForum['social_forum_id']);

        $optionModel = $this->getModelFromCache('XenForo_Model_Option');

        $viewParams = array(
            'socialForum' => $socialForum,

            'notices' => $notices,
            'options' => $optionModel->prepareOptions(
                $optionModel->getOptionsByIds(array(
                    'enableNotices'
                ))),
            'canEditOptionDefinition' => $optionModel->canEditOptionAndGroupDefinitions()
        );

        $subView = $this->responseView('ThemeHouse_SocialNotices_ViewPublic_SocialForum_Notice_List',
            'th_social_notice_list_socialnotices', $viewParams);
        return $this->_getWrapper($subView);
    } /* END actionNotices */

    protected function _getNoticeAddEditResponse(array $notice)
    {
        if (ThemeHouse_SocialGroups_SocialForum::hasInstance()) {
            $socialForum = ThemeHouse_SocialGroups_SocialForum::getInstance()->toArray();
        } else {
            return $this->responseError('th_requested_social_forum_not_found_socialgroups');
        }

        $this->_assertCanManageSocialNotices($socialForum);

        $noticeModel = $this->_getNoticeModel();

        $ftpHelper = $this->getHelper('ForumThreadPost');
        $forum = $this->getHelper('ForumThreadPost')->assertForumValidAndViewable($socialForum['node_id'], array());

        $viewParams = array(
            'socialForum' => $socialForum,

            'notice' => $notice,

            'userCriteria' => XenForo_Helper_Criteria::prepareCriteriaForSelection($notice['user_criteria']),
            'userCriteriaData' => XenForo_Helper_Criteria::getDataForUserCriteriaSelection(),

            'pageCriteria' => XenForo_Helper_Criteria::prepareCriteriaForSelection($notice['page_criteria']),
            'pageCriteriaData' => XenForo_Helper_Criteria::getDataForPageCriteriaSelection(),

            'showInactiveCriteria' => true,

            'nodeBreadCrumbs' => $ftpHelper->getNodeBreadCrumbs($forum, true)
        );

        return $this->responseView('ThemeHouse_SocialNotices_ViewPublic_SocialForum_Notice_Edit',
            'th_social_notice_edit_socialnotices', $viewParams);
    } /* END _getNoticeAddEditResponse */

    public function actionNoticesAdd()
    {
        return $this->_getNoticeAddEditResponse($this->_getNoticeModel()
            ->getDefaultNotice());
    } /* END actionNoticesAdd */

    public function actionNoticesEdit()
    {
        $noticeId = $this->_input->filterSingle('notice_id', XenForo_Input::UINT);
        $notice = $this->_getNoticeOrError($noticeId);

        return $this->_getNoticeAddEditResponse($notice);
    } /* END actionNoticesEdit */

    public function actionNoticesSave()
    {
        $this->_assertPostOnly();

        if (ThemeHouse_SocialGroups_SocialForum::hasInstance()) {
            $socialForum = ThemeHouse_SocialGroups_SocialForum::getInstance()->toArray();
        } else {
            return $this->responseError('th_requested_social_forum_not_found_socialgroups');
        }

        $this->_assertCanManageSocialNotices($socialForum);

        $noticeId = $this->_input->filterSingle('notice_id', XenForo_Input::UINT);

        $data = $this->_input->filter(
            array(
                'title' => XenForo_Input::STRING,
                'message' => XenForo_Input::STRING,
                'dismissible' => XenForo_Input::UINT,
                'active' => XenForo_Input::UINT,
                'wrap' => XenForo_Input::UINT
            ));

        $dw = XenForo_DataWriter::create('XenForo_DataWriter_Notice');
        if ($noticeId) {
            $dw->setExistingData($noticeId);
        } else {
            $data['page_criteria'] = array(
                'social_forums' => array(
                    'rule' => 'social_forums',
                    'data' => array(
                        'social_forum_ids' => array(
                            $socialForum['social_forum_id']
                        )
                    )
                )
            );
            $data['user_criteria'] = array();
        }
        $dw->bulkSet($data);
        $dw->save();

        $noticeId = $dw->get('notice_id');

        return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS,
            XenForo_Link::buildPublicLink('social-forums/notices', $socialForum));
    } /* END actionNoticesSave */

    public function actionNoticesDelete()
    {
        if (ThemeHouse_SocialGroups_SocialForum::hasInstance()) {
            $socialForum = ThemeHouse_SocialGroups_SocialForum::getInstance()->toArray();
        } else {
            return $this->responseError('th_requested_social_forum_not_found_socialgroups');
        }

        $this->_assertCanManageSocialNotices($socialForum);

        $noticeId = $this->_input->filterSingle('notice_id', XenForo_Input::UINT);

        if ($this->isConfirmedPost()) {
            return $this->_deleteData('XenForo_DataWriter_Notice', 'notice_id',
                XenForo_Link::buildPublicLink('social-forums/notices', $socialForum));
        } else {
            $viewParams = array(
                'notice' => $this->_getNoticeOrError($noticeId),
                'socialForum' => $socialForum
            );

            return $this->_getWrapper(
                $this->responseView('ThemeHouse_SocialNotices_ViewAdmin_SocialForum_Notice_Delete',
                    'th_social_notice_delete_socialnotices', $viewParams));
        }
    } /* END actionNoticesDelete */

    /**
     * Asserts that the currently browsing user can manage social notices.
     */
    protected function _assertCanManageSocialNotices(array $socialForum)
    {
        if (!$this->_getForumModel()->canManageSocialNotices($socialForum, $errorPhraseKey)) {
            throw $this->getNoPermissionResponseException();
        }
    } /* END _assertCanManageSocialNotices */

    /**
     * Gets a valid notice or throws an exception.
     *
     * @param integer $noticeId
     *
     * @return array
     */
    protected function _getNoticeOrError($noticeId)
    {
        $noticeModel = $this->_getNoticeModel();

        $notice = $noticeModel->getNoticeById($noticeId);
        if (!$notice) {
            throw $this->responseException($this->responseError(new XenForo_Phrase('requested_notice_not_found'), 404));
        }

        return $noticeModel->prepareNotice($notice);
    } /* END _getNoticeOrError */

    /**
     *
     * @return XenForo_Model_Notice
     */
    protected function _getNoticeModel()
    {
        return $this->getModelFromCache('XenForo_Model_Notice');
    } /* END _getNoticeModel */
}