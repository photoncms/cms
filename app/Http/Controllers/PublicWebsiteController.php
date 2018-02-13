<?php

namespace Photon\Http\Controllers;

use Photon\Http\Controllers\Controller;
use Photon\PhotonCms\Core\IAPI\IAPI;

class PublicWebsiteController extends Controller
{
    private $IAPI;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(IAPI $IAPI)
    {
        $this->IAPI = $IAPI;
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Following are examples of all available call chains. Some examples might need preparation of data that they are supposed to manipulate.

//        $result = $this->IAPI->users->get();
//        var_dump($result);
//        $result = $this->IAPI->users(1)->get();
//        var_dump($result);
//        $result = $this->IAPI->extension_call->users(1)->test->get('test1', 'test2');
//        var_dump($result);
//        $result = $this->IAPI->nodes->albums->get();
//        var_dump($result);
//        $result = $this->IAPI->nodes->albums(1)->get(['child_modules' => ['albums', 'lyrics']]);
//        var_dump($result);
//        $result = $this->IAPI->nodes->albums->ancestors(1)->get();
//        var_dump($result);
//        $result = $this->IAPI->news(1)->delete(['force' => true]);
//        var_dump($result);
//        $result = $this->IAPI->news(1)->put(['title' => 'true', 'content' => 'something']);
//        var_dump($result);
//        $result = $this->IAPI->news->put(['title' => 'true', 'filter' => ['user' => 1]]);
//        var_dump($result);
//        $result = $this->IAPI->nodes->reposition->put(['action' => 'setScope', 'affected' => ['table' => 'products', 'id' => 1], 'target' => ['id' => 1]]);
//        var_dump($result);
//        $result = $this->IAPI->news->post([
//            'title' => 'Some title',
//            'content' => 'Some content',
//            'author' => 1
//        ]);
//        var_dump($result);
//        $result = $this->IAPI->filter->news->post([
//            'filter' => ['author' => ['equal' => 1]],
//            'pagination' => [
//                'items_per_page' => 2,
//                'current_page' => 1
//            ],
//            'sorting' => [
//                'id' => 'desc'
//            ]
//        ]);
//        var_dump($result);
//        $result = $this->IAPI->count->users->post(['filter' => ['confirmed' => ['equal' => 1]]]);
//        var_dump($result);

        return view('welcome');
    }
}
