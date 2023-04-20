<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">{{ lang('search_blog_posts') }}</h5>
        <hr />
        {{ form_open('blog/search', 'method="get" id="search-form" class="form-horizontal"') }}
        <div class="input-group">
            <input type="text" name="search_term" class="form-control" placeholder="{{ lang('search_for') }}...">
            <div class="input-group-append">
                <button class="btn btn-secondary" type="submit">{{ lang('go') }}</button>
            </div>
        </div>
        {{ form_close() }}
    </div>
</div>