<?php

namespace smtech\AdvisorDashboard;

use smtech\LTI\Configuration\Option;
use Battis\DataUtilities;
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
    function getAccountList()
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
    function getTermList()
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
}
