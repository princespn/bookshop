{% extends "global/meta.tpl" %}
{% block title %}{{ lang('downline')|capitalize }}{% endblock %}
{% block body %}
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h2 class="text-sm-center">{{ lang('referral_downline') }} </h2>
            </div>
            <div class="col-md-2 text-sm-right">
                <a href="javascript:history.back()"
                   class="btn btn-secondary">{{ i('fa fa-undo') }} {{ lang('go_back') }}</a>
            </div>
        </div>
        <hr/>
        <div id="downline">
            <table id="downline-table" class="table">
                <tr valign="top">
                    <td colspan="7" align="center">
                        <div class="downline-box">
                            <h3>{{ lang('you') }}</h3>
                            <i class="fa fa-user fa-5x "></i></a><br/>
                            <small>{{ sess('fname') }}</small>
                            <br/>
                            <i class="fa fa-arrow-down "></i>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <td align="center"><table class="table">
                            <tr valign="top">
                                {{ p.results }}
                            </tr>
                        </table></td>
                </tr>
            </table>
        </div>
    </div>
{% endblock body %}
{% block javascript_footer %}
    {{ parent() }}
{% endblock %}