<nav class="nav-wrap">
    <div class="nav-left pull-left">
        <span>上海瑞和工程咨询项目绩效管理系统</span>
    </div>
    <div class="nav-right pull-right">
        <div class="nav-item">
            <span><strong>您好！{{ Auth::user()->name }}</strong></span>
            <span id="navTime">{{ date('Y年m月d日 H:i:s') }}</span>
        </div>

        <div class="nav-item">
            <a href="{{ route('user.edit_password') }}" target="_self"><strong>修改密码</strong></a>
            <a href="{{ route('logout') }}" target="_self"><strong>退出系统</strong></a>
        </div>
    </div>
</nav>