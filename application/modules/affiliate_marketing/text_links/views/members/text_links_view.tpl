{% if rows.values %}
    {% for s in rows.values %}
        <div class="card panel panel-default">
            <div class="card-header">{{ s.name }}</div>
            <div class="card-body panel-body">
                    <h5>{{ s.text_link_title }}</h5>
                    <p> {{ s.description }}</p>
                <div class="text-sm-right">
                    <a class="btn btn-secondary btn-sm" data-toggle="collapse" href="#b-{{ s.id }}" aria-expanded="false"
                       aria-controls="b{{ s.id }}">{{ i('fa fa-copy') }} {{ lang('code') }}</a>
                </div>
                <div id="b-{{ s.id }}" class="row collapse">
                    <div class="col-md-10 offset-md-1">
                        <p><small class="text-muted">{{ lang('copy_paste_code') }}</small>
                            <textarea class="form-control" onclick="this.select()">&lt;a href="{{ aff_tools_url('tools/text_links/'~s.id) }}" {{ rel() }}&gt;{{ s.text_link_title }}&lt;/a&gt;</textarea>
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
