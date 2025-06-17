<?php

    require "../conn.php";

    function getDataFromRequest() {
        $GLOBALS['isPaginated'] = getValueFromGet(value: 'isPaginated', isBool: true) ? true : false;
        $GLOBALS['limit']       = getValueFromGet(value: 'limit') == null ? 20 : getValueFromGet(value: 'limit');
        $GLOBALS['page']        = getValueFromGet(value: 'page') == null ? 1 : getValueFromGet(value: 'page');
        $GLOBALS['searchText']  = getValueFromGet(value: 'searchText') == null ? '' : getValueFromGet(value: 'searchText');
        $GLOBALS['filtersMap']  = getValueFromGet(value: 'filtersMap') == null ? '{}' : getValueFromGet(value: 'filtersMap');
        $GLOBALS['orderBy']     = getValueFromGet(value: 'orderBy') == null ? 'userId DESC' : getValueFromGet(value: 'orderBy');
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
            $searchQuery = '(fullName LIKE ?)';
            $searchValue = '%'.$GLOBALS['searchText'].'%';

            $filtersArray = json_decode($GLOBALS['filtersMap'], true);

            // Gender Filter
            $genderValue = array_key_exists('gender', $filtersArray) ? $filtersArray['gender'] : null;
            $genderQuery = is_null($genderValue) ? "" : "AND gender = '$genderValue'";

            // Start Date Of Created Filter
            $startDateOfCreatedValue =  array_key_exists('startDateOfCreated', $filtersArray) ? $filtersArray['startDateOfCreated'] : null;
            $startDateOfCreatedQuery = is_null($startDateOfCreatedValue) ? "" : "AND createdAt >= $startDateOfCreatedValue";

            // End Date Of Created Filter
            $endDateOfCreatedValue = array_key_exists('endDateOfCreated', $filtersArray) ? $filtersArray['endDateOfCreated'] : null;
            $endDateOfCreatedQuery = is_null($endDateOfCreatedValue) ? "" : "AND createdAt <= $endDateOfCreatedValue";
            
            // isFeatured Filter
            $isFeaturedValue = array_key_exists('isFeatured', $filtersArray) ? $filtersArray['isFeatured'] : null;
            $isFeaturedQuery = is_null($isFeaturedValue) ? "" : "AND isFeatured = '$isFeaturedValue'";

            // countryId
            $countryIdValue = array_key_exists('country', $filtersArray) ? $filtersArray['country'] : null;
            $countryIdQuery = is_null($countryIdValue) ? "" : "AND countryId = '$countryIdValue'";

            // Filters Query
            $filtersQuery = "$genderQuery $startDateOfCreatedQuery $endDateOfCreatedQuery $isFeaturedQuery $countryIdQuery";

            $total = countRows(table: 'users', where: "$searchQuery $filtersQuery", valuesArray: array($searchValue));

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

                $users = getAll(table: 'users',  where: "$searchQuery $filtersQuery", valuesArray: array($searchValue), orderBy: $GLOBALS['orderBy'], limit: $limit, offset: $offset);
                
                if($users || count($users) == 0) {

                    $result = [
                        'base' => [
                            'status' => true,
                            'messageAr' => 'نجاح',
                            'messageEn' => 'Success',
                        ],
                        'pagination' => $pagination,
                        'users' => $users,
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