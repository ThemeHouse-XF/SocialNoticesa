<?php

class ThemeHouse_SocialNotices_Extend_XenForo_DataWriter_Notice extends XFCP_ThemeHouse_SocialNotices_Extend_XenForo_DataWriter_Notice
{

    /**
     * Post-save handling.
     */
    protected function _postSave()
    {
        parent::_postSave();

        $pageCriteria = unserialize($this->get('page_criteria'));
        foreach ($pageCriteria as $pageCriterion) {
            if ($pageCriterion['rule'] == 'social_forums') {
                if (isset($pageCriterion['data']['social_forum_ids'])) {
                    $this->_updateSocialNotices($pageCriterion['data']['social_forum_ids']);
                    return;
                }
            }
        }

        $this->_updateSocialNotices();

        parent::_postSave();
    } /* END _postSave */

    /**
     * Post-delete handling.
     */
    protected function _postDelete()
    {
        $this->_updateSocialNotices();

        parent::_postDelete();
    } /* END _postDelete */

    protected function _updateSocialNotices($socialForumIds = array())
    {
        $noticeId = $this->get('notice_id');

        $this->_db->delete('xf_social_notice', 'notice_id = ' . $noticeId);

        foreach ($socialForumIds as $socialForumId) {
            $this->_db->insert('xf_social_notice',
                array(
                    'notice_id' => $noticeId,
                    'social_forum_id' => $socialForumId
                ));
        }
    } /* END _updateSocialNotices */
}