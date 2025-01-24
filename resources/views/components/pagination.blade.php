<div class="d-flex flex-column align-items-center gap-2">
    <div class="text-secondary small">
        Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} entries
    </div>
    @if ($paginator->hasPages())
        <nav class="mt-2" aria-label="Page navigation">
            <ul class="pagination justify-content-center mb-0">
                {{-- First Page Link --}}
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->url(1) }}" title="First Page">
                        <i class="fa fa-angle-double-left"></i>
                    </a>
                </li>

                {{-- Previous Page Link --}}
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($paginator->currentPage() - 1) }}">
                        <i class="fa fa-angle-left"></i>
                    </a>
                </li>

                {{-- First 3 Pages --}}
                @for ($i = 1; $i <= min(3, $paginator->lastPage()); $i++)
                    <li class="page-item {{ $i == $paginator->currentPage() ? 'active' : '' }}">
                        <a class="page-link text-center" href="{{ $paginator->url($i) }}" style="min-width: 45px;">{{ $i }}</a>
                    </li>
                @endfor

                @if ($paginator->lastPage() > 6)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif

                {{-- Page Input --}}
                @if ($paginator->lastPage() > 6)
                    <li class="page-item">
                        <form class="page-link border-1 border-secondary-subtle" style="width: auto; padding: 1%;" action="" method="GET">
                            @foreach (request()->except('page') as $key => $value)
                                <input name="{{ $key }}" type="hidden" value="{{ $value }}">
                            @endforeach
                            <input class="form-control form-control-sm border-0 text-center" name="page" type="text" value="{{ $paginator->currentPage() }}" style="width: 45px; height: 24px;" inputmode="numeric" pattern="[0-9]*" min="1" max="{{ $paginator->lastPage() }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" autocomplete="off">
                        </form>
                    </li>
                @endif

                @if ($paginator->lastPage() > 6)
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif

                {{-- Last 3 Pages --}}
                @for ($i = max($paginator->lastPage() - 2, min(3, $paginator->lastPage()) + 1); $i <= $paginator->lastPage(); $i++)
                    <li class="page-item {{ $i == $paginator->currentPage() ? 'active' : '' }}">
                        <a class="page-link text-center" href="{{ $paginator->url($i) }}" style="min-width: 45px;">{{ $i }}</a>
                    </li>
                @endfor

                {{-- Next Page Link --}}
                <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($paginator->currentPage() + 1) }}">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>

                {{-- Last Page Link --}}
                <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" title="Last Page">
                        <i class="fa fa-angle-double-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
    @endif
</div>
