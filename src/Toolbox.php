<?php

namespace smtech\AdvisorDashboard;

use smtech\LTI\Configuration\Option;
use Battis\HierarchicalSimpleCache;

/**
 * Advisor Dashboard toolbox
 *
 * Adds some common, useful methods to the St. Mark's-styled
 * ReflexiveCanvasLTI Toolbox
 *
 * @author  Seth Battis <SethBattis@stmarksschool.org>
 * @version v1.2
 */
class Toolbox extends \smtech\StMarksReflexiveCanvasLTI\Toolbox
{

    /**
     * Configure course and account navigation placements
     *
     * @return Generator
     */
    public function getGenerator()
    {
        parent::getGenerator();

        $this->generator->setOptionProperty(
            Option::COURSE_NAVIGATION(),
            'visibility',
            'admins'
        );
        $this->generator->setOptionProperty(
            Option::ACCOUNT_NAVIGATION(),
            'visibility',
            'admins'
        );

        return $this->generator;
    }

    /**
     * Get a listing of all accounts organized for presentation in a select picker
     *
     * @return array
     **/
    public function getAccountList()
    {
        $cache = new HierarchicalSimpleCache($this->getMySQL(), __CLASS__);

        $accounts = $cache->getCache('accounts');
        if ($accounts === false) {
            $accountsResponse = $this->api_get(
                'accounts/1/sub_accounts',
                [
                    'recursive' => 'true'
                ]
            );
            $accounts = [];
            foreach ($accountsResponse as $account) {
                $accounts[$account['id']] = $account;
            }
            $cache->setCache('accounts', $accounts);
        }

        return $accounts;
    }

    /**
     * Get a listing of all terms organized for presentation in a select picker
     *
     * @return array
     **/
    public function getTermList()
    {
        $cache = new HierarchicalSimpleCache($this->getMySQL(), __CLASS__);

        $terms = $cache->getCache('terms');
        if ($terms === false) {
            $_terms = $this->api_get(
                'accounts/1/terms',
                [
                    'workflow_state' => 'active'
                ]
            );
            $termsResponse = $_terms['enrollment_terms'];
            $terms = [];
            foreach ($termsResponse as $term) {
                $terms[$term['id']] = $term;
            }
            $cache->setCache('terms', $terms);
        }

        return $terms;
    }

    /**
     * @param array $arr
     * @param string|integer $key
     * @return string `$arr[$key]` if present, `''` otherwise
     */
    public function blank($arr, $key)
    {
        if (empty($arr[$key])) {
            return '';
        } else {
            return $arr[$key];
        }
    }

    /**
     * @param integer $account Canvas account ID
     * @return boolean `TRUE` if the account is a child of the Academics
     *                        account, `FALSE` otherwise
     */
    public function isAcademic($account)
    {
        if ($account == 132) { // FIXME really, hard-coded values? Really?
            return true;
        } elseif ($account == 1 || !is_integer($account)) {
            return false;
        } else {
            if (empty($this->accounts)) {
                $this->accounts = $this->getAccountList();
            }
            return isAcademic($this->accounts[$account]['parent_account_id']);
        }
    }
}
