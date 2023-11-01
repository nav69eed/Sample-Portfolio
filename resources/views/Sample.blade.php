@forelse ($data as $single)
    <div>
        {{  $single->content }}
    </div>
@empty
    empty
@endforelse