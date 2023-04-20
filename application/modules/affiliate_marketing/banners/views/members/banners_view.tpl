{% if  rows.values %}
    {% for s in rows.values %}
        <div class="card panel panel-default">
            <div class="card-header">{{ s.name }}</div>
            <div class="card-body panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="text-md-center">
                            <img src="{{ s.banner_file_name }}" class="img-responsive" width="{{ s.banner_width }}" height="{{ s.banner_height }}" /></p>
                    </div>
                </div>
                <div id="b-{{ s.id }}" class="row collapse">
                    <div class="col-md-10 offset-md-1">
                        <p><small class="text-muted">{{ lang('copy_paste_code') }}</small>
                            <textarea class="form-control" onclick="this.select()">&lt;a href="{{ aff_tools_url('tools/banners/'~s.id) }}" {{ rel() }} &gt;&lt;img src="{{ s.banner_file_name }}" width="{{ s.banner_width }}" height="{{ s.banner_height }}" border="0" /&gt;&lt;/a&gt;</textarea>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-md-right">
                        <button class="btn btn-sm btn-secondary" data-toggle="collapse" href="#b-{{ s.id }}" aria-expanded="false" aria-controls="b{{ s.id }}">{{ i('fa fa-copy') }} {{ lang('get_code') }}</button>
                    </div>
                </div>
            </div>
        </div>
    {% endfor %}
    <div class="text-md-center">
        {{ rows.page.paginate.rows }}
    </div>
{% else %}
<div role="alert" class="alert alert-info">
    {{ lang('no_tools_found') }}
</div>
{% endif %}
