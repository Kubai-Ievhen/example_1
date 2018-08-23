<?php
/**
 * Created by PhpStorm.
 * User: yevhen
 * Date: 24.05.18
 * Time: 11:46
 */

namespace App\Resources;


use Illuminate\Http\Request;

class HelperResource
{

    /**
     * @param Request $request
     * @param $result
     * @param $page
     * @param $routeName
     * @return array
     */
    public static function formPaginationData(Request $request, $result, $page, $perPage, $routeName, array $routeData=[]){
        $count = $result->count();
        $lastPage = ($count-$count%$perPage)/$perPage+($count%$perPage?1:0);
        $data = [];

        $data['first_page_url'] = self::formSearchLink($request,$routeName, $routeData,1);
        $data['current_page'] = $page;
        $data['from'] = $perPage*$page - ($perPage-1);
        $data['last_page'] = $lastPage;
        $data['last_page_url'] = self::formSearchLink($request,$routeName,$routeData, $lastPage);
        $data['next_page_url'] = $page+1<=$lastPage?self::formSearchLink($request,$routeName, $routeData,$page+1):null;
        $data['path'] = self::formSearchLink($request,$routeName,$routeData);
        $data['per_page'] = $perPage;
        $data['prev_page_url'] = null;
        $data['to'] = $perPage*$page>$count?$count:$perPage*$page;
        $data['total'] = $count;

        return $data;
    }

    /**
     * @param Request $request
     * @param $routeName
     * @param bool $page
     * @return string
     */
    public static function formSearchLink(Request $request,$routeName,array $route_data=[], $page = false){
        $baseUrl = route($routeName,$route_data);
        $urlData = $request->all();

        if ($page) {
            $urlData['page'] = $page;
        } else {
            unset($urlData['page']);
        }

        return $baseUrl.'?'.http_build_query($urlData);
    }
}