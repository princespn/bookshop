{% if paginate.rows %}
    <div id="site_pagination">
        <ul class="text-capitalize pagination justify-content-center">
        {{ paginate.rows }}
        </ul>
    </div>
{% endif %}