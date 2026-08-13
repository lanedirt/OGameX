<li>
    <a class="{{ Request::is('admin/hello-world*') ? 'active' : '' }}" href="{{ route('helloworld.index') }}">
        HelloWorld example
    </a>
</li>
