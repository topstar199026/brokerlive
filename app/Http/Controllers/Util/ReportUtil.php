<?php

namespace App\Http\Controllers\Util;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Util\SystemTasksUtil;

use App\Models\TaskStatus;
use App\Models\NestedReferrerData;
class ReportUtil extends Controller
{
    public static function prepareData($brokerId, $options = null)
    {
        $dbData = self::getDataFromDB($brokerId, $options);
        if (isset($options['status'])) {
            $status = $dbData->toArray();
            unset($status['id']);
            return $status;
        }

        if (isset($dbData['data'])) {
            $dbData['data'] = self::buildTreeData($dbData['data']);
        }

        return $dbData;
    }
    
    public static function getStatus($brokerId, $options = null)
    {
        return SystemTasksUtil::getStatus("RefreshNestedReferrerData_user_{$brokerId}");
    }

    public static function getDataFromDB($brokerId, $options = null)
    {
        $status = self::getStatus($brokerId);
        // if no refresh has ever been triggered
        // or if cache is set to 'false'
        // and there is no running referesh task
        // re-refresh the data
        if (!$status->loaded() ||
            (isset($options['cache']) && $options['cache'] === false &&
            $status->status !== 'running')) {
            // start the refresh task
            SystemTasksUtil::runTask('RefreshNestedReferrerData', array(
                'userId' => $brokerId
            ));
            
            return array(
                'status' => 'running'
            );
        }
        if ($status->loaded() && $status->status == 'running') {
            return array(
                'status' => 'running',
                'details' => $status->as_array()
            );
        }

        if (!$status->loaded()) {
            // get status of refresh all task
            $status = self::getStatus(false);
        }

        $query = NestedReferrerData::select()
        ->where('user_id', '=', $brokerId)
        ->orderBy('referrer');

        return array(
            'status' => $status->status,
            'details' => $status->toArray(),
            'data' => $query->get()
        );
    }

    /**
     * raw data will be in a flat format, we need to reformat it in hierarchical tree form
     * *parent* - child relationship determined by referrer > applicant
     * referrer can be from loansplit or deal
     *   - format: [id]|[Name]
     * applicants can be loan_applicants or deal_applicants. loan_applicants will be used if it's not empty
     *   - format: [id]|First Last,[id]|First Last
     * a typical flat record:
     * deal_id, loansplit_id, user_id, deal_name, settlement_date, split_values, referrer, loan_applicants, deal_applicants
     *
     * 32, 24, 2, Adams, Abbie & Giles, Michael, 2012-07-20, 438800|438800, 22|Peter Gindy, NULL, 425|Alana De Wolde,426|Brad Schaeche
     *
     *
     * Tree structure:
     *   Parent node (referrer)
     *       . Split - data
     *       . Split - data
     *       + Child applicant - referrer
     *       . split
     *       - Child applicant - referrer
     *             . Split - data
     *             + Nested applicant - referrer
     *
     * PHP References are NO GOOD at all!
     */
    private static function buildTreeData($rawData)
    {
        // hold the tree data (only the root nodes)
        $flatTreeData = [];
        // map the node to the index in the tree
        $referrerMap = [];

        foreach ($rawData as $row) {
            self::treeRowFromSplitRow($row, $flatTreeData, $referrerMap);
        }

        // move orphan nodes to orphan-root and remove it from the parent
        foreach ($flatTreeData[0] as $i => $child) {
            if ($child['ref_id'] == 0) {
                $orphan['children'][] = $child;
                array_splice($flatTreeData[0], $i, 1);
            }
        }

        $treeData = self::buildNestedTreeData($flatTreeData, $flatTreeData[0]);

        if (isset($flatTreeData['orphan'])) {
            // the first one is the Broken links, to highlight brokenlink to user
            $orphan = [
                'type' => 'orphan-root',
                'parent' => -1,
                'deal_id' => 0,
                'deal_name' => '',
                'ref_id' => 0,
                'name' => '',
                'text' => 'No referrer',
                'split' => 0,
                'upfront' => 0,
                'trail' => 0,
                'children' => $flatTreeData['orphan']
            ];
            array_unshift($treeData, $orphan);
        }

        foreach ($treeData as &$node) {
            self::sumTree($node);
        }

        return array_values($treeData);
    }

    private static function treeRowFromSplitRow(&$row, &$flatTreeData, &$referrerMap)
    {
        self::rowPreprocess($row, $flatTreeData, $referrerMap);
        $parentID = intval($row['referrer_id']);
        if (isset($row['loan_applicants'])) {
            $applicants = explode(',', $row['loan_applicants']);
            // create nodes for applicants
            for ($i = 0; $i < count($applicants); $i++) {
                $splits = explode('|', $applicants[$i]);
                if (count($splits) < 2) {
                    // something wrong with the data, just skip
                    continue;
                }
                $applicantID = intval($splits[0]);
                $applicantName = $splits[1];
            
                if ($applicantID == 0 || $parentID == $applicantID) {
                    continue;
                }

                if (!isset($referrerMap[$applicantID])) {
                    // if the applicant is not present in the tree, create it create new node
                    $applicant = [
                        'type' => 'referrer',
                        'parent' => $parentID,
                        'deal_id' => $row['deal_id'],
                        'deal_name' => $row['deal_name'],
                        'ref_id' => $applicantID,
                        'name' => $applicantName,
                        'split' => 0,
                        'upfront' => 0,
                        'trail' => 0,
                        'children' => [],
                        'rawData' => $row,
                        'a_attr' => [
                            'class' => 'referrer-node'
                        ]
                    ];
                    $flatTreeData[$parentID][] = $applicant;
                } else {
                    // if the applicant is present, move it to the correct parent
                    // this is because we have flat table structure, and applicant
                    // can be created in root node before we have information about his/her referrer
                    // just to be sure we record the old parent
                    // so whatever later will be use
                    // $referrerMap store data in this form: old_parent|index_in_old_parent
                    $splits = explode('|', $referrerMap[$applicantID]);
                    $oldParent = intval($splits[0]);

                    if ($oldParent !== $parentID) {
                        $oldIndex = intval($splits[1]);
                        $applicant = $flatTreeData[$oldParent][$oldIndex];

                        // remove item from old parent
                        array_splice($flatTreeData[$oldParent], $oldIndex, 1);
                        // loop through all other siblings and reduce the index by 1
                        // if their position is greater than the removed applicant in the old parent
                        foreach ($flatTreeData[$oldParent] as $sibling) {
                            if (isset($referrerMap[$sibling['ref_id']])) {
                                $sibSplits = explode('|', $referrerMap[$sibling['ref_id']]);
                                $sibParent = intval($sibSplits[0]);
                                $sibIndex = intval($sibSplits[1]);
                                if ($sibIndex > $oldIndex) {
                                    $referrerMap[$sibling['ref_id']] = $sibParent . '|' . ($sibIndex - 1);
                                }
                            }
                        }
                        // add to new parent
                        $flatTreeData[$parentID][] = $applicant;
                    }
                }
                // re-record it in new parent
                $referrerMap[$applicantID] = $parentID . '|' . (count($flatTreeData[$parentID]) - 1);
            }
        }

        // create nodes for split (only 1)
        if (isset($row['loansplit_id'])) {
            $splitNode = [
                'type' => 'split',
                'parent' => $parentID,
                'deal_id' => $row['deal_id'],
                'split_id' => $row['loansplit_id'],
                'ref_id' => $row['deal_id'] . '_' . $row['loansplit_id'],
                'split' => 1,
                'upfront' => $row['upfront'],
                'trail' => $row['trail'],
                'referrer_id' => $row['referrer_id'],
                'referrer_name' => $row['referrer'],
                'deal_name' => $row['deal_name'],
                'rawData' => $row,
                'a_attr' => [
                    'class' => 'split-node'
                ]
            ];
            if ($parentID == 0) {
                // orphan split
                $flatTreeData['orphan'][] = $splitNode;
            } else {
                $flatTreeData[$parentID][] = $splitNode;
            }
        }
    }

    private static function rowPreprocess(&$row, &$flatTreeData, &$referrerMap)
    {
        if (!isset($row['loan_applicants'])) {
            // copy deal_applicants to loan_applicants if it is empty
            // subsequence code just need to check loan_applicants
            $row['loan_applicants'] = $row['deal_applicants'];
        }

        // split the referrer
        if (isset($row['referrer'])) {
            $referrerSplits = explode('|', $row['referrer']);
            $row['referrer_id'] = $referrerSplits[0];
            $row['referrer'] = $referrerSplits[1];
        } else {
            $row['referrer_id'] = 0;
            $row['referrer'] = '';
        }

        if (isset($row['split_values'])) {
            $values = explode('|', $row['split_values']);
            $row['upfront'] = (float)$values[0];
            $row['trail'] = (float)$values[1];
        } else {
            $row['upfront'] = 0;
            $row['trail'] = 0;
        }

        // Log::instance()->add(Log::INFO, 'Pre-Processing row, ref_id #:id', array(
        //     ':id' => $row['referrer_id']
        // ))->write();
        if (!isset($referrerMap[$row['referrer_id']])) {
            // Log::instance()->add(Log::INFO, '--- Referrer not existed, creating a new parent node')->write();
            // root parent
            $node = [
                'parent' => 0, // << indicate a root node
                'type' => 'referrer',
                'deal_id' => $row['deal_id'],
                'deal_name' => $row['deal_name'],
                'ref_id' => $row['referrer_id'],
                'name' => $row['referrer'],
                'text' => '(Referrer only. No deal)',
                'split' => 0,
                'upfront' => 0,
                'trail' => 0,
                'rawData' => $row,
                'a_attr' => [
                    'class' => 'referrer-node'
                ]
            ];

            $flatTreeData[0][] = $node;
            $referrerMap[$row['referrer_id']] = '0|' . (count($flatTreeData[0]) - 1);
        }
    }

    private static function buildNestedTreeData(&$flatTreeData, $children)
    {
        $tree = [];
        foreach ($children as $child) {
            if (isset($flatTreeData[$child['ref_id']])) {
                $child['children'] =
                    self::buildNestedTreeData(
                        $flatTreeData,
                        $flatTreeData[$child['ref_id']]
                    );
            }
            $tree[] = $child;
        }
        return $tree;
    }

    private static function sumTree(&$node)
    {
        if ($node['type'] == 'split') {
            self::setNodeText($node);
            return array($node['upfront'], $node['trail'], 1);
        }

        if (!empty($node['children'])) {
            foreach ($node['children'] as &$child) {
                $values = self::sumTree($child);
                $node['upfront'] += $values[0];
                $node['trail'] += $values[1];
                $node['split'] += $values[2];
            }
            // change a referrer node to referrer-plus if only
            // it is a referrer node, and it has children, and at least 1 split
            if ($node['type'] == 'referrer' && $node['split'] > 0) {
                $node['type'] = 'referrer-plus';
            }
        }

        self::setNodeText($node);
        return array($node['upfront'], $node['trail'], $node['split']);
    }

    /**
     * Set the text to be displayed for a row
     *   - If there is no parent deal, display the referrer's name
     *   - If there is parent deal, display the parent deal as:
     *      Settlement Date | Status | (x loan splits: upfront xxx - trail xxx) | deal name
     *
     */
    private static function setNodeText(&$node)
    {
        switch ($node['type']) {
            case 'referrer':
            case 'referrer-plus':
                $node['text'] = $node['name'] . ' (#' . $node['ref_id'] . ') ' .
                    ($node['split'] == 0 ? '' :
                    ($node['split'] . ' split' . ($node['split'] != 1 ? 's' : '')) .
                    ': Upfront $' . number_format($node['upfront']) .
                    ' - Trail $' . number_format($node['trail']));
                break;
            case 'split':
                $node['text'] = $node['rawData']['settlement_date'] . ' | '.
                    $node['rawData']['deal_name'] . ' | '.
                    'Upfront $' . number_format($node['upfront']) .
                    ' - Trail $' . number_format($node['trail']);
                break;
        }
    }
}
