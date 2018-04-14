<div class="sidebar-wrap">
    <div class="sidebar-title">
        <h1>菜单</h1>
    </div>
    <ul class="sidebar-list">
        @foreach($side_menu as $parent)
            <li class="sidebar-item">
                <a class="openPopover"><i class="fa {{ $parent['icon'] }}"></i>{{ $parent['name'] }}</a>
                <ul class="sub-menu {{ set_hidden($parent['path']) }}">
                    @foreach($parent['list'] as $child)
                        <li class="{{ navViewActive($child['route']) }}"><a href="{{ route($child['route']) }}"><i class="fa {{ $child['icon'] }}"></i>
                                {{ $child['name'] }}@if(isset($child['count']))({{ $child['count'] }})@endif
                            </a></li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</div>