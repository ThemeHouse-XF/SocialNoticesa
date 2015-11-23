<?php

class ThemeHouse_SocialNotices_Extend_XenForo_Model_Notice extends XFCP_ThemeHouse_SocialNotices_Extend_XenForo_Model_Notice
{

    /**
     *
     * @see XenForo_Model_Notice::rebuildNoticeCache()
     */
    public function rebuildNoticeCache()
    {
        $oldCache = parent::rebuildNoticeCache();

        $cache = array();
        foreach ($oldCache as $noticeId => $notice) {
            foreach ($notice['page_criteria'] as $pageCriterion) {
                if ($pageCriterion['rule'] == 'social_forums') {
                    continue (2);
                }
            }
            $cache[$noticeId] = $notice;
        }

        $this->_getDataRegistryModel()->set('notices', $cache);

        return $cache;
    } /* END rebuildNoticeCache */

    public function getNoticesForSocialForum($socialForumId)
    {
        return $this->fetchAllKeyed(
            '
			SELECT *
            FROM xf_social_notice AS social_notice
			LEFT JOIN xf_notice AS notice ON (social_notice.notice_id = notice.notice_id)
			WHERE social_forum_id = ?
		', 'notice_id', $socialForumId);
    } /* END getNoticesForSocialForum */
}