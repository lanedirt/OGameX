<li>
    <a class="{{ Request::is('admin/hello-world*') ? 'active' : '' }}" href="{{ route('helloworld.index') }}">
        {{ __('t_helloworld.admin_nav') }}
    </a>
</li>
