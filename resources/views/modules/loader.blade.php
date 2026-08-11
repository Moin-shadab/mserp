@php
    $pageDirDot = str_replace('/', '.', $pageDir);
    $cssView = $pageDirDot . '.css';
    $mainView = $pageDirDot . '.main';
    $indexView = $pageDirDot . '.index';
    $jsView = $pageDirDot . '.js';
@endphp

@if(view()->exists($cssView))
    <style>
        @include($cssView)
    </style>
@endif

@if(view()->exists($pageDirDot))
    @include($pageDirDot)
@elseif(view()->exists($mainView))
    @include($mainView)
@elseif(view()->exists($indexView))
    @include($indexView)
@endif

@if(view()->exists($jsView))
    <script>
        (function() {
            @include($jsView)
        })();
    </script>
@endif
