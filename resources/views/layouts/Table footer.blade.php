  {{-- Pagination Footer --}}
                @if($leden->hasPages())
                <div class="table-footer">
                    <div class="pagination-info">
                        Toont {{ $leden->firstItem() }}–{{ $leden->lastItem() }} van {{ $leden->total() }} leden
                    </div>
                    <div class="pagination">
                        {{-- Previous Button --}}
                        @if($leden->onFirstPage())
                            <span class="page-nav disabled">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        @else
                            <a href="{{ $leden->previousPageUrl() }}" class="page-nav">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        @endif

                        @php
                            $currentPage = $leden->currentPage();
                            $lastPage = $leden->lastPage();
                        @endphp

                        {{-- Page Numbers --}}
                        @for($i = 1; $i <= min(5, $lastPage); $i++)
                            @if($i == $currentPage)
                                <span class="page-link active">{{ $i }}</span>
                            @else
                                <a href="{{ $leden->url($i) }}" class="page-link">{{ $i }}</a>
                            @endif
                        @endfor

                        @if($lastPage > 5)
                            <span class="page-dots">•••</span>
                            @if($currentPage == $lastPage)
                                <span class="page-link active">{{ $lastPage }}</span>
                            @else
                                <a href="{{ $leden->url($lastPage) }}" class="page-link">{{ $lastPage }}</a>
                            @endif
                        @endif

                        {{-- Next Button --}}
                        @if($leden->hasMorePages())
                            <a href="{{ $leden->nextPageUrl() }}" class="page-nav">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                        @else
                            <span class="page-nav disabled">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
                  @endif