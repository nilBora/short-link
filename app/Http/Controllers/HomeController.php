<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ShortenerController as Shortener;
use App\Models\ShortLink;
use App\Models\Setting;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $shortUrl = false;
        $currentUrl = false;
        
        if ($request->request->has('link')) {
            $currentUrl = $request->request->get('link');
            $shortUrl = $this->_getShortUrl($request, $currentUrl);
        }
        
        $data = [
            'short_url'   => $shortUrl,
            'current_url' => $currentUrl
        ];
        
        return view('home', $data);
    }
    
    public function _getShortUrl(Request $request, $currentUrl)
    {   
        $generaorObject = new HashGeneratorController();
        
        if (!$generaorObject->isValidUrl($currentUrl)) {
            return "Url Is Not Valid";
        }
            
        $linkData = ShortLink::where('url', $currentUrl)->first();
       
        if ($linkData) {
            return $linkData->short_url;
        }
        
        $generaorObject = new HashGeneratorController();
        
        $hash = $generaorObject->newHash();
        
        $host = $this->_getHost();
        
        $shortUrl = $host.$hash;
        
        $shortLinkObject = new ShortLink();
        
        $shortLinkObject->url = $currentUrl;
        $shortLinkObject->hash = $hash;
        $shortLinkObject->short_url = $shortUrl;
        $shortLinkObject->ip = $request->ip();
        $shortLinkObject->save();
        
        return $shortUrl;
    }
    
    private function _getHost()
    {   
        return Setting::where('name', 'host')->first()->value;
    }
    
    public function onRedirectUrl($hash)
    {
        $data = \DB::table('short_links')->where('hash', $hash)->first();
        if (!$data) {
            return abort(404);
        }
        //XXX: Добавить в очередь
        \DB::Table('short_links')->where('id', $data->id)->Increment('redirect_count', 1);
        
        return redirect($data->url);
    }
    
    public function doAjaxShortLink()
    {
        
    }
}
