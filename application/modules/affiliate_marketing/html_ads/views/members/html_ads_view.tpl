{% if rows.values %}
    {% for s in rows.values %}
        <div class="card panel panel-default">
            <div class="card-header">{{ s.name }}</div>
            <div class="card-body panel-body">
                <div class="jumbotron white">
                    <h5>{{ s.html_ad_title }}</h5>
                    <p> {{ s.html_ad_body }}</p>
                </div>
                <div class="text-sm-right">
                    <a class="btn btn-secondary btn-sm" data-toggle="collapse" href="#b-{{ s.id }}" aria-expanded="false"
                       aria-controls="b{{ s.id }}">{{ i('fa fa-copy') }} {{ lang('code') }}</a>
                </div>
                <div id="b-{{ s.id }}" class="row collapse">
                    <div class="col-md-10 offset-md-1">
                        <p>
                            <small class="text-muted">{{ lang('copy_paste_code') }}</small>
                            <textarea class="form-control" rows="5" onclick="this.select()">{{ s.html_ad_body }}</textarea>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    {% endfor %}
    <div class="text-sm-center">
        {{ rows.page.paginate.rows }}
    </div>
{% else %}
<div role="alert" class="alert alert-info">
    {{ lang('no_tools_found') }}
</div>
{% endif %}
