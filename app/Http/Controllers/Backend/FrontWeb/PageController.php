<?php

namespace App\Http\Controllers\Backend\FrontWeb;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\FrontWeb\Pages\UpdateRequest;
use App\Models\Backend\FrontWeb\Page;
use App\Models\Backend\FrontWeb\Section;
use App\Models\Backend\Setting;
use App\Repositories\FrontWeb\Pages\PagesInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class PageController extends Controller
{
    private const HEADER_MENU_SECTION_TYPE = 99;
    private const HEADER_MENU_KEY = 'frontend_header_menu';

    protected $repo;
    public function __construct(PagesInterface $repo)
    {
        $this->repo = $repo;
    }
    public function index(){
        $pages = $this->repo->all();
        $allPages = Page::orderBy('id')->get(['id', 'page', 'title', 'status']);
        $activePages = $allPages
            ->where('status', Status::ACTIVE)
            ->values()
            ->map(function ($page) {
                return [
                    'id' => (int) $page->id,
                    'page' => $page->page,
                    'title' => $page->title,
                    'default_url' => $this->defaultUrl($page->page),
                ];
            });
        $savedMenu = $this->getSavedHeaderMenu();
        $menuItems = $this->buildMenuItems($allPages, $savedMenu);

        return view('backend.front_web.pages.index', compact('pages', 'menuItems', 'activePages'));
    }

    public function edit($id){
        $page  = $this->repo->getFind($id); 
        return view('backend.front_web.pages.edit',compact('page'));
    }
    public function update(UpdateRequest $request,$id){
        if($this->repo->update($id,$request)):
            Toastr::success(__('levels.page_updated'),__('message.success'));
            return redirect()->route('pages.index');
        else:
            Toastr::error(__('parcel.error_msg'),__('message.error'));
            return redirect()->back()->withInput();
        endif;
    }

    public function updateMenu(Request $request)
    {
        $request->validate([
            'menu' => ['required', 'array', 'min:1'],
            'menu.*.page_id' => ['required', 'integer', 'exists:pages,id'],
            'menu.*.label' => ['required', 'string', 'max:190'],
            'menu.*.url' => ['required', 'string', 'max:255'],
        ]);

        $pageIds = collect($request->input('menu', []))
            ->pluck('page_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $activePages = Page::query()
            ->whereIn('id', $pageIds)
            ->where('status', Status::ACTIVE)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $payload = [];
        foreach ($request->input('menu', []) as $item) {
            $pageId = (int) $item['page_id'];
            if (!in_array($pageId, $activePages, true)) {
                continue;
            }

            $payload[] = [
                'page_id' => $pageId,
                'label' => trim((string) $item['label']),
                'url' => trim((string) $item['url']),
            ];
        }

        if (empty($payload)) {
            Toastr::error('No valid active pages found for header menu.', __('message.error'));
            return redirect()->back()->withInput();
        }

        $encoded = json_encode($payload);
        Section::query()->updateOrCreate(
            ['type' => self::HEADER_MENU_SECTION_TYPE, 'key' => self::HEADER_MENU_KEY],
            ['value' => $encoded]
        );
        // Keep legacy key updated for compatibility where payload is short.
        Setting::query()->updateOrCreate(
            ['key' => self::HEADER_MENU_KEY],
            ['value' => $encoded]
        );

        Toastr::success('Header menu updated successfully.', __('message.success'));
        return redirect()->route('pages.index');
    }

    private function buildMenuItems($allPages, ?string $savedMenu): array
    {
        $pagesById = $allPages->keyBy('id');
        $items = [];

        if (!empty($savedMenu)) {
            $decoded = json_decode($savedMenu, true);
            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    $pageId = (int) data_get($item, 'page_id');
                    $page = $pagesById->get($pageId);
                    if (!$page || (int) $page->status !== Status::ACTIVE) {
                        continue;
                    }

                    $items[] = [
                        'page_id' => $pageId,
                        'slug' => $page->page,
                        'default_title' => $page->title,
                        'label' => data_get($item, 'label', $page->title),
                        'url' => data_get($item, 'url', $this->defaultUrl($page->page)),
                    ];
                }
            }
        }

        if (!empty($items)) {
            return $items;
        }

        foreach ($allPages as $page) {
            if ((int) $page->status !== Status::ACTIVE) {
                continue;
            }

            $items[] = [
                'page_id' => (int) $page->id,
                'slug' => $page->page,
                'default_title' => $page->title,
                'label' => $page->title,
                'url' => $this->defaultUrl($page->page),
            ];
        }

        return $items;
    }

    private function defaultUrl(?string $slug): string
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

    private function getSavedHeaderMenu(): ?string
    {
        $fromSection = Section::query()
            ->where('type', self::HEADER_MENU_SECTION_TYPE)
            ->where('key', self::HEADER_MENU_KEY)
            ->value('value');

        if (!empty($fromSection)) {
            return $fromSection;
        }

        return Setting::query()->where('key', self::HEADER_MENU_KEY)->value('value');
    }
}
