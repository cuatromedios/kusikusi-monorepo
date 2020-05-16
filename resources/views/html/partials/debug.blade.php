@if (env('APP_ENV') === 'local' && env('APP_DEBUG') === true)
    <div style="position: fixed; bottom: 0; left: 0; right: 0; max-height: 50vh; overflow: scroll; text-align: right">
        <div id="kusikusi_debug_header" style="">
            <a href="javascript:;" onclick="var kdv = document.getElementById('kusikusi_debug_vars'); kdv.style.display = kdv.style.display === 'none' ? 'block' : 'none';" style="color: white; margin: 0; padding: 0.5em; font-size: .75em; background-color: #0c5460; color: white; text-align: right; display: inline-block; border-top-left-radius: 8px">Vars</a>
        </div>
        <div id="kusikusi_debug_vars" style="margin: 0; margin-top: -1em; padding: 0; text-align: left; display: none">
            @php
                dump(get_defined_vars()['__data']);
            @endphp
        </div>
    </div>
@endif
