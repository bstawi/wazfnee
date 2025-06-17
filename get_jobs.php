<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['isPaginated']     = getValueFromGet(value: 'isPaginated', isBool: true) ? true : false;
        $GLOBALS['limit']           = getValueFromGet(value: 'limit') == null ? 20 : getValueFromGet(value: 'limit');
        $GLOBALS['page']            = getValueFromGet(value: 'page') == null ? 1 : getValueFromGet(value: 'page');
        $GLOBALS['searchText']      = getValueFromGet(value: 'searchText') == null ? '' : getValueFromGet(value: 'searchText');
        $GLOBALS['filtersMap']      = getValueFromGet(value: 'filtersMap') == null ? '{}' : getValueFromGet(value: 'filtersMap');
        $GLOBALS['orderBy']         = getValueFromGet(value: 'orderBy') == null ? 'jobId DESC' : getValueFromGet(value: 'orderBy');
        $GLOBALS['currentUserId']   = getValueFromGet(value: 'currentUserId');
    }

    function isExistAnyDataEmpty() {
        if(
            false
        ) {
            return true;
        }
        else {
            return false;
        }
    }

    if($_SERVER['REQUEST_METHOD'] == 'GET') {

        getDataFromRequest();
        
        if(isExistAnyDataEmpty()) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'املأ جميع البيانات المطلوبة',
                    'messageEn' => 'Fill all required data',
                ],
            ];
        }
        else if(!isNumberLargeThanZero($GLOBALS['limit'])) {

            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'يجب ان يكون الحد اكبر من 0',
                    'messageEn' => 'Limit must be greater than 0',
                ],
            ];
        }
        else if(!isNumberLargeThanZero($GLOBALS['page'])) {
            
            $result = [
                'base' => [
                    'status' => false,
                    'messageAr' => 'يجب ان تكون الصفحة اكبر من 0',
                    'messageEn' => 'Page must be greater than 0',
                ],
            ];
        }
        else {
            
            // Search
            $searchQuery = '(title LIKE ?)';
            $searchValue = '%'.$GLOBALS['searchText'].'%';

            $filtersArray = json_decode($GLOBALS['filtersMap'], true);

            // Start Date Of Created Filter
            $startDateOfCreatedValue =  array_key_exists('startDateOfCreated', $filtersArray) ? $filtersArray['startDateOfCreated'] : null;
            $startDateOfCreatedQuery = is_null($startDateOfCreatedValue) ? "" : "AND createdAt >= $startDateOfCreatedValue";

            // End Date Of Created Filter
            $endDateOfCreatedValue = array_key_exists('endDateOfCreated', $filtersArray) ? $filtersArray['endDateOfCreated'] : null;
            $endDateOfCreatedQuery = is_null($endDateOfCreatedValue) ? "" : "AND createdAt <= $endDateOfCreatedValue";

            // isAvailable
            $isAvailableValue = array_key_exists('isAvailable', $filtersArray) ? $filtersArray['isAvailable'] : null;
            $isAvailableQuery = is_null($isAvailableValue) ? "" : "AND isAvailable = '$isAvailableValue'";

            // categoryId
            $categoryIdValue = array_key_exists('categoryId', $filtersArray) ? $filtersArray['categoryId'] : null;
            $categoryIdQuery = is_null($categoryIdValue) ? "" : "AND categoryId = '$categoryIdValue'";

            // userId
            $userIdValue = array_key_exists('user', $filtersArray) ? $filtersArray['user'] : null;
            $userIdQuery = is_null($userIdValue) ? "" : "AND userId = '$userIdValue'";

            // countryId
            $countryIdValue = array_key_exists('country', $filtersArray) ? $filtersArray['country'] : null;
            $countryIdQuery = is_null($countryIdValue) ? "" : "AND countryId = '$countryIdValue'";

            // currencyId
            $currencyIdValue = array_key_exists('currency', $filtersArray) ? $filtersArray['currency'] : null;
            $currencyIdQuery = is_null($currencyIdValue) ? "" : "AND currencyId = '$currencyIdValue'";
        
            // isFeatured
            $isFeaturedValue = array_key_exists('isFeatured', $filtersArray) ? $filtersArray['isFeatured'] : null;
            $isFeaturedQuery = is_null($isFeaturedValue) ? "" : "AND isFeatured = '$isFeaturedValue'";

            // adStatus
            $adStatusValue = array_key_exists('adStatus', $filtersArray) ? $filtersArray['adStatus'] : null;
            $adStatusQuery = is_null($adStatusValue) ? "" : "AND adStatus = '$adStatusValue'";
            
            // Filters Query
            $filtersQuery = "$startDateOfCreatedQuery $endDateOfCreatedQuery $isAvailableQuery $categoryIdQuery $userIdQuery $countryIdQuery $currencyIdQuery $isFeaturedQuery $adStatusQuery";

            $total = countRows(table: 'jobs', where: "$searchQuery $filtersQuery", valuesArray: array($searchValue));

            if(!$GLOBALS['isPaginated']) {
                $limit = $total == 0 ? 1 : $total;
                $page = 1;
                $pagesNumber = 1;
                $offset = 0;
            }
            $limit = (int) $GLOBALS['limit'];
            $page = (int) $GLOBALS['page'];
            $pagesNumber = (int) ceil($total / $limit);
            $offset = ($page -1) * $limit;
            $pagination = [
                'total' => $total,
                'limit' => $limit,
                'page' => $page,
                'pagesNumber' => $pagesNumber,
                'offset' => $offset,
                'isPaginated' => $GLOBALS['isPaginated'],
            ];
            
            if(isPageRangeValid($page, $pagesNumber) || $page == 1) {
    
                $jobs = getAll(
                    table: 'jobs',
                    where: "$searchQuery $filtersQuery",
                    valuesArray: array($searchValue),
                    orderBy: $GLOBALS['orderBy'],
                    limit: $limit,
                    offset: $offset,
                    currentUserId: $GLOBALS['currentUserId'],
                );
                
                if($jobs || count($jobs) == 0) {
    
                    $result = [
                        'base' => [
                            'status' => true,
                            'messageAr' => 'نجاح',
                            'messageEn' => 'Success',
                        ],
                        'pagination' => $pagination,
                        'jobs' => $jobs,
                    ];
                }
                else {
    
                    $result = [
                        'base' => [
                            'status' => false,
                            'messageAr' => 'حدث خطأ',
                            'messageEn' => 'An error occurred',
                        ],
                    ];
                }
            }
            else {
                
                $result = [
                    'base' => [
                        'status' => false,
                        'messageAr' => 'الصفحة غير موجودة',
                        'messageEn' => 'Page not found',
                    ],
                ];
            }
        }
    }
    else {
        
        $result = [
            'base' => [
                'status' => false,
                'messageAr' => 'حدث خطأ',
                'messageEn' => 'An error occurred',
            ],
        ];
    }

    header('Content-Type: application/json');

    echo json_encode($result); 