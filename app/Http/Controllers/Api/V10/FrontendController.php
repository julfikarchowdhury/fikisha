<?php

namespace App\Http\Controllers\Api\V10;

use App\Enums\DeliveryType;
use App\Enums\SectionType;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\MerchantSignUpRequest;
use App\Http\Requests\MerchantPanel\Parcel\ParcelCalculateRequest;
use App\Http\Requests\MerchantPanel\Parcel\StoreApiRequest;
use App\Http\Resources\Frontend\BlogResource;
use App\Http\Resources\Frontend\PartnerResource;
use App\Http\Resources\Frontend\ServiceResource;
use App\Http\Resources\Frontend\SliderResource;
use App\Mail\ContactMail;
use App\Models\Backend\FrontWeb\Page;
use App\Models\Backend\FrontWeb\Section;
use App\Models\Backend\Setting;
use App\Models\Language;
use App\Repositories\FrontWeb\Blogs\BlogsInterface;
use App\Repositories\FrontWeb\Faq\FaqInterface;
use App\Repositories\FrontWeb\Pages\PagesInterface;
use App\Repositories\FrontWeb\Partner\PartnerInterface;
use App\Repositories\FrontWeb\Section\SectionInterface;
use App\Repositories\FrontWeb\Service\ServiceInterface;
use App\Repositories\FrontWeb\Slider\SliderInterface;
use App\Repositories\FrontWeb\SocialLink\SocialLinkInterface;
use App\Repositories\FrontWeb\WhyCourier\WhyCourierInterface;
use App\Repositories\Merchant\MerchantInterface;
use App\Repositories\Parcel\ParcelInterface;
use App\Repositories\ShippingType\ShippingTypeInterface;
use App\Repositories\Town\TownInterface;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontendController extends Controller
{
    use ApiReturnFormatTrait;
    private const HEADER_MENU_SECTION_TYPE = 99;
    private const HEADER_MENU_KEY = 'frontend_header_menu';

    protected $pageRepo,
        $sectionRepo,
        $merchantRepo,
        $parcelRepo,
        $townRepo,
        $shippingTypeRepo,$faqRepo,
        $serviceRepo,$whycourierRepo,
        $blogRepo,$partnerRepo,
        $socialLinkRepo,$sliderRepo;
    public function __construct(
        PagesInterface $pageRepo,
        SectionInterface $sectionRepo,
        MerchantInterface $merchantRepo,
        ParcelInterface $parcelRepo,
        TownInterface $townRepo,
        ShippingTypeInterface $shippingTypeRepo,
        FaqInterface          $faqRepo,
        ServiceInterface      $serviceRepo,
        WhyCourierInterface   $whycourierRepo,
        BlogsInterface        $blogRepo,
        PartnerInterface      $partnerRepo,
        SocialLinkInterface   $socialLinkRepo,
        SliderInterface       $sliderRepo, 
    ){
        $this->pageRepo         = $pageRepo;
        $this->sectionRepo      = $sectionRepo;
        $this->merchantRepo     = $merchantRepo;
        $this->parcelRepo       = $parcelRepo;
        $this->townRepo         = $townRepo;
        $this->shippingTypeRepo = $shippingTypeRepo;
        $this->faqRepo          = $faqRepo;
        $this->serviceRepo      = $serviceRepo;
        $this->whycourierRepo   = $whycourierRepo;
        $this->blogRepo         = $blogRepo;
        $this->partnerRepo      = $partnerRepo;
        $this->socialLinkRepo   = $socialLinkRepo;
        $this->sliderRepo       = $sliderRepo; 
    }

    // pages
  
    public function contactUs()
    {
        try {
            $page = $this->pageRepo->get('contact');
            $contact    = $this->sectionRepo->getFind(SectionType::CONTACT);
            $breadcrumb = $this->sectionRepo->getFind(SectionType::BREADCRUMB);
            return $this->responseWithSuccess(__('levels.contact_us'), [
                'page'         => $page,
                'contact'      => $contact,
                'breadcrumb'   => $breadcrumb
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    public function contactMessageSend(Request $request)
    {
        $request->validate([
            'name'    => ['required'],
            'email'   => ['required', 'email'],
            'subject' => ['required'],
            'message' => ['required', 'min:10']
        ]);
        try {

           
            Mail::send(new ContactMail($request->all()));
            return $this->responseWithSuccess(__('levels.message_sended_successfully'), [], 200);
        } catch (\Throwable $th) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    } 
 
    public function privacyPolicy()
    {
        try {
            $page = $this->pageRepo->get('privacy_policy');
            $breadcrumb = $this->sectionRepo->getFind(SectionType::BREADCRUMB);
            return $this->responseWithSuccess(__('levels.privacy_policy'), [
                'page' => $page,
                'breadcrumb'=> $breadcrumb
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }
    
    public function termsConditions()
    {
        try {
            $page = $this->pageRepo->get('terms_conditions');
            
            $breadcrumb = $this->sectionRepo->getFind(SectionType::BREADCRUMB);
            return $this->responseWithSuccess(__('levels.terms_conditions'), [
                'page' => $page,
                'breadcrumb' => $breadcrumb
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    /**
     * Public URLs for legal pages (SPA routes used by the web frontend).
     */
    public function legalPageLinks()
    {
        try {
            return $this->responseWithSuccess('', [
                'privacy_policy' => [
                    'slug' => 'privacy_policy',
                    'url' => url('/privacy/policy'),
                ],
                'terms_and_conditions' => [
                    'slug' => 'terms_conditions',
                    'url' => url('/terms/conditions'),
                ],
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    public function aboutUs()
    {
        try {
            $page = $this->pageRepo->get('about_us');
            $breadcrumb = $this->sectionRepo->getFind(SectionType::BREADCRUMB);
            return $this->responseWithSuccess(__('levels.about_us'), [
                'page'       => $page,
                'breadcrumb' => $breadcrumb
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    public function getFaq()
    {
        try {
            $page = $this->pageRepo->get('faq');
            $faq_list = $this->faqRepo->getActiveAll();
            return $this->responseWithSuccess(__('levels.faq'), [
                'page' => $page,
                'faq_list' => $faq_list
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    
    public function  networkCoverage(){
        try {
            $page = $this->pageRepo->get('network_coverage');
            $breadcrumb = $this->sectionRepo->getFind(SectionType::BREADCRUMB);
            return $this->responseWithSuccess(__('levels.network_coverage'), [
                'page' => $page,
                'breadcrumb' => $breadcrumb
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }
    public function  trackshippment(){
        try {
            $page = $this->pageRepo->get('track_shippment');
            $breadcrumb = $this->sectionRepo->getFind(SectionType::BREADCRUMB);
            return $this->responseWithSuccess(__('levels.track_shippment'), [
                'page'       => $page,
                'breadcrumb' => $breadcrumb
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }
    public function  booking(){
        try {
            $page = $this->pageRepo->get('get_qoute');
            $breadcrumb = $this->sectionRepo->getFind(SectionType::BREADCRUMB);
            return $this->responseWithSuccess(__('levels.track_shippment'), [
                'page'       => $page,
                'breadcrumb' => $breadcrumb
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }
  
    // end pages

    // sections
  
    public function sections()
    {
        try {
            $sections = [];
     
            $sections['service_banner']     = $this->sectionRepo->getFind(SectionType::BANNER);
            $sections['our_work_process']   = $this->sectionRepo->getFind(SectionType::OUR_WORK_PROCESS);
            $sections['about']              = $this->sectionRepo->getFind(SectionType::ABOUT);
            $sections['subscribe']          = $this->sectionRepo->getFind(SectionType::SUBSCRIBE);
            $sections['app_link']           = $this->sectionRepo->getFind(SectionType::APP_LINK);
            $sections['map_link']           = $this->sectionRepo->getFind(SectionType::MAP_LINK);
            $sections['why_choose_us']['info']      = $this->sectionRepo->getFind(SectionType::WHY_COURIER);
            $sections['why_choose_us']['list']      = $this->whycourierRepo->getAll();
       
            return $this->responseWithSuccess(__('levels.sections'), [
                'sections' => $sections
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    //end sections


    public function  serviceList(){
        try {
            $serviceList =[];
            $serviceList['inside_services']  =  ServiceResource::collection($this->serviceRepo->insideServiceAll());
            $serviceList['outside_services'] =  ServiceResource::collection($this->serviceRepo->outsideServiceAll());
            return $this->responseWithSuccess(__('levels.service_list'), [
                'serviceList' => $serviceList
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }
 
    public function serviceDetails($id){
      
        try { 
            $serviceDetails=  $this->shippingTypeRepo->get($id); 
            return $this->responseWithSuccess(__('levels.service_details'), [
                'service_details' => $serviceDetails
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    public function partner(){
        try { 
            $pertner =  PartnerResource::collection($this->partnerRepo->getAll()); 
            return $this->responseWithSuccess(__('levels.pertner'), [
                'pertner' => $pertner
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }
    public function sliders(){
        try { 
            $sliders =  SliderResource::collection($this->sliderRepo->all()); 
            return $this->responseWithSuccess(__('levels.pertner'), [
                'sliders' => $sliders
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }
    
    public function socialLinks(){
        try { 
            $social_links =  $this->socialLinkRepo->getAll(); 
            return $this->responseWithSuccess(__('levels.social_links'), [
                'social_links' => $social_links
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }
    public function blogList(){
        try { 
            $blogList =  BlogResource::collection($this->blogRepo->getActive(request()->limit)); 
            return $this->responseWithSuccess(__('levels.service_list'), [
                'blogList' => $blogList
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }
    public function BlogDetails($id){
        try { 
            $blogDetails = new BlogResource($this->blogRepo->getFind($id)); 
            return $this->responseWithSuccess(__('levels.blog_details'), [
                'blogDetails' => $blogDetails
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

   
    public function defaultData()
    {
        $settings = [
            'name' => settings()->name,
            'email' => settings()->email,
            'phone' => settings()->phone,
            'default_language' => settings()->default_language,
            'currency' => settings()->currency,
            'currency_location' => settings()->currency_location,
            'copyright' => settings()->copyright,
            'prefix' => settings()->prefix,
            'system_commission' => settings()->system_commission,
            'logo' => settings()->logo_image,
            'about' => settings()->about,
            'favicon' => settings()->favicon_image,
            'marketplace_pricing_mode' => settings()->marketplace_pricing_mode,
            'marketplace_base_fare' => settings()->marketplace_base_fare,
            'marketplace_per_km_rate' => settings()->marketplace_per_km_rate,
            'marketplace_per_kg_rate' => settings()->marketplace_per_kg_rate,
            'marketplace_receiver_markup_percent' => settings()->marketplace_receiver_markup_percent,
            'inside_city_distance' => settings()->inside_city_distance,
            'inside_city_base_fare' => settings()->inside_city_base_fare,
            'inside_city_per_km_rate' => settings()->inside_city_per_km_rate,
            'inside_city_per_kg_rate' => settings()->inside_city_per_kg_rate,
            'outside_city_base_fare' => settings()->outside_city_base_fare,
            'outside_city_per_km_rate' => settings()->outside_city_per_km_rate,
            'outside_city_per_kg_rate' => settings()->outside_city_per_kg_rate,
        ];
        $default = [ 
            'download_now' => $this->sectionRepo->getFind(SectionType::APP_LINK),
            'settings' => $settings,
            'languages' => Language::where('status',Status::ACTIVE)->get(),
            'social_links' => $this->socialLinkRepo->getAll(),
            'header_menu' => $this->headerMenu(),
        ];
        return $this->responseWithSuccess(__('success'), [
            'default' => $default
        ], 200);
    }
    
    public function changeLanguage($id)
    {
        return $this->responseWithSuccess(__('success'), [
            'language' =>Language::find($id)
        ], 200);
    }

    private function headerMenu(): array
    {
        $savedMenu = Section::query()
            ->where('type', self::HEADER_MENU_SECTION_TYPE)
            ->where('key', self::HEADER_MENU_KEY)
            ->value('value');
        if (empty($savedMenu)) {
            $savedMenu = Setting::query()->where('key', self::HEADER_MENU_KEY)->value('value');
        }
        $pages = Page::query()->where('status', Status::ACTIVE)->get(['id', 'page', 'title', 'status'])->keyBy('id');
        $items = [];

        if (!empty($savedMenu)) {
            $decoded = json_decode($savedMenu, true);
            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    $pageId = (int) data_get($item, 'page_id');
                    $page = $pages->get($pageId);
                    if (!$page) {
                        continue;
                    }

                    $items[] = [
                        'page_id' => $pageId,
                        'label' => data_get($item, 'label', $page->title),
                        'url' => data_get($item, 'url', $this->defaultPageUrl($page->page)),
                    ];
                }
            }
        }

        if (!empty($items)) {
            return $items;
        }

        foreach ($pages as $page) {
            $items[] = [
                'page_id' => (int) $page->id,
                'label' => $page->title,
                'url' => $this->defaultPageUrl($page->page),
            ];
        }

        return $items;
    }

    private function defaultPageUrl(?string $slug): string
    {
        $map = [
            'tracking' => '/tracking/log',
            'pricing' => '/pricing',
            'about_us' => '/about_us',
            'about' => '/about_us',
            'service' => '/truck/service',
            'truck_service' => '/truck/service',
            'contact' => '/contact',
            'faq' => '/faq',
            'network_coverage' => '/network/coverage',
            'track_shippment' => '/track/ship',
            'track_ship' => '/track/ship',
            'privacy_policy' => '/privacy/policy',
            'terms_conditions' => '/terms/conditions',
        ];

        return $map[$slug ?? ''] ?? '/';
    }


    public function merchantSignUp(Request $request)
    {
        $validator = new MerchantSignUpRequest();
        $validator = Validator::make($request->all(), $validator->rules());
        if ($validator->fails()) {
            return $this->responseWithError(__('merchant.added_msg'), ['message' => $validator->errors()], 422);
        }
        if ($this->merchantRepo->signUpStore($request)) {
            return $this->responseWithSuccess(__('merchant.added_msg'), ['mobile' => $request->mobile], 200);
        } else {
            return $this->responseWithError(__('merchant.error_msg'), [], 500);
        }
    }

 

    public function trackMyParcel(Request $request)
    {
        try {
            $parcel         = $this->parcelRepo->parcelTracking($request);
            $parcel_events   = $this->parcelRepo->parcelEvents($parcel->id ?? null);
            return $this->responseWithSuccess(__('levels.parcel'), [
                'parcel' => $parcel,
                'parcel_events' => $parcel_events
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    public function services()
    {
        try {
            $services = [
                1 => 'Individual',
                2 => 'Business',
            ];
            return $this->responseWithSuccess(__('levels.service'), [
                'services' => $services
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    public function provinceList()
    {
        try {
            $province_list = $this->townRepo->getAll();
            return $this->responseWithSuccess(__('levels.province'), [
                'province_list' => $province_list
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    public function ourNetwork(Request $request)
    {

        try {
            return $this->responseWithSuccess(__('levels.service'), [
                'hubs' => []
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    public function packagingList()
    {
        try {
            $packaging         = $this->parcelRepo->packaging();
            return $this->responseWithSuccess(__('levels.packaging'), [
                'packaging_list' => $packaging
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    public function getShippingType(Request $request)
    {
        try {
            if ($request->total_distance_km <= settings()->inside_city_distance) :
                $request['delivery_type_id'] = DeliveryType::SAMEDAY;
            else :
                $request['delivery_type_id'] = DeliveryType::SUBCITY;
            endif;
            $shipping_types = $this->shippingTypeRepo->getActive($request);
            return $this->responseWithSuccess(__('levels.shipping_type'), [
                'shipping_types' => $shipping_types
            ], 200);
        } catch (\Exception $exception) {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    public function calculateTheQuote(Request $request)
    {
        $validator = new ParcelCalculateRequest();
        $validator = Validator::make($request->all(), $validator->rules());

        if ($validator->fails()) {
            return $this->responseWithError(__('parcel.title'), ['message' => $validator->errors()], 422);
        }

        if ($this->parcelRepo->calculateTheQuote($request)) {
            return $this->responseWithSuccess(__('parcel.charge_details'), [
                'charge_details' => $this->parcelRepo->calculateTheQuote($request)
            ], 200);
        } else {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    public function parcelItemCalculate(Request $request)
    {
        $validator = new ParcelCalculateRequest();
        $validator = Validator::make($request->all(), $validator->rules());

        if ($validator->fails()) {
            return $this->responseWithError(__('parcel.title'), ['message' => $validator->errors()], 422);
        }

        if ($this->parcelRepo->parcelItemCalculate($request)) {
            return $this->responseWithSuccess(__('parcel.charge_details'), [
                'charge_details' => $this->parcelRepo->parcelItemCalculate($request)
            ], 200);
        } else {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }

    public function sendParcels(Request $request)
    {
        $validator = new StoreApiRequest();
        $validator = Validator::make($request->all(), $validator->rules());
        if ($validator->fails()) {
            return $this->responseWithError(__('parcel.title'), ['message' => $validator->errors()], 422);
        }
        $request['payment_mode'] = 2;
        $request['selling_price'] = 0;
        if ($this->parcelRepo->storeApi($request)) {
            return $this->responseWithSuccess(__('parcel.parcel_create_successfully'), [], 200);
        } else {
            return $this->responseWithError(__('levels.error_msg'), [], 500);
        }
    }


    
}
