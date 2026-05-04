@extends('backend.partials.master')
@section('title')
{{ __('levels.pages') }} {{ __('levels.list') }}
@endsection
@section('maincontent')

<div class="container-fluid  dashboard-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{__('levels.front_web')}}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link">{{ __('levels.pages') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            @if(hasPermission('pages_update'))
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">Header Menu</h4>
                    <small class="text-muted">Drag and drop to reorder menu items.</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('pages.menu.update') }}" method="POST" id="headerMenuForm">
                        @csrf

                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <select id="addMenuPageSelect" class="form-control">
                                    <option value="">Select page to add</option>
                                    @foreach($activePages as $activePage)
                                        <option value="{{ $activePage['id'] }}"
                                            data-label="{{ $activePage['title'] }}"
                                            data-url="{{ $activePage['default_url'] }}">
                                            {{ $activePage['title'] }} ({{ $activePage['page'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-sm btn-secondary" id="addMenuItemBtn">Add Page</button>
                            </div>
                        </div>

                        <div id="menuRows" class="d-flex flex-column gap-3">
                            @foreach($menuItems as $item)
                                <div class="menu-item card p-3 mb-2" data-page-id="{{ $item['page_id'] }}">
                                    <div class="d-flex align-items-center" style="gap: 12px;">
                                        <span class="drag-handle text-muted" style="cursor: move;">
                                            <i class="fa fa-arrows-alt"></i>
                                        </span>
                                        <input type="hidden" name="menu[{{ $loop->index }}][page_id]" value="{{ $item['page_id'] }}">
                                        <input type="text" class="form-control" name="menu[{{ $loop->index }}][label]" value="{{ $item['label'] }}" placeholder="Menu label" required>
                                        <input type="text" class="form-control" name="menu[{{ $loop->index }}][url]" value="{{ $item['url'] }}" placeholder="URL" required>
                                        <button type="button" class="btn btn-link text-danger p-0 remove-menu-item" title="Remove">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-sm btn-primary">Save Menu Order</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('levels.pages') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table " style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('levels.slug') }}</th>
                                    <th>{{ __('levels.title') }}</th>
                                    <th>{{ __('levels.status') }}</th>
                                    <th>{{ __('levels.updated') }}</th>
                                    @if(hasPermission('pages_update') )
                                    <th>{{ __('levels.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($pages as $page)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{__('levels.'.@$page->page)}}</td>
                                    <td>{{@$page->title}}</td>
                                    <td>{!!@$page->my_status!!}</td>
                                    <td>{{ dateFormat($page->updated_at) }}</td>
                                    @if(hasPermission('pages_update') == true )
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if(hasPermission('pages_update'))
                                                <a href="{{route('pages.edit',$page->id)}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ @$pages->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ @$pages->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ @$pages->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ @$pages->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ static_asset('backend/vendor/full-calendar/js/jquery-ui.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuRows = document.getElementById('menuRows');
        const addBtn = document.getElementById('addMenuItemBtn');
        const addSelect = document.getElementById('addMenuPageSelect');

        if (!menuRows) {
            return;
        }

        let draggingEl = null;

        function reindexMenuInputs() {
            const rows = menuRows.querySelectorAll('.menu-item');
            rows.forEach((row, index) => {
                const pageId = row.querySelector('input[type="hidden"]');
                const label = row.querySelector('input[placeholder="Menu label"]');
                const url = row.querySelector('input[placeholder="URL"]');
                if (pageId) pageId.name = `menu[${index}][page_id]`;
                if (label) label.name = `menu[${index}][label]`;
                if (url) url.name = `menu[${index}][url]`;
            });
        }

        function setRowDraggableState() {
            menuRows.querySelectorAll('.menu-item').forEach((row) => {
                row.setAttribute('draggable', 'true');
            });
        }

        function getDragAfterElement(container, y) {
            const elements = [...container.querySelectorAll('.menu-item:not(.dragging)')];
            let closest = { offset: Number.NEGATIVE_INFINITY, element: null };

            elements.forEach((child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    closest = { offset, element: child };
                }
            });

            return closest.element;
        }

        function initNativeDnD() {
            menuRows.addEventListener('dragstart', function (e) {
                const row = e.target.closest('.menu-item');
                if (!row) return;

                const hasHandle = e.target.closest('.drag-handle');
                if (!hasHandle) {
                    e.preventDefault();
                    return;
                }

                draggingEl = row;
                row.classList.add('dragging');
                if (e.dataTransfer) {
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', row.dataset.pageId || '');
                }
            });

            menuRows.addEventListener('dragover', function (e) {
                e.preventDefault();
                if (!draggingEl) return;

                const afterElement = getDragAfterElement(menuRows, e.clientY);
                if (afterElement == null) {
                    menuRows.appendChild(draggingEl);
                } else {
                    menuRows.insertBefore(draggingEl, afterElement);
                }
            });

            menuRows.addEventListener('drop', function (e) {
                e.preventDefault();
            });

            menuRows.addEventListener('dragend', function () {
                if (draggingEl) {
                    draggingEl.classList.remove('dragging');
                }
                draggingEl = null;
                reindexMenuInputs();
            });
        }

        function initJquerySortable() {
            if (!window.jQuery || !jQuery.fn || !jQuery.fn.sortable) {
                return false;
            }
            jQuery(menuRows).sortable({
                axis: 'y',
                handle: '.drag-handle',
                items: '.menu-item',
                tolerance: 'pointer',
                helper: 'clone',
                update: function () {
                    reindexMenuInputs();
                }
            });
            return true;
        }

        menuRows.addEventListener('click', function (e) {
            const removeBtn = e.target.closest('.remove-menu-item');
            if (!removeBtn) return;

            const row = removeBtn.closest('.menu-item');
            if (row) {
                row.remove();
                reindexMenuInputs();
            }
        });

        if (addBtn && addSelect) {
            addBtn.addEventListener('click', function () {
                const selectedOption = addSelect.options[addSelect.selectedIndex];
                const pageId = selectedOption ? selectedOption.value : '';
                const label = selectedOption ? selectedOption.getAttribute('data-label') : '';
                const url = selectedOption ? selectedOption.getAttribute('data-url') : '';

                if (!pageId) return;

                if (menuRows.querySelector(`.menu-item[data-page-id="${pageId}"]`)) {
                    return;
                }

                const row = document.createElement('div');
                row.className = 'menu-item card p-3 mb-2';
                row.setAttribute('data-page-id', pageId);
                row.setAttribute('draggable', 'true');
                row.innerHTML = `
                    <div class="d-flex align-items-center" style="gap: 12px;">
                        <span class="drag-handle text-muted" style="cursor: move;">
                            <i class="fa fa-arrows-alt"></i>
                        </span>
                        <input type="hidden" value="${pageId}">
                        <input type="text" class="form-control" value="${label || ''}" placeholder="Menu label" required>
                        <input type="text" class="form-control" value="${url || '/'}" placeholder="URL" required>
                        <button type="button" class="btn btn-link text-danger p-0 remove-menu-item" title="Remove">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                `;

                menuRows.appendChild(row);
                addSelect.value = '';
                reindexMenuInputs();
            });
        }

        const sortableReady = initJquerySortable();
        if (!sortableReady) {
            setRowDraggableState();
            initNativeDnD();
        }
        reindexMenuInputs();
    });
</script>
@endpush