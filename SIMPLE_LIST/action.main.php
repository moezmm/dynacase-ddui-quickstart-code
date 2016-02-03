<?php

function main(Action &$action) {

    require_once 'FDL/freedom_util.php';

    $usage = new ActionUsage($action);

    $usage->setStrictMode(false);
    $usage->verify(true);

    $searchName = ApplicationParameterManager::getParameterValue(
        ApplicationParameterManager::CURRENT_APPLICATION, "SEARCH_NAME"
    );

    $search = new_Doc('', $searchName, true);
    if($search->isAlive()) {
        if (is_a($search, '\Dcp\Family\Search')) {
            $familyId = $search->getAttributeValue(
                \Dcp\AttributeIdentifiers\Search::se_famid
            );
            $action->lay->eSet("search_name", $searchName);
            $action->lay->eSet("search_title", $search->getTitle());
            $action->lay->eSet("family_id", $familyId);
            $action->lay->eSet("family_title", $search->getTitle($familyId));
        } else {
            $action->lay->eSet("error", "'$searchName' is not a search");
        }
    } else {
        $action->lay->eSet("error", "'$searchName' is not alive");
    }

    $action->lay->eSet("WS", \ApplicationParameterManager::getParameterValue("CORE", "WVERSION"));

}